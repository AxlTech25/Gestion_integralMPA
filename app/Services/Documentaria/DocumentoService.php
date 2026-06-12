<?php

namespace App\Services\Documentaria;

use App\Models\Documento;
use App\Models\DocumentoFirma;
use App\Models\DocumentoSello;
use App\Models\Usuario;
use App\Services\Core\AuditoriaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DocumentoService
{
    public function __construct(
        private FirmaService $firmaService,
        private SelloService $selloService,
        private AuditoriaService $auditoria,
    ) {}

    public function firmar(Documento $documento, Usuario $usuario): Documento
    {
        return DB::transaction(function () use ($documento, $usuario) {
            $documento->load('expediente');
            $expediente = $documento->expediente;

            if ($expediente->unidad_actual_id !== $usuario->unidad_activa_id) {
                throw ValidationException::withMessages([
                    'documento' => ['El expediente no está en su unidad actual.'],
                ]);
            }

            if ($documento->estado === 'firmado') {
                throw ValidationException::withMessages([
                    'documento' => ['El documento ya fue firmado.'],
                ]);
            }

            if (! $documento->archivo_path) {
                throw ValidationException::withMessages([
                    'documento' => ['No hay archivo asociado al documento.'],
                ]);
            }

            $firma = $this->firmaService->firmarDocumento($documento, $usuario);
            DocumentoFirma::create([
                'documento_id' => $documento->id,
                'usuario_id' => $usuario->id,
                'unidad_id' => $usuario->unidad_activa_id,
                'firma_hash' => $firma['firma_hash'],
                'firma_metadata' => $firma['firma_metadata'],
                'firmado_at' => now(),
            ]);

            $sello = $this->selloService->resolverParaUnidad($usuario->unidad_activa_id);
            $pdfSellado = $this->selloService->aplicarSelloDocumento($documento, $sello, $expediente->codigo);

            DocumentoSello::create([
                'documento_id' => $documento->id,
                'sello_institucional_id' => $sello?->id,
                'sello_imagen_path' => $sello?->imagen_path ?? 'sellos/sello_mpa.jpg',
                'sello_metadata' => [
                    'codigo_expediente' => $expediente->codigo,
                    'pdf_sellado' => $pdfSellado !== null,
                    'sello_texto' => $this->selloService->textoSello($sello, $expediente->codigo),
                ],
                'aplicado_at' => now(),
            ]);

            $documento->update([
                'estado' => 'firmado',
                'archivo_path' => $pdfSellado ?? $documento->archivo_path,
                'hash_contenido' => $pdfSellado
                    ? hash_file('sha256', storage_path('app/private/'.$pdfSellado))
                    : $documento->hash_contenido,
            ]);

            $this->auditoria->registrar('MOD-DOC', 'firmar', 'documento', $documento->id, [
                'expediente_codigo' => $expediente->codigo,
            ], $usuario);

            return $documento->fresh();
        });
    }
}
