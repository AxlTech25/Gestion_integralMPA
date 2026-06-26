<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Services\Dashboard\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService) {}

    public function operativo(Request $request)
    {
        $usuario = $request->user();
        if (! $usuario instanceof Usuario) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        if (! $usuario->hasAnyPermiso(['doc.expediente.consultar', 'dash.tramitacion.ver'])) {
            return response()->json(['message' => 'No tiene permiso para ver el panel operativo.'], 403);
        }

        $dias = $request->integer('dias', 30);

        return response()->json($this->dashboardService->operativo($usuario, $dias));
    }

    public function estrategico(Request $request)
    {
        $usuario = $request->user();
        if (! $usuario instanceof Usuario) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        $dias = $request->integer('dias', 30);

        return response()->json($this->dashboardService->estrategico($usuario, $dias));
    }
}
