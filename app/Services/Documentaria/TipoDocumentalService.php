<?php

namespace App\Services\Documentaria;

use App\Models\TipoDocumental;
use App\Models\Usuario;
use App\Services\Core\AuditoriaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TipoDocumentalService
{
    public function __construct(private AuditoriaService $auditoria) {}

    public function crear(array $data, Usuario $usuario): TipoDocumental
    {
        return DB::transaction(function () use ($data, $usuario) {
            if (TipoDocumental::where('codigo', $data['codigo'])->exists()) {
                throw ValidationException::withMessages([
                    'codigo' => ['El código de tipo documental ya existe.'],
                ]);
            }

            $tipo = TipoDocumental::create([
                'codigo' => strtoupper($data['codigo']),
                'nombre' => $data['nombre'],
                'prefijo_numeracion' => strtoupper($data['prefijo_numeracion']),
                'formato_display' => $data['formato_display'] ?? '{prefijo}-{anio}-{secuencial}',
                'clase_norma' => $data['clase_norma'] ?? 'otro',
                'ambito_emision' => $data['ambito_emision'] ?? 'unidad',
                'unidad_emisora_id' => $data['unidad_emisora_id'] ?? null,
                'registro_por_secretaria' => $data['registro_por_secretaria'] ?? false,
                'requiere_firma_antes_derivar' => $data['requiere_firma_antes_derivar'] ?? false,
                'requiere_recepcion' => $data['requiere_recepcion'] ?? true,
                'activo' => $data['activo'] ?? true,
            ]);

            $this->syncUnidadesRegistro($tipo, $data['unidades_registro_ids'] ?? []);

            $this->auditoria->registrar('MOD-DOC', 'crear', 'tipo_documental', $tipo->id, [
                'codigo' => $tipo->codigo,
            ], $usuario);

            return $tipo->load('unidadEmisora');
        });
    }

    public function actualizar(TipoDocumental $tipo, array $data, Usuario $usuario): TipoDocumental
    {
        return DB::transaction(function () use ($tipo, $data, $usuario) {
            $tipo->fill([
                'nombre' => $data['nombre'] ?? $tipo->nombre,
                'prefijo_numeracion' => isset($data['prefijo_numeracion'])
                    ? strtoupper($data['prefijo_numeracion'])
                    : $tipo->prefijo_numeracion,
                'clase_norma' => $data['clase_norma'] ?? $tipo->clase_norma,
                'ambito_emision' => $data['ambito_emision'] ?? $tipo->ambito_emision,
                'unidad_emisora_id' => $data['unidad_emisora_id'] ?? $tipo->unidad_emisora_id,
                'registro_por_secretaria' => $data['registro_por_secretaria'] ?? $tipo->registro_por_secretaria,
                'requiere_firma_antes_derivar' => $data['requiere_firma_antes_derivar'] ?? $tipo->requiere_firma_antes_derivar,
                'requiere_recepcion' => $data['requiere_recepcion'] ?? $tipo->requiere_recepcion,
                'activo' => $data['activo'] ?? $tipo->activo,
            ]);
            $tipo->save();

            if (isset($data['unidades_registro_ids'])) {
                $this->syncUnidadesRegistro($tipo, $data['unidades_registro_ids']);
            }

            $this->auditoria->registrar('MOD-DOC', 'actualizar', 'tipo_documental', $tipo->id, [
                'codigo' => $tipo->codigo,
            ], $usuario);

            return $tipo->load(['unidadEmisora', 'unidadesRegistro']);
        });
    }

    private function syncUnidadesRegistro(TipoDocumental $tipo, array $unidadIds): void
    {
        $tipo->unidadesRegistro()->sync($unidadIds);
    }
}
