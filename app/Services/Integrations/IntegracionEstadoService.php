<?php

namespace App\Services\Integrations;

use App\Models\SyncLog;

class IntegracionEstadoService
{
    public function __construct(
        private SigaSyncService $sigaSync,
        private SiafSyncService $siafSync,
    ) {}

    /** @return array<string, mixed> */
    public function estado(): array
    {
        return [
            'siga' => [
                'driver' => config('integrations.siga.driver'),
                'es_simulacion' => $this->sigaSync->esSimulacion(),
                'ultimos_sync' => $this->ultimosPorSistema('siga'),
            ],
            'siaf' => [
                'driver' => config('integrations.siaf.driver'),
                'es_simulacion' => $this->siafSync->esSimulacion(),
                'ultimos_sync' => $this->ultimosPorSistema('siaf'),
            ],
        ];
    }

    /** @return array<int, array<string, mixed>> */
    public function logsRecientes(int $limit = 20): array
    {
        return SyncLog::query()
            ->with('ejecutor:id,username,nombre_completo')
            ->orderByDesc('ejecutado_at')
            ->limit($limit)
            ->get()
            ->map(fn (SyncLog $log) => [
                'id' => $log->id,
                'sistema' => $log->sistema,
                'tipo_sync' => $log->tipo_sync,
                'modo' => $log->modo,
                'estado' => $log->estado,
                'registros_ok' => $log->registros_ok,
                'registros_error' => $log->registros_error,
                'mensaje' => $log->mensaje,
                'ejecutado_at' => $log->ejecutado_at?->toIso8601String(),
                'ejecutor' => $log->ejecutor?->nombre_completo,
            ])
            ->all();
    }

    /** @return array<string, array<string, mixed>|null> */
    private function ultimosPorSistema(string $sistema): array
    {
        $tipos = $sistema === 'siga'
            ? ['patrimonio', 'organigrama', 'personal']
            : ['ejecucion'];

        $resultado = [];
        foreach ($tipos as $tipo) {
            $log = SyncLog::query()
                ->where('sistema', $sistema)
                ->where('tipo_sync', $tipo)
                ->orderByDesc('ejecutado_at')
                ->first();

            $resultado[$tipo] = $log ? [
                'id' => $log->id,
                'estado' => $log->estado,
                'registros_ok' => $log->registros_ok,
                'registros_error' => $log->registros_error,
                'mensaje' => $log->mensaje,
                'ejecutado_at' => $log->ejecutado_at?->toIso8601String(),
            ] : null;
        }

        return $resultado;
    }
}
