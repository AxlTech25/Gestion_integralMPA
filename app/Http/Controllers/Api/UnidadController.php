<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\UpdateUnidadRequest;
use App\Models\UnidadOrganizacional;
use App\Services\Core\UnidadAdminService;
use Illuminate\Http\Request;

class UnidadController extends Controller
{
    public function __construct(private UnidadAdminService $unidadService) {}

    public function index(Request $request)
    {
        $query = UnidadOrganizacional::query()
            ->with(['hijos' => fn ($q) => $q->orderBy('nombre')])
            ->orderBy('codigo_org');

        if ($request->boolean('solo_activas', true)) {
            $query->activas();
        }

        if ($request->boolean('solo_raiz')) {
            $query->whereNull('padre_id');
        }

        return response()->json($query->get());
    }

    public function tree()
    {
        $unidades = UnidadOrganizacional::query()
            ->activas()
            ->orderBy('codigo_org')
            ->get();

        $byPadre = $unidades->groupBy('padre_id');

        $build = function (?int $padreId) use (&$build, $byPadre) {
            return ($byPadre[$padreId] ?? collect())->map(function (UnidadOrganizacional $u) use ($build) {
                return [
                    'id' => $u->id,
                    'codigo_org' => $u->codigo_org,
                    'nombre' => $u->nombre,
                    'tipo' => $u->tipo,
                    'permite_derivacion' => $u->permite_derivacion,
                    'activa' => $u->activa,
                    'gerencia_id' => $u->gerencia_id,
                    'padre_id' => $u->padre_id,
                    'children' => $build($u->id),
                ];
            })->values();
        };

        return response()->json($build(null));
    }

    public function derivacion()
    {
        $unidades = UnidadOrganizacional::destinoDerivacion()
            ->orderBy('nombre')
            ->get(['id', 'codigo_org', 'nombre', 'tipo', 'gerencia_id']);

        return response()->json($unidades);
    }

    public function show(UnidadOrganizacional $unidad)
    {
        $unidad->load(['gerencia', 'padre', 'hijos']);

        return response()->json($unidad);
    }

    public function update(UpdateUnidadRequest $request, UnidadOrganizacional $unidad)
    {
        $unidad = $this->unidadService->actualizar(
            $unidad,
            $request->validated(),
            $request->user()
        );

        return response()->json($unidad);
    }
}
