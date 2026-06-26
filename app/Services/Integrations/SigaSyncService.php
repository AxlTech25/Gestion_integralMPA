<?php

namespace App\Services\Integrations;

use App\Contracts\Integrations\SigaClientInterface;
use App\Models\Equipo;
use App\Models\PersonalSigaReferencia;
use App\Models\SyncLog;
use App\Models\SyncLogDetalle;
use App\Models\UnidadOrganizacional;
use App\Models\Usuario;

class SigaSyncService
{
    public function __construct(private SigaClientInterface $client) {}

    /** @return array<string, mixed> */
    public function syncPatrimonio(?Usuario $ejecutor = null, string $modo = 'automatico'): array
    {
        $log = $this->iniciarLog('siga', 'patrimonio', $modo, $ejecutor);
        $ok = 0;
        $errores = 0;
        $omitidos = 0;
        $registradoPor = $ejecutor?->id ?? Usuario::query()->orderBy('id')->value('id');

        try {
            $items = $this->client->getPatrimonioInformatica();

            foreach ($items as $item) {
                if (! $this->esInformaticaMunicipal($item)) {
                    $omitidos++;
                    $this->detalle($log, 'patrimonio', $item['codigo_siga'] ?? null, null, null, 'omitido', 'Categoría no informática municipal');

                    continue;
                }

                try {
                    $unidad = $this->resolverUnidad($item);
                    if (! $unidad) {
                        $errores++;
                        $this->detalle($log, 'patrimonio', $item['codigo_siga'] ?? null, null, null, 'error', 'Unidad no encontrada');

                        continue;
                    }

                    $equipo = Equipo::query()
                        ->where(function ($q) use ($item) {
                            $q->where('codigo_siga', $item['codigo_siga']);
                            if (! empty($item['codigo_patrimonial'])) {
                                $q->orWhere('codigo_patrimonial', $item['codigo_patrimonial']);
                            }
                        })
                        ->first();

                    $payload = [
                        'codigo_siga' => $item['codigo_siga'],
                        'codigo_patrimonial' => $item['codigo_patrimonial'] ?? $item['codigo_siga'],
                        'tipo_equipo' => $item['tipo_equipo'] ?? 'otro',
                        'marca' => $item['marca'] ?? 'N/D',
                        'modelo' => $item['modelo'] ?? 'N/D',
                        'numero_serie' => $item['numero_serie'] ?? null,
                        'estado_operativo' => $item['estado_operativo'] ?? 'operativo',
                        'unidad_id' => $unidad->id,
                        'custodio_nombre' => $item['custodio_nombre'] ?? null,
                        'custodio_cargo' => $item['custodio_cargo'] ?? null,
                        'valor_patrimonial' => $item['valor_patrimonial'] ?? null,
                        'fecha_adquisicion' => $item['fecha_adquisicion'] ?? null,
                        'registrado_por' => $registradoPor,
                    ];

                    if ($equipo) {
                        $equipo->update($payload);
                        $accion = 'actualizado';
                    } else {
                        $equipo = Equipo::create($payload);
                        $accion = 'insertado';
                    }

                    $ok++;
                    $this->detalle($log, 'patrimonio', $item['codigo_siga'], 'equipos', $equipo->id, 'ok', $accion);
                } catch (\Throwable $e) {
                    $errores++;
                    $this->detalle($log, 'patrimonio', $item['codigo_siga'] ?? null, null, null, 'error', $e->getMessage());
                }
            }

            $estado = $errores > 0 ? ($ok > 0 ? 'parcial' : 'error') : 'ok';
            $this->cerrarLog($log, $estado, $ok, $errores, sprintf(
                'Patrimonio SIGA — insertados/actualizados: %d, omitidos: %d, errores: %d',
                $ok,
                $omitidos,
                $errores
            ));

            return $this->resultado($log, $ok, $errores, $omitidos);
        } catch (\Throwable $e) {
            $this->cerrarLog($log, 'error', $ok, $errores + 1, $e->getMessage());

            throw $e;
        }
    }

