<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MlPrediccion;
use App\Services\Patrimonio\MlPredictionService;
use Illuminate\Http\Request;

class MlPrediccionController extends Controller
{
    public function __construct(private MlPredictionService $predictionService) {}

    public function semaforo()
    {
        return response()->json($this->predictionService->resumenSemaforo());
    }

    public function criticos(Request $request)
    {
        $nivel = $request->string('nivel', 'rojo');

        $predicciones = MlPrediccion::query()
            ->select('ml_predicciones.*')
            ->with(['equipo.unidad'])
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
            ->where('nivel_riesgo', $nivel)
            ->orderByDesc('probabilidad_falla')
            ->limit(50)
            ->get();

        return response()->json($predicciones->map(fn ($p) => [
            'equipo_id' => $p->equipo_id,
            'codigo_patrimonial' => $p->equipo?->codigo_patrimonial,
            'marca' => $p->equipo?->marca,
            'modelo' => $p->equipo?->modelo,
            'unidad' => $p->equipo?->unidad?->nombre,
            'custodio_nombre' => $p->equipo?->custodio_nombre,
            'probabilidad_falla' => (float) $p->probabilidad_falla,
            'nivel_riesgo' => $p->nivel_riesgo,
            'calculado_at' => $p->calculado_at?->toIso8601String(),
        ]));
    }

    public function ejecutar()
    {
        $resultado = $this->predictionService->ejecutarBatch();

        return response()->json($resultado);
    }
}
