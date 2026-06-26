<?php

namespace App\Console\Commands;

use App\Services\Integrations\SiafSyncService;
use Illuminate\Console\Command;

class SyncSiafCommand extends Command
{
    protected $signature = 'sgmi:sync-siaf {periodo? : Período YYYY-MM}';

    protected $description = 'Sincroniza ejecución presupuestal desde SIAF (API o simulador)';

    public function handle(SiafSyncService $service): int
    {
        $periodo = $this->argument('periodo');
        $resultado = $service->syncEjecucion(null, 'automatico', $periodo);

        $this->info($resultado['mensaje']);
        $this->line('Simulación: '.($resultado['es_simulacion'] ? 'sí' : 'no'));

        return self::SUCCESS;
    }
}
