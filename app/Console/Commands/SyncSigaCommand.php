<?php

namespace App\Console\Commands;

use App\Services\Integrations\SigaSyncService;
use Illuminate\Console\Command;

class SyncSigaCommand extends Command
{
    protected $signature = 'sgmi:sync-siga {tipo=all : patrimonio|organigrama|personal|all}';

    protected $description = 'Sincroniza datos desde SIGA (API o simulador)';

    public function handle(SigaSyncService $service): int
    {
        $tipo = $this->argument('tipo');

        $resultados = [];

        if (in_array($tipo, ['patrimonio', 'all'], true)) {
            $resultados['patrimonio'] = $service->syncPatrimonio(null, 'automatico');
            $this->info('SIGA patrimonio: '.$resultados['patrimonio']['mensaje']);
        }

        if (in_array($tipo, ['organigrama', 'all'], true)) {
            $resultados['organigrama'] = $service->syncOrganigrama(null, 'automatico');
            $this->info('SIGA organigrama: '.$resultados['organigrama']['mensaje']);
        }

        if (in_array($tipo, ['personal', 'all'], true)) {
            $resultados['personal'] = $service->syncPersonal(null, 'automatico');
            $this->info('SIGA personal: '.$resultados['personal']['mensaje']);
        }

        if ($resultados === []) {
            $this->error('Tipo inválido. Use: patrimonio, organigrama, personal o all');

            return self::FAILURE;
        }

        $sim = $service->esSimulacion() ? ' (simulador)' : ' (API real)';

        $this->line('Driver'.$sim);

        return self::SUCCESS;
    }
}
