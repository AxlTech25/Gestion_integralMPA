<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Calidad\CerrarNoConformidadRequest;
use App\Http\Requests\Calidad\StoreAccionCorrectivaRequest;
use App\Http\Requests\Calidad\StoreNoConformidadRequest;
use App\Http\Requests\Calidad\UpdateAccionCorrectivaRequest;
use App\Http\Requests\Calidad\UpdateNoConformidadRequest;
use App\Models\AccionCorrectiva;
use App\Models\NoConformidad;
use App\Models\Usuario;
use App\Services\Calidad\NoConformidadService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NoConformidadController extends Controller
{
    public function __construct(private NoConformidadService $service) {}

    public function index(Request $request)
    {
        $usuario = $request->user();
        if (! $usuario instanceof Usuario) {
            return response()->json([], Response::HTTP_UNAUTHORIZED);
        }

        if (! $usuario->hasAnyPermiso(['calidad.nc.consultar', 'calidad.nc.reportar', 'calidad.nc.gestionar'])) {
            return response()->json(['message' => 'No tiene permiso.'], Response::HTTP_FORBIDDEN);
        }

        $query = NoConformidad::query()
            ->with(['unidad', 'reportador', 'responsable', 'accionesCorrectivas'])
            ->orderByDesc('created_at');

        if ($request->filled('estado')) {
            $query->where('estado', $request->string('estado'));
        }

        if ($request->boolean('solo_abiertas')) {
            $query->where('estado', '!=', 'cerrada');
        }

        if (
            $usuario->hasPermiso('calidad.nc.reportar')
            && ! $usuario->hasAnyPermiso(['calidad.nc.gestionar', 'calidad.nc.consultar'])
        ) {
            $query->where('reportado_por', $usuario->id);
        }

        $items = $query->limit($request->integer('limit', 50))->get();

        return response()->json($items->map(fn ($nc) => $this->formatNc($nc)));
    }

    public function store(StoreNoConformidadRequest $request)
    {
        $nc = $this->service->reportar($request->user(), $request->validated());

        return response()->json($this->formatNc($nc), Response::HTTP_CREATED);
    }

    public function show(Request $request, NoConformidad $noConformidad)
    {
        if (! $this->puedeVer($request->user(), $noConformidad)) {
            return response()->json(['message' => 'No autorizado.'], Response::HTTP_FORBIDDEN);
        }

        $noConformidad->load([
            'unidad', 'reportador', 'responsable', 'verificador',
            'expediente', 'incidencia', 'accionesCorrectivas.responsable',
        ]);

        return response()->json($this->formatNcDetalle($noConformidad));
    }

    public function update(UpdateNoConformidadRequest $request, NoConformidad $noConformidad)
    {
        $nc = $this->service->actualizar($noConformidad, $request->user(), $request->validated());

        return response()->json($this->formatNcDetalle($nc));
    }

    public function cerrar(CerrarNoConformidadRequest $request, NoConformidad $noConformidad)
    {
        $nc = $this->service->cerrar($noConformidad, $request->user(), $request->validated());

        return response()->json($this->formatNcDetalle($nc));
    }

    public function storeAccionCorrectiva(StoreAccionCorrectivaRequest $request, NoConformidad $noConformidad)
    {
        $ac = $this->service->crearAccionCorrectiva($noConformidad, $request->user(), $request->validated());

        return response()->json($this->formatAc($ac), Response::HTTP_CREATED);
    }

    public function updateAccionCorrectiva(UpdateAccionCorrectivaRequest $request, AccionCorrectiva $accionCorrectiva)
    {
        $ac = $this->service->actualizarAccionCorrectiva($accionCorrectiva, $request->user(), $request->validated());

        return response()->json($this->formatAc($ac));
    }

    public function resumen(Request $request)
    {
        $usuario = $request->user();
        if (! $usuario instanceof Usuario || ! $usuario->hasAnyPermiso(['calidad.nc.consultar', 'calidad.nc.gestionar'])) {
            return response()->json(['message' => 'No tiene permiso.'], Response::HTTP_FORBIDDEN);
        }

        return response()->json([
            'abiertas' => NoConformidad::where('estado', '!=', 'cerrada')->count(),
            'cerradas' => NoConformidad::where('estado', 'cerrada')->count(),
            'con_ac_pendiente' => NoConformidad::where('requiere_ac', true)
                ->where('estado', '!=', 'cerrada')
                ->count(),
            'ac_abiertas' => AccionCorrectiva::whereNotIn('estado', ['cerrada', 'ineficaz'])->count(),
        ]);
    }

    private function puedeVer(?Usuario $usuario, NoConformidad $nc): bool
    {
        if (! $usuario instanceof Usuario) {
            return false;
        }

        if ($usuario->hasAnyPermiso(['calidad.nc.consultar', 'calidad.nc.gestionar'])) {
            return true;
        }

        return $usuario->hasPermiso('calidad.nc.reportar') && $nc->reportado_por === $usuario->id;
    }

    private function formatNc(NoConformidad $nc): array
    {
        return [
            'id' => $nc->id,
            'codigo' => $nc->codigo,
            'proceso' => $nc->proceso,
            'severidad' => $nc->severidad,
            'descripcion' => $nc->descripcion,
            'estado' => $nc->estado,
            'requiere_ac' => $nc->requiere_ac,
            'unidad' => $nc->unidad?->nombre,
            'reportado_por' => $nc->reportador?->nombre_completo,
            'responsable' => $nc->responsable?->nombre_completo,
            'acciones_count' => $nc->relationLoaded('accionesCorrectivas')
                ? $nc->accionesCorrectivas->count()
                : $nc->accionesCorrectivas()->count(),
            'created_at' => $nc->created_at?->toIso8601String(),
            'cerrada_at' => $nc->cerrada_at?->toIso8601String(),
        ];
    }

    private function formatNcDetalle(NoConformidad $nc): array
    {
        $data = $this->formatNc($nc);
        $data['requisito_incumplido'] = $nc->requisito_incumplido;
        $data['evidencia'] = $nc->evidencia;
        $data['contencion'] = $nc->contencion;
        $data['causa_raiz'] = $nc->causa_raiz;
        $data['verificacion_eficacia'] = $nc->verificacion_eficacia;
        $data['verificada_por'] = $nc->verificador?->nombre_completo;
        $data['verificada_at'] = $nc->verificada_at?->toIso8601String();
        $data['expediente_codigo'] = $nc->expediente?->codigo;
        $data['expediente_id'] = $nc->expediente_id;
        $data['incidencia_id'] = $nc->incidencia_id;
        $data['acciones_correctivas'] = $nc->accionesCorrectivas->map(fn ($ac) => $this->formatAc($ac));

        return $data;
    }

    private function formatAc(AccionCorrectiva $ac): array
    {
        return [
            'id' => $ac->id,
            'codigo' => $ac->codigo,
            'no_conformidad_id' => $ac->no_conformidad_id,
            'causa_raiz' => $ac->causa_raiz,
            'plan_acciones' => $ac->plan_acciones,
            'estado' => $ac->estado,
            'responsable' => $ac->responsable?->nombre_completo,
            'responsable_id' => $ac->responsable_id,
            'evidencia_implementacion' => $ac->evidencia_implementacion,
            'metodo_verificacion' => $ac->metodo_verificacion,
            'resultado_verificacion' => $ac->resultado_verificacion,
            'implementada_at' => $ac->implementada_at?->toIso8601String(),
            'cerrada_at' => $ac->cerrada_at?->toIso8601String(),
            'created_at' => $ac->created_at?->toIso8601String(),
        ];
    }
}
