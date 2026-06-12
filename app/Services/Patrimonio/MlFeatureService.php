<?php

namespace App\Services\Patrimonio;

use App\Models\Equipo;
use App\Models\FichaMantenimiento;
use App\Models\Incidencia;
use Carbon\Carbon;

class MlFeatureService
{
    /** @return array<string, mixed> */
    public function forEquipo(Equipo $equipo): array
    {
        $equipo->loadMissing(['fichaTecnica', 'fichasMantenimiento']);

        $desde = now()->subMonths(12);
        $incidencias12m = Incidencia::where('equipo_id', $equipo->id)
            ->where('created_at', '>=', $desde)
            ->count();

        $correctivos12m = FichaMantenimiento::where('equipo_id', $equipo->id)
            ->where('tipo', 'correctivo')
            ->where('fecha', '>=', $desde)
            ->count();

        $ultimoMantenimiento = FichaMantenimiento::where('equipo_id', $equipo->id)
            ->orderByDesc('fecha')
            ->value('fecha');

        $diasDesdeMantenimiento = $ultimoMantenimiento
            ? Carbon::parse($ultimoMantenimiento)->diffInDays(now())
            : 365;

        $ficha = $equipo->fichaTecnica;
        $antiguedad = $ficha?->antiguedad_anios ?? ($equipo->fecha_adquisicion
            ? $equipo->fecha_adquisicion->diffInYears(now())
            : null);

        $sinDatos = ! $ficha && $incidencias12m === 0 && $correctivos12m === 0 && ! $ultimoMantenimiento;

        return [
            'antiguedad_anios' => $antiguedad !== null ? (float) $antiguedad : 0,
            'ram_gb' => $ficha?->ram_gb ?? 0,
            'almacenamiento_gb' => $ficha?->almacenamiento_gb ?? 0,
            'incidencias_12m' => $incidencias12m,
            'mantenimientos_correctivos_12m' => $correctivos12m,
            'dias_desde_ultimo_mantenimiento' => $diasDesdeMantenimiento,
            'estado_operativo' => $equipo->estado_operativo,
            'tipo_equipo' => $equipo->tipo_equipo,
            'sin_datos_suficientes' => $sinDatos,
        ];
    }
}
