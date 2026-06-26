<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Documentaria\DevolverExpedienteRequest;
use App\Http\Requests\Documentaria\DerivarExpedienteRequest;
use App\Http\Requests\Documentaria\StoreExpedienteRequest;
use App\Models\Expediente;
use App\Models\Usuario;
use App\Services\Core\AccesoService;
use App\Services\Documentaria\ExpedienteHistorialService;
use App\Services\Documentaria\ExpedienteService;
use Illuminate\Http\Request;

class ExpedienteController extends Controller
{
    public function __construct(
        private ExpedienteService $expedienteService,
        private ExpedienteHistorialService $historialService,
    ) {}

    public function bandeja(Request $request)
    {
        $usuario = $request->user();
        if (! $usuario instanceof Usuario) {
            return response()->json([], 401);
        }

        if (! app(AccesoService::class)->puedeOperarDocumentaria($usuario)) {
            return response()->json(['message' => 'Acceso denegado a bandeja operativa.'], 403);
        }

        $query = Expediente::query()
            ->with(['tipoDocumental', 'unidadOrigen', 'unidadActual'])
            ->where('unidad_actual_id', $usuario->unidad_activa_id)
            ->whereIn('estado', ['registrado', 'por_recepcionar', 'en_tramite', 'devuelto'])
            ->orderByDesc('prioridad')
            ->orderBy('created_at');

        if ($request->filled('prioridad') && $request->prioridad !== 'todas') {
            $query->where('prioridad', $request->prioridad);
        }

        if ($request->filled('tipo_documental_id')) {
            $query->where('tipo_documental_id', $request->integer('tipo_documental_id'));
        }

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($inner) use ($q) {
                $inner->where('codigo', 'like', "%{$q}%")
                    ->orWhere('asunto', 'like', "%{$q}%");
            });
        }

        if ($request->filled('antiguedad_min')) {
            $dias = $request->integer('antiguedad_min');
            $query->where('created_at', '<=', now()->subDays($dias));
        }

        if ($request->filled('antiguedad_max')) {
            $dias = $request->integer('antiguedad_max');
            $query->where('created_at', '>=', now()->subDays($dias));
        }

        $expedientes = $query->get();

        $resumen = [
            'total' => $expedientes->count(),
            'urgentes' => $expedientes->where('prioridad', 'alta')->count(),
            'por_recepcionar' => $expedientes->where('estado', 'por_recepcionar')->count(),
            'promedio_dias' => $expedientes->isEmpty()
                ? 0
                : round($expedientes->avg(fn ($e) => $this->expedienteService->antiguedadDias($e)), 1),
        ];

        return response()->json([
            'resumen' => $resumen,
            'expedientes' => $expedientes->map(fn ($e) => $this->formatListItem($e)),
        ]);
    }

    public function store(StoreExpedienteRequest $request)
    {
        $expediente = $this->expedienteService->registrar(
            $request->user(),
            $request->validated(),
            $request->file('archivo')
        );

        return response()->json($this->formatDetail($expediente, $request->user()), 201);
    }

    public function buscar(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $expedientes = Expediente::query()
            ->where(function ($inner) use ($q) {
                $inner->where('codigo', 'like', "%{$q}%")
                    ->orWhere('asunto', 'like', "%{$q}%");
            })
            ->orderByDesc('updated_at')
            ->limit(8)
            ->get(['id', 'codigo', 'asunto', 'estado']);

        return response()->json($expedientes);
    }

    public function show(Expediente $expediente, Request $request)
    {
        $expediente = $this->expedienteService->cargarRelaciones($expediente);

        return response()->json($this->formatDetail($expediente, $request->user()));
    }

    public function showByCodigo(string $codigo, Request $request)
    {
        $expediente = Expediente::where('codigo', $codigo)->firstOrFail();
        $expediente = $this->expedienteService->cargarRelaciones($expediente);

        return response()->json($this->formatDetail($expediente, $request->user()));
    }

    public function derivar(DerivarExpedienteRequest $request, Expediente $expediente)
    {
        $expediente = $this->expedienteService->derivar(
            $expediente->load('tipoDocumental'),
            $request->user(),
            $request->integer('unidad_destino_id'),
            $request->input('proveido')
        );

        return response()->json($this->formatDetail($expediente, $request->user()));
    }

    public function recepcionar(Request $request, Expediente $expediente)
    {
        $expediente = $this->expedienteService->recepcionar($expediente, $request->user());

        return response()->json($this->formatDetail($expediente, $request->user()));
    }

    public function devolver(DevolverExpedienteRequest $request, Expediente $expediente)
    {
        $expediente = $this->expedienteService->devolver(
            $expediente,
            $request->user(),
            $request->string('observacion')
        );

        return response()->json($this->formatDetail($expediente, $request->user()));
    }

    public function archivar(Request $request, Expediente $expediente)
    {
        $expediente = $this->expedienteService->archivar($expediente, $request->user());

        return response()->json($this->formatDetail($expediente, $request->user()));
    }

    private function formatListItem(Expediente $e): array
    {
        return [
            'id' => $e->id,
            'codigo' => $e->codigo,
            'asunto' => $e->asunto,
            'prioridad' => $e->prioridad,
            'estado' => $e->estado,
            'tipo' => $e->tipoDocumental?->nombre,
            'tipo_documental_id' => $e->tipo_documental_id,
            'unidad_origen' => $e->unidadOrigen?->nombre,
            'unidad_actual' => $e->unidadActual?->nombre,
            'antiguedad_dias' => $this->expedienteService->antiguedadDias($e),
            'created_at' => $e->created_at?->toIso8601String(),
            'updated_at' => $e->updated_at?->toIso8601String(),
        ];
    }

    private function formatDetail(Expediente $e, ?Usuario $usuario = null): array
    {
        $principal = $e->documentoPrincipal;

        $historial = $this->historialService->lineaDeTiempo($e)
            ->map(fn ($item) => $this->historialService->formatearNodo($item));

        $enUnidad = $usuario && $e->unidad_actual_id === $usuario->unidad_activa_id;

        return [
            'id' => $e->id,
            'codigo' => $e->codigo,
            'asunto' => $e->asunto,
            'prioridad' => $e->prioridad,
            'estado' => $e->estado,
            'tipo' => $e->tipoDocumental?->nombre,
            'tipo_documental_id' => $e->tipo_documental_id,
            'unidad_origen' => $e->unidadOrigen?->nombre,
            'oficina_actual' => $e->unidadActual?->nombre,
            'unidad_actual_id' => $e->unidad_actual_id,
            'detalles' => $e->asunto,
            'fecha_creacion' => $e->created_at?->format('d/m/Y'),
            'fecha_actualizacion' => $e->updated_at?->format('d/m/Y'),
            'antiguedad_dias' => $this->expedienteService->antiguedadDias($e),
            'anexos' => $e->adjuntos->map(fn ($a) => [
                'id' => $a->id,
                'nombre' => $a->nombre_archivo,
                'tipo' => str_contains($a->mime_type, 'pdf') ? 'pdf' : 'file',
            ]),
            'documento_principal' => $principal ? [
                'id' => $principal->id,
                'titulo' => $principal->titulo,
                'estado' => $principal->estado,
                'firmado' => $principal->estado === 'firmado',
            ] : null,
            'requiere_firma_derivar' => (bool) $e->tipoDocumental?->requiere_firma_antes_derivar,
            'historial' => $historial,
            'puede_recepcionar' => $enUnidad && $e->estado === 'por_recepcionar',
            'puede_firmar' => $enUnidad && $principal && $principal->estado !== 'firmado',
            'puede_archivar' => $enUnidad && in_array($e->estado, ['en_tramite', 'devuelto', 'registrado'], true),
        ];
    }
}
