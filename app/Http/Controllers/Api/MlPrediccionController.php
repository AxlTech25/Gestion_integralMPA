<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Equipo;
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
            ->with(['equipo.unidad'])
            ->where('nivel_riesgo', $nivel)
            ->orderByDesc('probabilidad_falla')
            ->limit(20)
            ->get()
            ->unique('equipo_id')
            ->values();

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
