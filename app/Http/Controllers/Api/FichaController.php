<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patrimonio\StoreFichaMantenimientoRequest;
use App\Http\Requests\Patrimonio\StoreFichaTecnicaRequest;
use App\Models\Equipo;
use App\Services\Patrimonio\FichaService;

class FichaController extends Controller
{
    public function __construct(private FichaService $fichaService) {}

    public function storeTecnica(StoreFichaTecnicaRequest $request, Equipo $equipo)
    {
        $ficha = $this->fichaService->guardarFichaTecnica($equipo, $request->user(), $request->validated());

        return response()->json($ficha, 201);
    }

    public function storeMantenimiento(StoreFichaMantenimientoRequest $request, Equipo $equipo)
    {
        $ficha = $this->fichaService->registrarMantenimiento($equipo, $request->user(), $request->validated());

        return response()->json($ficha, 201);
    }
}
