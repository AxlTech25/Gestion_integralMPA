<?php

namespace App\Console\Commands;

use App\Services\Patrimonio\MlPredictionService;
use Illuminate\Console\Command;

class EjecutarMlPredictCommand extends Command
{
    protected $signature = 'sgmi:ml-predict';

    protected $description = 'Ejecuta predicción ML Random Forest para equipos patrimoniales';

    public function handle(MlPredictionService $predictionService): int
    {
        $resultado = $predictionService->ejecutarBatch();

        $this->info(sprintf(
            'ML completado — procesados: %d, omitidos: %d, modelo: %s',
            $resultado['procesados'],
            $resultado['omitidos'],
            $resultado['modelo_version'],
        ));

        return self::SUCCESS;
    }
}
