<?php

namespace App\Services\Integrations;

use App\Contracts\Integrations\SiafClientInterface;
use App\Models\SiafEjecucionSnapshot;
use App\Models\SyncLog;
use App\Models\SyncLogDetalle;
use App\Models\Usuario;

class SiafSyncService
{
    public function __construct(private SiafClientInterface $client) {}

    /** @return array<string, mixed> */
    public function syncEjecucion(?Usuario $ejecutor = null, string $modo = 'automatico', ?string $periodo = null): array
    {
        $periodo = $periodo ?? now()->format('Y-m');
        $log = SyncLog::create([
            'sistema' => 'siaf',
            'tipo_sync' => 'ejecucion',
            'modo' => $modo,
            'estado' => 'ok',
            'registros_ok' => 0,
            'registros_error' => 0,
            'ejecutado_por' => $ejecutor?->id,
            'ejecutado_at' => now(),
        ]);

        try {
            $data = $this->client->getEjecucionPresupuestal($periodo);
            $pim = (float) ($data['pim'] ?? 0);
            $ejecucion = (float) ($data['ejecucion_total'] ?? 0);
            $porcentaje = (float) ($data['porcentaje_ejecucion'] ?? ($pim > 0 ? round(($ejecucion / $pim) * 100, 2) : 0));

            $snapshot = SiafEjecucionSnapshot::create([
                'periodo' => $data['periodo'] ?? $periodo,
                'pim' => $pim,
                'ejecucion_total' => $ejecucion,
                'porcentaje_ejecucion' => $porcentaje,
                'detalle_resumido_json' => $data['detalle_resumido'] ?? $data['detalle_resumido_json'] ?? null,
                'es_simulacion' => $this->client->esSimulacion(),
                'sincronizado_at' => now(),
            ]);

            SyncLogDetalle::create([
                'sync_log_id' => $log->id,
                'entidad_externa' => 'ejecucion',
                'referencia' => $snapshot->periodo,
                'entidad_local' => 'siaf_ejecucion_snapshots',
                'entidad_local_id' => $snapshot->id,
                'estado' => 'ok',
                'mensaje' => 'Snapshot almacenado',
                'created_at' => now(),
            ]);

            $log->update([
                'estado' => 'ok',
                'registros_ok' => 1,
                'mensaje' => "SIAF período {$snapshot->periodo} sincronizado",
            ]);

            return [
                'sync_log_id' => $log->id,
                'estado' => 'ok',
                'registros_ok' => 1,
                'registros_error' => 0,
                'mensaje' => $log->mensaje,
                'es_simulacion' => $this->client->esSimulacion(),
                'snapshot' => [
                    'id' => $snapshot->id,
                    'periodo' => $snapshot->periodo,
                    'pim' => (float) $snapshot->pim,
                    'ejecucion_total' => (float) $snapshot->ejecucion_total,
                    'porcentaje_ejecucion' => (float) $snapshot->porcentaje_ejecucion,
                ],
            ];
        } catch (\Throwable $e) {
            $log->update([
                'estado' => 'error',
                'registros_error' => 1,
                'mensaje' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function esSimulacion(): bool
    {
        return $this->client->esSimulacion();
    }
}