    /** @return array<string, mixed> */
    public function syncOrganigrama(?Usuario $ejecutor = null, string $modo = 'automatico'): array
    {
        $log = $this->iniciarLog('siga', 'organigrama', $modo, $ejecutor);
        $ok = 0;
        $errores = 0;
        $omitidos = 0;
        $mapaSiga = [];

        try {
            $unidades = $this->client->getOrganigrama();

            foreach ($unidades as $item) {
                if (($item['tipo'] ?? '') === 'comite') {
                    $omitidos++;
                    $this->detalle($log, 'unidad', $item['codigo_siga'] ?? null, null, null, 'omitido', 'Comité excluido de derivación');

                    continue;
                }

                try {
                    $unidad = $this->upsertUnidad($item);
                    $mapaSiga[$item['codigo_siga']] = $unidad->id;
                    $ok++;
                    $this->detalle($log, 'unidad', $item['codigo_siga'], 'unidades_organizacionales', $unidad->id, 'ok', 'sincronizado');
                } catch (\Throwable $e) {
                    $errores++;
                    $this->detalle($log, 'unidad', $item['codigo_siga'] ?? null, null, null, 'error', $e->getMessage());
                }
            }

            foreach ($unidades as $item) {
                if (($item['tipo'] ?? '') === 'comite' || empty($item['codigo_siga_padre'])) {
                    continue;
                }

                $unidadId = $mapaSiga[$item['codigo_siga']] ?? null;
                $padreId = $mapaSiga[$item['codigo_siga_padre']] ?? null;

                if ($unidadId && $padreId) {
                    UnidadOrganizacional::whereKey($unidadId)->update([
                        'padre_id' => $padreId,
                        'gerencia_id' => $this->resolverGerenciaId($padreId),
                    ]);
                }
            }

            $estado = $errores > 0 ? ($ok > 0 ? 'parcial' : 'error') : 'ok';
            $this->cerrarLog($log, $estado, $ok, $errores, sprintf(
                'Organigrama SIGA — unidades: %d, omitidos: %d, errores: %d',
                $ok,
                $omitidos,
                $errores
            ));

            return $this->resultado($log, $ok, $errores, $omitidos);
        } catch (\Throwable $e) {
            $this->cerrarLog($log, 'error', $ok, $errores + 1, $e->getMessage());

            throw $e;
        }
    }

    /** @return array<string, mixed> */
    public function syncPersonal(?Usuario $ejecutor = null, string $modo = 'automatico'): array
    {
        $log = $this->iniciarLog('siga', 'personal', $modo, $ejecutor);
        $ok = 0;
        $errores = 0;
        $sugerencias = 0;

        try {
            foreach ($this->client->getPersonal() as $item) {
                try {
                    $unidad = $this->resolverUnidad($item);
                    $activo = (bool) ($item['activo'] ?? true);

                    $referencia = PersonalSigaReferencia::updateOrCreate(
                        ['codigo_siga' => $item['codigo_siga']],
                        [
                            'dni' => $item['dni'] ?? null,
                            'nombre_completo' => $item['nombre_completo'] ?? 'Sin nombre',
                            'unidad_id' => $unidad?->id,
                            'activo_siga' => $activo,
                            'desactivacion_sugerida' => ! $activo,
                            'sincronizado_at' => now(),
                        ]
                    );

                    if (! $activo && $referencia->usuario_id) {
                        $sugerencias++;
                    }

                    $ok++;
                    $this->detalle(
                        $log,
                        'personal',
                        $item['codigo_siga'],
                        'personal_siga_referencias',
                        $referencia->id,
                        'ok',
                        $activo ? 'activo' : 'inactivo — revisar usuario'
                    );
                } catch (\Throwable $e) {
                    $errores++;
                    $this->detalle($log, 'personal', $item['codigo_siga'] ?? null, null, null, 'error', $e->getMessage());
                }
            }

            $estado = $errores > 0 ? ($ok > 0 ? 'parcial' : 'error') : 'ok';
            $this->cerrarLog($log, $estado, $ok, $errores, sprintf(
                'Personal SIGA — registros: %d, sugerencias desactivación: %d, errores: %d',
                $ok,
                $sugerencias,
                $errores
            ));

            return $this->resultado($log, $ok, $errores, 0, ['sugerencias_desactivacion' => $sugerencias]);
        } catch (\Throwable $e) {
            $this->cerrarLog($log, 'error', $ok, $errores + 1, $e->getMessage());

            throw $e;
        }
    }

    public function esSimulacion(): bool
    {
        return $this->client->esSimulacion();
    }

    /** @param array<string, mixed> $item */
    private function esInformaticaMunicipal(array $item): bool
    {
        $categoria = $item['categoria'] ?? 'informatica_municipal';

        return in_array($categoria, ['informatica_municipal', 'informatica', 'ti'], true);
    }

