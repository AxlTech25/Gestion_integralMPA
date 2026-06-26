<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Services\Core\MenuService;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function __construct(private MenuService $menuService) {}

    public function index(Request $request)
    {
        $usuario = $request->user();
        if (! $usuario instanceof Usuario) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        return response()->json([
            'menu' => $this->menuService->forUsuario($usuario),
            'nucleo' => $this->menuService->nucleoSubmenu($usuario),
            'patrimonio' => $this->menuService->patrimonioSubmenu($usuario),
            'calidad' => $this->menuService->calidadSubmenu($usuario),
        ]);
    }
}
