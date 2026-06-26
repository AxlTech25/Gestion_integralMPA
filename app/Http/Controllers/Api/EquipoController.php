<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Patrimonio\StoreEquipoRequest;
use App\Http\Requests\Patrimonio\UpdateEquipoRequest;
use App\Models\Equipo;
use App\Models\Usuario;
use App\Services\Patrimonio\EquipoService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EquipoController extends Controller
{
    public function __construct(private EquipoService $equipoService) {}

    public function index(Request $request)
    {
        $usuario = $request->user();
        $vistaCompleta = $usuario instanceof Usuario && $usuario->hasPermiso('pat.equipo.registrar');

        $query = Equipo::query()
            ->with(['unidad', 'ultimaPrediccion'])
            ->orderBy('codigo_patrimonial');

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($inner) use ($q) {
                $inner->where('codigo_patrimonial', 'like', "%{$q}%")
                    ->orWhere('marca', 'like', "%{$q}%")
                    ->orWhere('modelo', 'like', "%{$q}%")
                    ->orWhere('custodio_nombre', 'like', "%{$q}%");
            });
        }

        if ($request->filled('unidad_id')) {
            $query->where('unidad_id', $request->integer('unidad_id'));
        }

        if ($request->filled('estado_operativo')) {
            $query->where('estado_operativo', $request->string('estado_operativo'));
        }

        if ($request->filled('nivel_riesgo')) {
            $nivel = $request->string('nivel_riesgo');
            $query->whereHas('ultimaPrediccion', fn ($q) => $q->where('nivel_riesgo', $nivel));
        }

        if ($request->boolean('solo_activos', true)) {
            $query->where('estado_operativo', '!=', 'baja');
        }

        $equipos = $query->paginate($request->integer('per_page', 15));

        return response()->json([
            'data' => collect($equipos->items())->map(fn ($e) => $this->formatEquipo($e, $vistaCompleta)),
            'meta' => [
                'current_page' => $equipos->currentPage(),
                'last_page' => $equipos->lastPage(),
                'per_page' => $equipos->perPage(),
                'total' => $equipos->total(),
            ],
        ]);
    }

    public function store(StoreEquipoRequest $request)
    {
        $equipo = $this->equipoService->registrar($request->user(), $request->validated());

        return response()->json($this->formatEquipo($equipo, true), 201);
    }

    public function show(Request $request, Equipo $equipo)
    {
        $usuario = $request->user();
        $vistaCompleta = $usuario instanceof Usuario && $usuario->hasPermiso('pat.equipo.registrar');

        $equipo->load([
            'unidad',
            'fichaTecnica',
            'fichasMantenimiento',
            'incidencias.reportador',
            'ultimaPrediccion',
        ]);

        return response()->json($this->formatEquipoDetalle($equipo, $vistaCompleta));
    }

    public function update(UpdateEquipoRequest $request, Equipo $equipo)
    {
        $equipo = $this->equipoService->actualizar($equipo, $request->user(), $request->validated());

        return response()->json($this->formatEquipo($equipo, true));
    }

    public function buscarSoporte(Request $request)
    {
        $usuario = $request->user();
        if (! $usuario instanceof Usuario) {
            return response()->json([], Response::HTTP_UNAUTHORIZED);
        }

        if (! $usuario->hasAnyPermiso(['pat.incidencia.reportar', 'pat.incidencia.gestionar', 'pat.equipo.consultar'])) {
            return response()->json(['message' => 'No tiene permiso.'], Response::HTTP_FORBIDDEN);
        }

        $q = trim((string) $request->input('q', ''));
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $query = Equipo::query()
            ->where('estado_operativo', '!=', 'baja')
            ->where(function ($inner) use ($q) {
                $inner->where('codigo_patrimonial', 'like', "%{$q}%")
                    ->orWhere('marca', 'like', "%{$q}%")
                    ->orWhere('modelo', 'like', "%{$q}%");
            });

        if (
            $usuario->hasPermiso('pat.incidencia.reportar')
            && ! $usuario->hasPermiso('pat.incidencia.gestionar')
            && ! $usuario->hasPermiso('pat.equipo.consultar')
        ) {
            $query->where('unidad_id', $usuario->unidad_activa_id);
        }

        $equipos = $query->orderBy('codigo_patrimonial')->limit(10)->get();

        return response()->json($equipos->map(fn ($e) => [
            'id' => $e->id,
            'codigo_patrimonial' => $e->codigo_patrimonial,
            'marca' => $e->marca,
            'modelo' => $e->modelo,
            'label' => trim("{$e->codigo_patrimonial} — {$e->marca} {$e->modelo}"),
        ]));
    }

    private function formatEquipo(Equipo $e, bool $vistaCompleta): array
    {
        $base = [
            'id' => $e->id,
            'codigo_patrimonial' => $e->codigo_patrimonial,
            'tipo_equipo' => $e->tipo_equipo,
            'marca' => $e->marca,
            'modelo' => $e->modelo,
            'numero_serie' => $e->numero_serie,
            'estado_operativo' => $e->estado_operativo,
            'unidad_id' => $e->unidad_id,
            'unidad' => $e->unidad?->nombre,
            'custodio_nombre' => $e->custodio_nombre,
            'custodio_cargo' => $e->custodio_cargo,
            'fecha_adquisicion' => $e->fecha_adquisicion?->format('Y-m-d'),
            'riesgo' => $e->ultimaPrediccion ? [
                'nivel' => $e->ultimaPrediccion->nivel_riesgo,
                'probabilidad' => (float) $e->ultimaPrediccion->probabilidad_falla,
            ] : null,
        ];

        if ($vistaCompleta) {
            $base['codigo_siga'] = $e->codigo_siga;
            $base['valor_patrimonial'] = $e->valor_patrimonial;
        }

        return $base;
    }

    private function formatEquipoDetalle(Equipo $e, bool $vistaCompleta): array
    {
        $data = $this->formatEquipo($e, $vistaCompleta);

        $data['ficha_tecnica'] = $e->fichaTecnica;
        $data['mantenimientos'] = $e->fichasMantenimiento->map(fn ($m) => [
            'id' => $m->id,
            'tipo' => $m->tipo,
            'fecha' => $m->fecha?->format('Y-m-d'),
            'descripcion' => $m->descripcion,
            'resultado' => $m->resultado,
            'tecnico' => $m->tecnico,
        ]);
        $data['incidencias'] = $e->incidencias->take(10)->map(fn ($i) => [
            'id' => $i->id,
            'tipo' => $i->tipo,
            'descripcion' => $i->descripcion,
            'estado' => $i->estado,
            'created_at' => $i->created_at?->toIso8601String(),
            'reportador' => $i->reportador?->nombre_completo,
        ]);

        return $data;
    }
}