    /** @param array<string, mixed> $item */
    private function resolverUnidad(array $item): ?UnidadOrganizacional
    {
        if (! empty($item['codigo_org'])) {
            $unidad = UnidadOrganizacional::where('codigo_org', $item['codigo_org'])->first();
            if ($unidad) {
                if (! empty($item['unidad_codigo_siga']) || ! empty($item['codigo_siga_unidad'])) {
                    $unidad->update(['codigo_siga' => $item['unidad_codigo_siga'] ?? $item['codigo_siga_unidad']]);
                }

                return $unidad;
            }
        }

        $codigoSiga = $item['unidad_codigo_siga'] ?? $item['codigo_siga_unidad'] ?? null;
        if ($codigoSiga) {
            return UnidadOrganizacional::where('codigo_siga', $codigoSiga)->first();
        }

        return null;
    }

    /** @param array<string, mixed> $item */
    private function upsertUnidad(array $item): UnidadOrganizacional
    {
        $query = UnidadOrganizacional::query();

        if (! empty($item['codigo_org'])) {
            $unidad = (clone $query)->where('codigo_org', $item['codigo_org'])->first();
            if ($unidad) {
                $unidad->update([
                    'codigo_siga' => $item['codigo_siga'],
                    'nombre' => $item['nombre'],
                    'activa' => (bool) ($item['activa'] ?? true),
                    'permite_derivacion' => (bool) ($item['permite_derivacion'] ?? false),
                ]);

                return $unidad;
            }
        }

        $unidad = (clone $query)->where('codigo_siga', $item['codigo_siga'])->first();

        if ($unidad) {
            $unidad->update([
                'nombre' => $item['nombre'],
                'tipo' => $item['tipo'] ?? 'unidad',
                'activa' => (bool) ($item['activa'] ?? true),
                'permite_derivacion' => (bool) ($item['permite_derivacion'] ?? false),
            ]);

            return $unidad;
        }

        return UnidadOrganizacional::create([
            'codigo_org' => $item['codigo_org'] ?? 'SIGA-'.substr($item['codigo_siga'], -8),
            'codigo_siga' => $item['codigo_siga'],
            'nombre' => $item['nombre'],
            'tipo' => $item['tipo'] ?? 'unidad',
            'permite_derivacion' => (bool) ($item['permite_derivacion'] ?? false),
            'activa' => (bool) ($item['activa'] ?? true),
        ]);
    }

    private function resolverGerenciaId(int $padreId): ?int
    {
        $padre = UnidadOrganizacional::find($padreId);
        if (! $padre) {
            return null;
        }

        if ($padre->tipo === 'gerencia') {
            return $padre->id;
        }

        return $padre->gerencia_id ?? $padre->padre_id;
    }

    private function iniciarLog(string $sistema, string $tipo, string $modo, ?Usuario $ejecutor): SyncLog
    {
        return SyncLog::create([
            'sistema' => $sistema,
            'tipo_sync' => $tipo,
            'modo' => $modo,
            'estado' => 'ok',
            'registros_ok' => 0,
            'registros_error' => 0,
            'ejecutado_por' => $ejecutor?->id,
            'ejecutado_at' => now(),
        ]);
    }

    private function cerrarLog(SyncLog $log, string $estado, int $ok, int $errores, string $mensaje): void
    {
        $log->update([
            'estado' => $estado,
            'registros_ok' => $ok,
            'registros_error' => $errores,
            'mensaje' => $mensaje,
        ]);
    }

    private function detalle(
        SyncLog $log,
        string $entidadExterna,
        ?string $referencia,
        ?string $entidadLocal,
        ?int $entidadLocalId,
        string $estado,
        string $mensaje
    ): void {
        SyncLogDetalle::create([
            'sync_log_id' => $log->id,
            'entidad_externa' => $entidadExterna,
            'referencia' => $referencia,
            'entidad_local' => $entidadLocal,
            'entidad_local_id' => $entidadLocalId,
            'estado' => $estado,
            'mensaje' => $mensaje,
            'created_at' => now(),
        ]);
    }

    /** @param array<string, mixed> $extra */
    private function resultado(SyncLog $log, int $ok, int $errores, int $omitidos = 0, array $extra = []): array
    {
        return array_merge([
            'sync_log_id' => $log->id,
            'estado' => $log->fresh()->estado,
            'registros_ok' => $ok,
            'registros_error' => $errores,
            'omitidos' => $omitidos,
            'mensaje' => $log->fresh()->mensaje,
            'es_simulacion' => $this->esSimulacion(),
        ], $extra);
    }
}
