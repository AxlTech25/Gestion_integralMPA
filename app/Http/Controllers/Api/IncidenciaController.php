<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patrimonio\StoreIncidenciaRequest;
use App\Http\Requests\Patrimonio\UpdateIncidenciaRequest;
use App\Models\Incidencia;
use App\Models\Usuario;
use App\Services\Patrimonio\IncidenciaService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IncidenciaController extends Controller
{
    public function __construct(private IncidenciaService $incidenciaService) {}

    public function index(Request $request)
    {
        $usuario = $request->user();
        if (! $usuario instanceof Usuario) {
            return response()->json([], Response::HTTP_UNAUTHORIZED);
        }

        if (! $usuario->hasAnyPermiso(['pat.incidencia.gestionar', 'pat.incidencia.reportar'])) {
            return response()->json(['message' => 'No tiene permiso para consultar incidencias.'], Response::HTTP_FORBIDDEN);
        }

        $query = Incidencia::query()
            ->with(['equipo.unidad', 'reportador', 'asignadoUtis'])
            ->orderByDesc('created_at');

        if (! $usuario->hasPermiso('pat.incidencia.gestionar')) {
            $query->where('reportado_por', $usuario->id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->string('estado'));
        }

        if ($request->boolean('solo_abiertas')) {
            $query->whereIn('estado', ['abierta', 'en_atencion']);
        }

        $items = $query->limit($request->integer('limit', 50))->get();

        return response()->json($items->map(fn ($i) => $this->formatIncidencia($i)));
    }

    public function store(StoreIncidenciaRequest $request)
    {
        $equipo = \App\Models\Equipo::findOrFail($request->integer('equipo_id'));
        $incidencia = $this->incidenciaService->reportar($equipo, $request->user(), $request->validated());

        return response()->json($this->formatIncidencia($incidencia), Response::HTTP_CREATED);
    }

    public function update(UpdateIncidenciaRequest $request, Incidencia $incidencia)
    {
        $incidencia = $this->incidenciaService->actualizarEstado(
            $incidencia,
            $request->user(),
            $request->validated()
        );

        return response()->json($this->formatIncidencia($incidencia));
    }

    private function formatIncidencia(Incidencia $i): array
    {
        return [
            'id' => $i->id,
            'equipo_id' => $i->equipo_id,
            'codigo_patrimonial' => $i->equipo?->codigo_patrimonial,
            'equipo_label' => trim(($i->equipo?->marca ?? '').' '.$i->equipo?->modelo),
            'unidad' => $i->equipo?->unidad?->nombre,
            'tipo' => $i->tipo,
            'descripcion' => $i->descripcion,
            'estado' => $i->estado,
            'solucion' => $i->solucion,
            'reportado_por' => $i->reportador?->nombre_completo,
            'asignado' => $i->asignadoUtis?->nombre_completo,
            'created_at' => $i->created_at?->toIso8601String(),
            'cerrada_at' => $i->cerrada_at?->toIso8601String(),
        ];
    }
}
