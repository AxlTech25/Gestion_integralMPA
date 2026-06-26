<?php

namespace App\Services\Dashboard;

use App\Models\Equipo;
use App\Models\Expediente;
use App\Models\ExpedienteMovimiento;
use App\Models\MlPrediccion;
use App\Models\SiafEjecucionSnapshot;
use App\Models\UnidadOrganizacional;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardService
{
    public function operativo(Usuario $usuario, int $dias = 30): array
    {
        $desde = now()->subDays($dias);
        $query = $this->expedientesBase($usuario);

        $pendientes = (clone $query)
            ->whereIn('estado', ['registrado', 'por_recepcionar', 'en_tramite', 'devuelto'])
            ->count();

        $urgentes = (clone $query)
            ->whereIn('estado', ['registrado', 'por_recepcionar', 'en_tramite', 'devuelto'])
            ->where('prioridad', 'alta')
            ->count();

        $porRecepcionar = (clone $query)->where('estado', 'por_recepcionar')->count();

        $promedioDias = (clone $query)
            ->where('estado', '!=', 'archivado')
            ->where('created_at', '>=', $desde)
            ->get()
            ->avg(fn ($e) => $e->created_at->diffInDays(now())) ?? 0;

        $actividad = (clone $query)
            ->with(['unidadOrigen', 'tipoDocumental'])
            ->where('updated_at', '>=', $desde)
            ->orderByDesc('updated_at')
            ->limit(8)
            ->get()
            ->map(fn ($e) => [
                'id' => $e->id,
                'codigo' => $e->codigo,
                'asunto' => $e->asunto,
                'estado' => $e->estado,
                'unidad_origen' => $e->unidadOrigen?->nombre,
                'updated_at' => $e->updated_at?->toIso8601String(),
            ]);

        $siaf = $usuario->hasPermiso('dash.siaf.ver')
            ? $this->siafResumen()
            : null;

        $tramitacion = $usuario->hasPermiso('dash.tramitacion.ver')
            ? [
                'por_unidad' => $this->tramitacionPorUnidad($usuario, $dias)->values()->all(),
                'por_gerencia' => $this->tramitacionPorGerencia($usuario, $dias)->values()->all(),
            ]
            : null;

        return [
            'periodo_dias' => $dias,
            'alcance' => $this->alcanceLabel($usuario),
            'kpis' => [
                'pendientes' => $pendientes,
                'urgentes' => $urgentes,
                'por_recepcionar' => $porRecepcionar,
                'promedio_dias' => round($promedioDias, 1),
                'tramitados_hoy' => $this->tramitadosHoy($usuario),
            ],
            'actividad_reciente' => $actividad,
            'siaf' => $siaf,
            'tramitacion' => $tramitacion,
        ];
    }

    public function estrategico(Usuario $usuario, int $dias = 30): array
    {
        $desde = now()->subDays($dias);
        $query = $this->expedientesInstitucionales($usuario);

        $pendientes = (clone $query)
            ->whereIn('estado', ['registrado', 'por_recepcionar', 'en_tramite', 'devuelto'])
            ->count();

        $tramitadosHoy = ExpedienteMovimiento::query()
            ->whereDate('created_at', today())
            ->whereIn('tipo_movimiento', ['derivacion', 'recepcion', 'devolucion'])
            ->count();

        $tramitadosPeriodo = ExpedienteMovimiento::query()
            ->where('created_at', '>=', $desde)
            ->whereIn('tipo_movimiento', ['derivacion', 'recepcion', 'devolucion'])
            ->count();

        $metaDiaria = max(1, (int) round($tramitadosPeriodo / max(1, $dias)));
        $metaPct = min(100, (int) round(($tramitadosHoy / $metaDiaria) * 100));

        return [
            'periodo_dias' => $dias,
            'alcance' => $this->alcanceLabel($usuario),
            'kpis' => [
                'expedientes_pendientes' => $pendientes,
                'tramitados_hoy' => $tramitadosHoy,
                'meta_diaria_pct' => $metaPct,
                'tendencia_pendientes' => $this->tendenciaPendientes($pendientes),
            ],
            'tramitacion_gerencias' => $this->tramitacionPorGerencia($usuario, $dias),
            'semaforo_ti' => $this->semaforoTi(),
            'alertas_ti' => $this->alertasTi(),
            'sugerencia' => $this->sugerenciaEstrategica($usuario),
            'siaf' => $usuario->hasPermiso('dash.siaf.ver') ? $this->siafResumen() : null,
        ];
    }

    /** @return Collection<int, array<string, mixed>> */
    public function tramitacionPorUnidad(Usuario $usuario, int $dias = 30): Collection
    {
        $desde = now()->subDays($dias);
        $base = $this->expedientesInstitucionales($usuario)
            ->whereIn('expedientes.estado', ['registrado', 'por_recepcionar', 'en_tramite', 'devuelto'])
            ->where('expedientes.updated_at', '>=', $desde);

        $pendientes = (clone $base)
            ->join('unidades_organizacionales as ua', 'expedientes.unidad_actual_id', '=', 'ua.id')
            ->selectRaw('ua.id as unidad_id, ua.nombre as unidad, COUNT(*) as pendientes')
            ->groupBy('ua.id', 'ua.nombre')
            ->orderByDesc('pendientes')
            ->limit(8)
            ->get();

        $promedios = (clone $base)
            ->get(['unidad_actual_id', 'created_at'])
            ->groupBy('unidad_actual_id')
            ->map(fn ($grupo) => round($grupo->avg(fn ($e) => $e->created_at->diffInDays(now())), 1));

        $max = max(1, $pendientes->max('pendientes') ?? 1);
        $barClasses = ['bg-primary', 'bg-secondary', 'bg-primary/60', 'bg-secondary/70', 'bg-tertiary', 'bg-primary/40'];

        return $pendientes->map(function ($row, $idx) use ($max, $promedios, $barClasses) {
            return [
                'unidad_id' => (int) $row->unidad_id,
                'nombre' => $this->abreviarUnidad($row->unidad),
                'pendientes' => (int) $row->pendientes,
                'promedio_dias' => (float) ($promedios[$row->unidad_id] ?? 0),
                'heightPct' => (int) round(($row->pendientes / $max) * 100),
                'barClass' => $barClasses[$idx % count($barClasses)],
            ];
        });
    }

    /** @return Collection<int, array<string, mixed>> */
    public function tramitacionPorGerencia(Usuario $usuario, int $dias = 30): Collection
    {
        $desde = now()->subDays($dias);

        $query = $this->expedientesInstitucionales($usuario)
            ->join('unidades_organizacionales as ua', 'expedientes.unidad_actual_id', '=', 'ua.id')
            ->leftJoin('unidades_organizacionales as g', 'ua.gerencia_id', '=', 'g.id')
            ->where('expedientes.estado', '!=', 'archivado')
            ->where('expedientes.updated_at', '>=', $desde);

        $rows = $query
            ->selectRaw('COALESCE(g.nombre, ua.nombre) as gerencia, COUNT(*) as total')
            ->groupBy('g.id', 'g.nombre', 'ua.nombre')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $max = max(1, $rows->max('total') ?? 1);

        return $rows->map(function ($row) use ($max) {
            $barClasses = ['bg-primary', 'bg-secondary', 'bg-primary/60', 'bg-secondary/70', 'bg-tertiary', 'bg-primary/40'];
            $idx = crc32($row->gerencia) % count($barClasses);

            return [
                'nombre' => $this->abreviarGerencia($row->gerencia),
                'valor' => (int) $row->total,
                'heightPct' => (int) round(($row->total / $max) * 100),
                'barClass' => $barClasses[$idx],
            ];
        });
    }

    public function alertasTi(): array
    {
        $predicciones = MlPrediccion::query()
            ->select('ml_predicciones.*')
            ->with(['equipo.unidad.gerencia'])
            ->joinSub(
                MlPrediccion::query()
                    ->selectRaw('equipo_id, MAX(calculado_at) as max_calc')
                    ->groupBy('equipo_id'),
                'ult',
                function ($join) {
                    $join->on('ml_predicciones.equipo_id', '=', 'ult.equipo_id')
                        ->on('ml_predicciones.calculado_at', '=', 'ult.max_calc');
                }
            )
            ->whereIn('nivel_riesgo', ['rojo', 'amarillo'])
            ->orderByDesc('probabilidad_falla')
            ->limit(8)
            ->get();

        return $predicciones->map(function (MlPrediccion $p) {
            $equipo = $p->equipo;
            $color = $p->nivel_riesgo === 'rojo' ? 'error' : 'secondary';
            $estado = $p->nivel_riesgo === 'rojo' ? 'CRÍTICO' : 'ALERTA';

            return [
                'id' => $p->id,
                'equipo_id' => $p->equipo_id,
                'equipo' => $equipo?->codigo_patrimonial ?? '—',
                'estado' => $estado,
                'color' => $color,
                'gerencia' => $equipo?->unidad?->gerencia?->nombre ?? $equipo?->unidad?->nombre ?? '—',
                'probabilidad' => (float) $p->probabilidad_falla,
                'accion' => 'Ver equipo',
            ];
        })->all();
    }

    public function semaforoTi(): array
    {
        $ultimas = MlPrediccion::query()
            ->select('ml_predicciones.*')
            ->joinSub(
                MlPrediccion::query()
                    ->selectRaw('equipo_id, MAX(calculado_at) as max_calc')
                    ->groupBy('equipo_id'),
                'ult',
                function ($join) {
                    $join->on('ml_predicciones.equipo_id', '=', 'ult.equipo_id')
                        ->on('ml_predicciones.calculado_at', '=', 'ult.max_calc');
                }
            )
            ->get();

        return [
            'verde' => $ultimas->where('nivel_riesgo', 'verde')->count(),
            'amarillo' => $ultimas->where('nivel_riesgo', 'amarillo')->count(),
            'rojo' => $ultimas->where('nivel_riesgo', 'rojo')->count(),
            'total' => $ultimas->count(),
            'sin_datos' => Equipo::where('estado_operativo', '!=', 'baja')->count() - $ultimas->count(),
        ];
    }

    public function siafResumen(): ?array
    {
        $snapshot = SiafEjecucionSnapshot::query()->orderByDesc('sincronizado_at')->first();

        if (! $snapshot) {
            return null;
        }

        return [
            'periodo' => $snapshot->periodo,
            'pim' => (float) $snapshot->pim,
            'ejecucion_total' => (float) $snapshot->ejecucion_total,
            'porcentaje_ejecucion' => (float) $snapshot->porcentaje_ejecucion,
            'es_simulacion' => $snapshot->es_simulacion,
            'sincronizado_at' => $snapshot->sincronizado_at?->toIso8601String(),
            'detalle' => $snapshot->detalle_resumido_json,
        ];
    }

    private function expedientesBase(Usuario $usuario)
    {
        $query = Expediente::query();

        if ($this->vistaInstitucional($usuario)) {
            return $query;
        }

        $unidadIds = $this->unidadesVisibles($usuario);
        if ($unidadIds->isEmpty()) {
            return $query->where('unidad_actual_id', $usuario->unidad_activa_id);
        }

        return $query->whereIn('unidad_actual_id', $unidadIds);
    }

    private function expedientesInstitucionales(Usuario $usuario)
    {
        if ($this->vistaInstitucional($usuario)) {
            return Expediente::query();
        }

        return $this->expedientesBase($usuario);
    }

    private function vistaInstitucional(Usuario $usuario): bool
    {
        return $usuario->hasRole('VISTA_EJECUTIVA')
            || $usuario->hasRole('ADMIN_SISTEMA')
            || $usuario->hasPermiso('core.usuarios.gestionar');
    }

    private function alcanceLabel(Usuario $usuario): string
    {
        if ($this->vistaInstitucional($usuario)) {
            return 'institucional';
        }

        $usuario->loadMissing('unidadActiva.gerencia');

        return $usuario->unidadActiva?->gerencia?->nombre
            ?? $usuario->unidadActiva?->nombre
            ?? 'unidad';
    }

    /** @return Collection<int, int> */
    private function unidadesVisibles(Usuario $usuario): Collection
    {
        $usuario->loadMissing('unidadActiva');
        $unidad = $usuario->unidadActiva;

        if (! $unidad) {
            return collect();
        }

        if ($usuario->hasPermiso('dash.tramitacion.ver') && $unidad->gerencia_id) {
            return UnidadOrganizacional::query()
                ->where('gerencia_id', $unidad->gerencia_id)
                ->orWhere('id', $unidad->gerencia_id)
                ->pluck('id');
        }

        return collect([$unidad->id]);
    }

    private function tramitadosHoy(Usuario $usuario): int
    {
        $query = ExpedienteMovimiento::query()
            ->whereDate('created_at', today())
            ->whereIn('tipo_movimiento', ['derivacion', 'recepcion', 'devolucion']);

        if (! $this->vistaInstitucional($usuario)) {
            $unidadIds = $this->unidadesVisibles($usuario);
            $query->whereHas('expediente', fn ($q) => $q->whereIn('unidad_actual_id', $unidadIds));
        }

        return $query->count();
    }

    private function tendenciaPendientes(int $actual): string
    {
        $ayer = Expediente::query()
            ->whereIn('estado', ['registrado', 'por_recepcionar', 'en_tramite', 'devuelto'])
            ->where('updated_at', '<', today())
            ->count();

        if ($ayer === 0) {
            return 'Sin variación';
        }

        $diff = $actual - $ayer;
        $pct = round(($diff / $ayer) * 100, 1);

        return $diff >= 0 ? "+{$pct}% vs. ayer" : "{$pct}% vs. ayer";
    }

    private function sugerenciaEstrategica(?Usuario $usuario = null): array
    {
        $critico = $this->alertasTi();
        $top = $critico[0] ?? null;

        if ($top && $top['color'] === 'error') {
            return [
                'titulo' => 'Mantenimiento prioritario TI',
                'texto' => "El equipo {$top['equipo']} en {$top['gerencia']} presenta riesgo crítico (".round($top['probabilidad'] * 100).'%). Priorice revisión preventiva.',
                'equipo_id' => $top['equipo_id'],
            ];
        }

        $gerenciaQuery = $usuario
            ? $this->expedientesInstitucionales($usuario)
            : Expediente::query();

        $gerenciaTop = $gerenciaQuery
            ->join('unidades_organizacionales as ua', 'expedientes.unidad_actual_id', '=', 'ua.id')
            ->leftJoin('unidades_organizacionales as g', 'ua.gerencia_id', '=', 'g.id')
            ->where('expedientes.estado', '!=', 'archivado')
            ->where('expedientes.updated_at', '>=', now()->subDays(30))
            ->selectRaw('COALESCE(g.nombre, ua.nombre) as gerencia, COUNT(*) as total')
            ->groupBy('g.id', 'g.nombre', 'ua.nombre')
            ->orderByDesc('total')
            ->first();

        if ($gerenciaTop) {
            $nombre = $this->abreviarGerencia($gerenciaTop->gerencia);

            return [
                'titulo' => 'Cuello de botella documental',
                'texto' => "{$nombre} concentra {$gerenciaTop->total} expedientes activos en el periodo. Revise derivaciones pendientes.",
                'equipo_id' => null,
            ];
        }

        return [
            'titulo' => 'Operación estable',
            'texto' => 'No se detectan alertas críticas en tramitación ni infraestructura TI.',
            'equipo_id' => null,
        ];
    }

    private function abreviarGerencia(string $nombre): string
    {
        $map = [
            'Gerencia de Desarrollo Urbano e Infraestructura' => 'Desarrollo Urbano',
            'Gerencia de Planeamiento y Presupuesto' => 'Planeamiento',
            'Gerencia de Administración' => 'Administración',
            'Gerencia Municipal' => 'Gerencia Municipal',
        ];

        return $map[$nombre] ?? (strlen($nombre) > 22 ? substr($nombre, 0, 20).'…' : $nombre);
    }

    private function abreviarUnidad(string $nombre): string
    {
        $map = [
            'Unidad de Tecnología de la Información y Sistemas (UTIS)' => 'UTIS',
            'Unidad de Trámite Documentario y Archivo' => 'Trámite Documentario',
            'Unidad de Presupuesto' => 'Presupuesto',
            'Unidad de Tesorería' => 'Tesorería',
            'Unidad de Contabilidad' => 'Contabilidad',
            'Unidad de Patrimonio' => 'Patrimonio',
        ];

        return $map[$nombre] ?? (strlen($nombre) > 24 ? substr($nombre, 0, 22).'…' : $nombre);
    }
}
