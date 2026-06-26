<?php

namespace App\Services\Documentaria;

use App\Models\Documento;
use App\Models\Expediente;
use App\Models\ExpedienteAdjunto;
use App\Models\ExpedienteMovimiento;
use App\Models\NumeracionExpediente;
use App\Models\TipoDocumental;
use App\Models\UnidadOrganizacional;
use App\Models\Usuario;
use App\Services\Core\AuditoriaService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ExpedienteService
{
    public function __construct(
        private AuditoriaService $auditoria,
        private ConstanciaService $constanciaService,
    ) {}

    public function registrar(Usuario $usuario, array $data, ?UploadedFile $archivo = null): Expediente
    {
        return DB::transaction(function () use ($usuario, $data, $archivo) {
            $tipo = TipoDocumental::where('activo', true)->findOrFail($data['tipo_documental_id']);
            $this->validarTipoParaUsuario($usuario, $tipo);

            $unidadOrigenId = $tipo->unidad_emisora_id ?? $usuario->unidad_activa_id;
            $anio = (int) ($data['anio'] ?? now()->year);

            $codigoData = $this->siguienteCodigo($tipo, $anio);

            $expediente = Expediente::create([
                'tipo_documental_id' => $tipo->id,
                'anio' => $anio,
                'secuencial' => $codigoData['secuencial'],
                'codigo' => $codigoData['codigo'],
                'asunto' => $data['asunto'],
                'prioridad' => $data['prioridad'] ?? 'media',
                'unidad_origen_id' => $unidadOrigenId,
                'unidad_actual_id' => $unidadOrigenId,
                'estado' => 'registrado',
                'registrado_por' => $usuario->id,
            ]);

            ExpedienteMovimiento::create([
                'expediente_id' => $expediente->id,
                'tipo_movimiento' => 'registro',
                'unidad_origen_id' => $unidadOrigenId,
                'unidad_destino_id' => $unidadOrigenId,
                'unidad_actuante_id' => $usuario->unidad_activa_id,
                'usuario_id' => $usuario->id,
                'proveido' => $data['proveido'] ?? null,
                'created_at' => now(),
            ]);

            if ($archivo) {
                $this->guardarAdjunto($expediente, $archivo, $usuario);
            }

            $expediente->update(['estado' => 'en_tramite']);

            $this->auditoria->registrar('MOD-DOC', 'registrar', 'expediente', $expediente->id, [
                'codigo' => $expediente->codigo,
            ], $usuario);

            return $this->cargarRelaciones($expediente);
        });
    }

    public function derivar(Expediente $expediente, Usuario $usuario, int $unidadDestinoId, ?string $proveido = null): Expediente
    {
        return DB::transaction(function () use ($expediente, $usuario, $unidadDestinoId, $proveido) {
            $expediente->load(['tipoDocumental', 'documentoPrincipal']);

            $this->validarEnUnidad($expediente, $usuario);
            $this->validarEstadoOperable($expediente);
            $this->validarFirmaAntesDerivar($expediente);
            $this->validarUnidadDestinoDerivacion($unidadDestinoId);

            $origenId = $expediente->unidad_actual_id;

            $movimiento = ExpedienteMovimiento::create([
                'expediente_id' => $expediente->id,
                'tipo_movimiento' => 'derivacion',
                'unidad_origen_id' => $origenId,
                'unidad_destino_id' => $unidadDestinoId,
                'unidad_actuante_id' => $usuario->unidad_activa_id,
                'usuario_id' => $usuario->id,
                'proveido' => $proveido,
                'created_at' => now(),
            ]);

            $this->constanciaService->registrarParaMovimiento(
                $movimiento,
                $usuario,
                'proveido_salida',
                $expediente->codigo,
            );

            $expediente->update([
                'unidad_actual_id' => $unidadDestinoId,
                'estado' => $expediente->tipoDocumental->requiere_recepcion ? 'por_recepcionar' : 'en_tramite',
            ]);

            $this->auditoria->registrar('MOD-DOC', 'derivar', 'expediente', $expediente->id, [
                'destino_id' => $unidadDestinoId,
            ], $usuario);

            return $this->cargarRelaciones($expediente->fresh());
        });
    }

    public function recepcionar(Expediente $expediente, Usuario $usuario): Expediente
    {
        return DB::transaction(function () use ($expediente, $usuario) {
            if ($expediente->unidad_actual_id !== $usuario->unidad_activa_id) {
                throw ValidationException::withMessages([
                    'expediente' => ['El expediente no está en su unidad.'],
                ]);
            }

            if ($expediente->estado !== 'por_recepcionar') {
                throw ValidationException::withMessages([
                    'expediente' => ['El expediente no está pendiente de recepción.'],
                ]);
            }

            $movimiento = ExpedienteMovimiento::create([
                'expediente_id' => $expediente->id,
                'tipo_movimiento' => 'recepcion',
                'unidad_origen_id' => $expediente->unidad_actual_id,
                'unidad_destino_id' => $expediente->unidad_actual_id,
                'unidad_actuante_id' => $usuario->unidad_activa_id,
                'usuario_id' => $usuario->id,
                'created_at' => now(),
            ]);

            $this->constanciaService->registrarParaMovimiento(
                $movimiento,
                $usuario,
                'recepcion',
                $expediente->codigo,
            );

            $expediente->update(['estado' => 'en_tramite']);

            $this->auditoria->registrar('MOD-DOC', 'recepcionar', 'expediente', $expediente->id, null, $usuario);

            return $this->cargarRelaciones($expediente->fresh());
        });
    }

    public function devolver(Expediente $expediente, Usuario $usuario, string $observacion): Expediente
    {
        return DB::transaction(function () use ($expediente, $usuario, $observacion) {
            $this->validarEnUnidad($expediente, $usuario);

            $ultimaDerivacion = ExpedienteMovimiento::where('expediente_id', $expediente->id)
                ->where('tipo_movimiento', 'derivacion')
                ->where('unidad_destino_id', $expediente->unidad_actual_id)
                ->orderByDesc('created_at')
                ->first();

            if (! $ultimaDerivacion) {
                throw ValidationException::withMessages([
                    'observacion' => ['No existe derivación previa hacia su unidad para devolver.'],
                ]);
            }

            $destinoId = $ultimaDerivacion->unidad_origen_id;

            $movimiento = ExpedienteMovimiento::create([
                'expediente_id' => $expediente->id,
                'tipo_movimiento' => 'devolucion',
                'unidad_origen_id' => $expediente->unidad_actual_id,
                'unidad_destino_id' => $destinoId,
                'unidad_actuante_id' => $usuario->unidad_activa_id,
                'usuario_id' => $usuario->id,
                'observacion' => $observacion,
                'created_at' => now(),
            ]);

            $this->constanciaService->registrarParaMovimiento(
                $movimiento,
                $usuario,
                'devolucion',
                $expediente->codigo,
            );

            $expediente->update([
                'unidad_actual_id' => $destinoId,
                'estado' => 'devuelto',
            ]);

            $this->auditoria->registrar('MOD-DOC', 'devolver', 'expediente', $expediente->id, [
                'observacion' => $observacion,
            ], $usuario);

            return $this->cargarRelaciones($expediente->fresh());
        });
    }

    public function archivar(Expediente $expediente, Usuario $usuario): Expediente
    {
        return DB::transaction(function () use ($expediente, $usuario) {
            $this->validarEnUnidad($expediente, $usuario);

            if ($expediente->estado === 'archivado') {
                throw ValidationException::withMessages([
                    'expediente' => ['El expediente ya está archivado.'],
                ]);
            }

            if ($expediente->estado === 'por_recepcionar') {
                throw ValidationException::withMessages([
                    'expediente' => ['Recepcione el expediente antes de archivarlo.'],
                ]);
            }

            if (! in_array($expediente->estado, ['en_tramite', 'devuelto', 'registrado'], true)) {
                throw ValidationException::withMessages([
                    'expediente' => ['El expediente no puede archivarse en su estado actual.'],
                ]);
            }

            $expediente->update([
                'estado' => 'archivado',
                'archivado_por' => $usuario->id,
                'archivado_at' => now(),
            ]);

            $this->auditoria->registrar('MOD-DOC', 'archivar', 'expediente', $expediente->id, [
                'codigo' => $expediente->codigo,
            ], $usuario);

            return $this->cargarRelaciones($expediente->fresh());
        });
    }

    /** @return array{secuencial: int, codigo: string} */
    private function siguienteCodigo(TipoDocumental $tipo, int $anio): array
    {
        $numeracion = NumeracionExpediente::where('tipo_documental_id', $tipo->id)
            ->where('anio', $anio)
            ->lockForUpdate()
            ->first();

        if (! $numeracion) {
            $numeracion = NumeracionExpediente::create([
                'tipo_documental_id' => $tipo->id,
                'anio' => $anio,
                'ultimo_secuencial' => 0,
            ]);
            $numeracion = NumeracionExpediente::where('id', $numeracion->id)->lockForUpdate()->first();
        }

        $secuencial = $numeracion->ultimo_secuencial + 1;
        $numeracion->update(['ultimo_secuencial' => $secuencial]);

        $codigo = str_replace(
            ['{prefijo}', '{anio}', '{secuencial}'],
            [$tipo->prefijo_numeracion, $anio, str_pad((string) $secuencial, 4, '0', STR_PAD_LEFT)],
            $tipo->formato_display
        );

        return ['secuencial' => $secuencial, 'codigo' => $codigo];
    }

    private function validarTipoParaUsuario(Usuario $usuario, TipoDocumental $tipo): void
    {
        if ($usuario->hasPermiso('doc.tipos.gestionar') || $usuario->hasPermiso('core.usuarios.gestionar')) {
            return;
        }

        if ($tipo->registro_por_secretaria && $usuario->hasPermiso('doc.expediente.registrar')) {
            return;
        }

        $unidadId = $usuario->unidad_activa_id;

        if ($tipo->unidad_emisora_id === null && $usuario->hasPermiso('doc.expediente.registrar')) {
            return;
        }

        if ($tipo->unidad_emisora_id === $unidadId) {
            return;
        }

        $permitidas = $tipo->unidadesRegistro()->pluck('unidades_organizacionales.id');

        if ($permitidas->contains($unidadId)) {
            return;
        }

        throw ValidationException::withMessages([
            'tipo_documental_id' => ['No puede registrar expedientes de este tipo documental.'],
        ]);
    }

    private function validarEnUnidad(Expediente $expediente, Usuario $usuario): void
    {
        if ($expediente->unidad_actual_id !== $usuario->unidad_activa_id) {
            throw ValidationException::withMessages([
                'expediente' => ['El expediente no está en su bandeja actual.'],
            ]);
        }
    }

    private function validarEstadoOperable(Expediente $expediente): void
    {
        if (! in_array($expediente->estado, ['en_tramite', 'devuelto', 'registrado'], true)) {
            throw ValidationException::withMessages([
                'expediente' => ['El expediente no puede derivarse en su estado actual.'],
            ]);
        }

        if ($expediente->estado === 'por_recepcionar') {
            throw ValidationException::withMessages([
                'expediente' => ['Debe recepcionar el expediente antes de derivarlo.'],
            ]);
        }
    }

    private function guardarAdjunto(Expediente $expediente, UploadedFile $archivo, Usuario $usuario): void
    {
        $extension = strtolower($archivo->getClientOriginalExtension());
        $prohibidas = ['exe', 'bat', 'cmd', 'com', 'msi', 'scr', 'ps1', 'vbs', 'js', 'sh', 'php'];

        if (in_array($extension, $prohibidas, true)) {
            throw ValidationException::withMessages([
                'archivo' => ['Tipo de archivo no permitido. Use PDF o imágenes (JPG, PNG, WEBP).'],
            ]);
        }

        $path = $archivo->store("expedientes/{$expediente->id}", 'local');
        $fullPath = Storage::disk('local')->path($path);
        $hash = is_file($fullPath) ? hash_file('sha256', $fullPath) : null;

        ExpedienteAdjunto::create([
            'expediente_id' => $expediente->id,
            'nombre_archivo' => $archivo->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $archivo->getMimeType() ?? 'application/octet-stream',
            'tamano_bytes' => $archivo->getSize(),
            'hash_archivo' => $hash,
            'subido_por' => $usuario->id,
            'created_at' => now(),
        ]);

        $this->crearDocumentoPrincipal($expediente, $path, $archivo->getClientOriginalName(), $usuario, $hash);
    }

    private function crearDocumentoPrincipal(
        Expediente $expediente,
        string $path,
        string $nombre,
        Usuario $usuario,
        ?string $hash,
    ): void {
        if ($expediente->documento_principal_id) {
            return;
        }

        $documento = Documento::create([
            'expediente_id' => $expediente->id,
            'version' => 1,
            'titulo' => $nombre,
            'es_principal' => true,
            'archivo_path' => $path,
            'hash_contenido' => $hash,
            'estado' => 'borrador',
            'creado_por' => $usuario->id,
        ]);

        $expediente->update(['documento_principal_id' => $documento->id]);
    }

    private function validarFirmaAntesDerivar(Expediente $expediente): void
    {
        if (! $expediente->tipoDocumental->requiere_firma_antes_derivar) {
            return;
        }

        $principal = $expediente->documentoPrincipal;

        if (! $principal || $principal->estado !== 'firmado') {
            throw ValidationException::withMessages([
                'expediente' => ['El documento principal debe estar firmado y sellado antes de derivar.'],
            ]);
        }
    }

    private function validarUnidadDestinoDerivacion(int $unidadDestinoId): void
    {
        $valida = UnidadOrganizacional::destinoDerivacion()
            ->where('id', $unidadDestinoId)
            ->exists();

        if (! $valida) {
            throw ValidationException::withMessages([
                'unidad_destino_id' => ['La unidad destino no está activa o no permite derivación documentaria.'],
            ]);
        }
    }

    public function cargarRelaciones(Expediente $expediente): Expediente
    {
        return $expediente->load([
            'tipoDocumental',
            'unidadOrigen',
            'unidadActual',
            'adjuntos',
            'documentoPrincipal.firma',
            'documentoPrincipal.sello',
            'movimientos.unidadOrigen',
            'movimientos.unidadDestino',
            'movimientos.unidadActuante',
            'movimientos.usuario',
            'movimientos.constancia',
        ]);
    }

    public function antiguedadDias(Expediente $expediente): int
    {
        return (int) $expediente->created_at->diffInDays(now());
    }
}
