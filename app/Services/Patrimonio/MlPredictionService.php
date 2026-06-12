<?php

namespace App\Services\Patrimonio;

use App\Models\Equipo;
use App\Models\MlModelo;
use App\Models\MlPrediccion;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MlPredictionService
{
    public function __construct(private MlFeatureService $featureService) {}

    public function ejecutarBatch(): array
    {
        $modelo = $this->resolverModelo();
        $equipos = Equipo::query()->where('estado_operativo', '!=', 'baja')->get();
        $procesados = 0;
        $omitidos = 0;

        foreach ($equipos as $equipo) {
            $features = $this->featureService->forEquipo($equipo);

            if ($features['sin_datos_suficientes']) {
                $omitidos++;
                continue;
            }

            $probabilidad = $this->predecirProbabilidad($equipo->id, $features);
            $nivel = $this->nivelRiesgo($probabilidad);

            MlPrediccion::create([
                'equipo_id' => $equipo->id,
                'ml_modelo_id' => $modelo->id,
                'probabilidad_falla' => $probabilidad,
                'nivel_riesgo' => $nivel,
                'factores_json' => $features,
                'calculado_at' => now(),
            ]);
            $procesados++;
        }

        return [
            'procesados' => $procesados,
            'omitidos' => $omitidos,
            'modelo_version' => $modelo->version,
        ];
    }

    public function resumenSemaforo(): array
    {
        $ultimas = MlPrediccion::query()
            ->select('ml_predicciones.*')
            ->joinSub(
                MlPrediccion::query()
                    ->selectRaw('equipo_id, MAX(calculado_at) as max_calc')
                    ->groupBy('equipo_id'),
                'ult',
                function ($join) {
                    $join->on('ml_predicciones.equipo_id', '=', 'ult.equipo_id')
                        ->on('ml_predicciones.calculado_at', '=', 'ult.max_calc');
                }
            )
            ->get();

        return [
            'verde' => $ultimas->where('nivel_riesgo', 'verde')->count(),
            'amarillo' => $ultimas->where('nivel_riesgo', 'amarillo')->count(),
            'rojo' => $ultimas->where('nivel_riesgo', 'rojo')->count(),
            'total' => $ultimas->count(),
        ];
    }

    private function resolverModelo(): MlModelo
    {
        $version = config('sgmi.ml.modelo_version');

        return MlModelo::firstOrCreate(
            ['version' => $version],
            [
                'algoritmo' => config('sgmi.ml.service_url') ? 'random_forest' : 'random_forest_sim',
                'parametros_json' => ['n_estimators' => 100],
                'metricas_json' => ['modo' => config('sgmi.ml.service_url') ? 'fastapi' : 'simulado'],
                'entrenado_at' => now(),
            ]
        );
    }

    /** @param array<string, mixed> $features */
    private function predecirProbabilidad(int $equipoId, array $features): float
    {
        $url = config('sgmi.ml.service_url');

        if ($url) {
            try {
                $response = Http::timeout(30)->post(rtrim($url, '/').'/predict/batch', [
                    'model_version' => config('sgmi.ml.modelo_version'),
                    'equipos' => [['id' => $equipoId, 'features' => $features]],
                ]);

                if ($response->successful()) {
                    $pred = collect($response->json('predicciones'))->firstWhere('equipo_id', $equipoId);
                    if ($pred && isset($pred['probabilidad'])) {
                        return min(0.99, max(0.01, (float) $pred['probabilidad']));
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('ML service fallback: '.$e->getMessage());
            }
        }

        return $this->simularRandomForest($features);
    }

    /** Simulación ponderada de features (desarrollo sin FastAPI). */
    private function simularRandomForest(array $features): float
    {
        $score = 0.05;
        $score += min(0.35, $features['incidencias_12m'] * 0.08);
        $score += min(0.25, $features['mantenimientos_correctivos_12m'] * 0.07);
        $score += min(0.20, ($features['antiguedad_anios'] ?? 0) * 0.03);

        if ($features['estado_operativo'] === 'reparacion') {
            $score += 0.25;
        }

        if (($features['ram_gb'] ?? 0) > 0 && $features['ram_gb'] < 8) {
            $score += 0.08;
        }

        if ($features['dias_desde_ultimo_mantenimiento'] > 180) {
            $score += 0.10;
        }

        if ($features['tipo_equipo'] === 'servidor') {
            $score += 0.05;
        }

        return min(0.95, max(0.05, $score));
    }

    private function nivelRiesgo(float $probabilidad): string
    {
        $amarillo = config('sgmi.ml.umbrales.amarillo');
        $verde = config('sgmi.ml.umbrales.verde');

        if ($probabilidad >= $amarillo) {
            return 'rojo';
        }

        if ($probabilidad >= $verde) {
            return 'amarillo';
        }

        return 'verde';
    }
}
