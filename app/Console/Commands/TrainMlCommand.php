<?php

namespace App\Console\Commands;

use App\Models\Equipo;
use App\Models\Incidencia;
use App\Models\MlModelo;
use App\Services\Patrimonio\MlFeatureService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TrainMlCommand extends Command
{
    protected $signature = 'sgmi:ml-train {--demo : Entrenar con fixture demo del servicio ML}';

    protected $description = 'Exporta dataset desde SGMI y entrena Random Forest en el servicio ML';

    public function handle(MlFeatureService $featureService): int
    {
        $url = config('sgmi.ml.service_url');
        if (! $url) {
            $this->error('Configure ML_SERVICE_URL en .env');

            return self::FAILURE;
        }

        if ($this->option('demo')) {
            return $this->entrenarDemo($url);
        }

        $dataset = [];
        $equipos = Equipo::query()->where('estado_operativo', '!=', 'baja')->get();

        foreach ($equipos as $equipo) {
            $features = $featureService->forEquipo($equipo);
            if ($features['sin_datos_suficientes']) {
                continue;
            }

            $incidenciasCriticas = Incidencia::where('equipo_id', $equipo->id)
                ->whereIn('tipo', ['falla', 'averia'])
                ->where('created_at', '>=', now()->subMonths(12))
                ->exists();

            $label = ($incidenciasCriticas || $equipo->estado_operativo === 'reparacion') ? 1 : 0;

            $dataset[] = [
                'equipo_id' => $equipo->id,
                'features' => $features,
                'label' => $label,
            ];
        }

        if (count($dataset) < 10) {
            $this->warn('Dataset insuficiente ('.count($dataset).' registros). Use --demo o agregue fichas/incidencias.');

            return $this->entrenarDemo($url);
        }

        $version = config('sgmi.ml.modelo_version');
        $response = $this->client()->post(rtrim($url, '/').'/train', [
            'version' => $version,
            'dataset' => $dataset,
        ]);

        if ($response->failed()) {
            $this->error('Error ML train: '.$response->body());

            return self::FAILURE;
        }

        $metricas = $response->json('metricas');
        MlModelo::updateOrCreate(
            ['version' => $version],
            [
                'algoritmo' => 'random_forest',
                'parametros_json' => ['n_estimators' => 100],
                'metricas_json' => $metricas,
                'entrenado_at' => now(),
            ]
        );

        $this->info(sprintf(
            'Modelo %s entrenado — accuracy: %s, f1: %s, muestras: %s',
            $version,
            $metricas['accuracy'] ?? '—',
            $metricas['f1'] ?? '—',
            $metricas['muestras'] ?? count($dataset),
        ));

        return self::SUCCESS;
    }

    private function entrenarDemo(string $url): int
    {
        $response = $this->client()->post(rtrim($url, '/').'/train/demo');

        if ($response->failed()) {
            $this->error('Error ML train/demo: '.$response->body());

            return self::FAILURE;
        }

        $this->info('Modelo demo entrenado: '.json_encode($response->json('metricas')));

        return self::SUCCESS;
    }

    private function client()
    {
        $request = Http::timeout((int) config('sgmi.ml.timeout', 60))->acceptJson();
        $token = config('sgmi.ml.api_token');

        if ($token) {
            $request = $request->withToken($token);
        }

        return $request;
    }
}
