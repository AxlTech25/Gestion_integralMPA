<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\StoreUsuarioRequest;
use App\Http\Requests\Core\TrasladoUsuarioRequest;
use App\Http\Requests\Core\UpdateUsuarioRequest;
use App\Models\Role;
use App\Models\Usuario;
use App\Services\Core\UsuarioAdminService;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function __construct(private UsuarioAdminService $usuarioService) {}

    public function index(Request $request)
    {
        $query = Usuario::query()
            ->with(['unidadActiva', 'roles'])
            ->orderBy('nombre_completo');

        if ($request->filled('unidad_id')) {
            $query->where('unidad_activa_id', $request->integer('unidad_id'));
        }

        if ($request->filled('activo')) {
            $query->where('activo', $request->boolean('activo'));
        }

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($inner) use ($q) {
                $inner->where('username', 'like', "%{$q}%")
                    ->orWhere('nombre_completo', 'like', "%{$q}%");
            });
        }

        return response()->json($query->paginate($request->integer('per_page', 15)));
    }

    public function store(StoreUsuarioRequest $request)
    {
        $usuario = $this->usuarioService->crear(
            $request->validated(),
            $request->user()
        );

        return response()->json($usuario, 201);
    }

    public function show(Usuario $usuario)
    {
        $usuario->load(['unidadActiva', 'roles', 'traslados.unidad']);

        return response()->json($usuario);
    }

    public function update(UpdateUsuarioRequest $request, Usuario $usuario)
    {
        $usuario = $this->usuarioService->actualizar(
            $usuario,
            $request->validated(),
            $request->user()
        );

        return response()->json($usuario);
    }

    public function traslado(TrasladoUsuarioRequest $request, Usuario $usuario)
    {
        $usuario = $this->usuarioService->trasladar(
            $usuario,
            $request->integer('unidad_id'),
            $request->input('motivo'),
            $request->user()
        );

        return response()->json($usuario);
    }

    public function roles()
    {
        return response()->json(Role::orderBy('nombre')->get(['id', 'codigo', 'nombre']));
    }
}
