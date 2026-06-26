<?php

namespace App\Services\Patrimonio;

use App\Models\Equipo;
use App\Models\Incidencia;
use App\Models\Usuario;
use App\Services\Core\AuditoriaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class IncidenciaService
{
    public function __construct(private AuditoriaService $auditoria) {}

    public function reportar(Equipo $equipo, Usuario $usuario, array $data): Incidencia
    {
        return DB::transaction(function () use ($equipo, $usuario, $data) {
            $this->validarEquipoParaReporte($usuario, $equipo);

            $incidencia = Incidencia::create([
                'equipo_id' => $equipo->id,
                'reportado_por' => $usuario->id,
                'tipo' => $data['tipo'],
                'descripcion' => $data['descripcion'],
                'estado' => 'abierta',
                'asignado_utis_id' => $usuario->hasPermiso('pat.incidencia.gestionar') ? $usuario->id : null,
                'created_at' => now(),
            ]);

            $this->auditoria->registrar('MOD-PAT-TI', 'incidencia_reportar', 'incidencia', $incidencia->id, null, $usuario);

            return $incidencia->load(['equipo.unidad', 'reportador']);
        });
    }

    public function actualizarEstado(Incidencia $incidencia, Usuario $usuario, array $data): Incidencia
    {
        return DB::transaction(function () use ($incidencia, $usuario, $data) {
            $updates = ['estado' => $data['estado']];

            if ($data['estado'] === 'en_atencion') {
                $updates['asignado_utis_id'] = $usuario->id;
            }

            if ($data['estado'] === 'cerrada') {
                $updates['solucion'] = $data['solucion'] ?? $incidencia->solucion;
                $updates['cerrada_at'] = now();

                if (empty($updates['solucion'])) {
                    throw ValidationException::withMessages([
                        'solucion' => ['La solución es obligatoria al cerrar la incidencia.'],
                    ]);
                }

                if (isset($data['estado_operativo_equipo'])) {
                    $incidencia->equipo->update(['estado_operativo' => $data['estado_operativo_equipo']]);
                }
            }

            $incidencia->update($updates);

            $this->auditoria->registrar('MOD-PAT-TI', 'incidencia_actualizar', 'incidencia', $incidencia->id, [
                'estado' => $data['estado'],
            ], $usuario);

            return $incidencia->fresh(['equipo.unidad', 'reportador', 'asignadoUtis']);
        });
    }

    public function validarEquipoParaReporte(Usuario $usuario, Equipo $equipo): void
    {
        if ($equipo->estado_operativo === 'baja') {
            throw ValidationException::withMessages([
                'equipo_id' => ['No se puede reportar sobre un equipo dado de baja.'],
            ]);
        }

        if ($usuario->hasPermiso('pat.incidencia.gestionar')) {
            return;
        }

        if (! $usuario->hasPermiso('pat.incidencia.reportar')) {
            throw ValidationException::withMessages([
                'equipo_id' => ['No tiene permiso para reportar incidencias.'],
            ]);
        }

        if ($equipo->unidad_id !== $usuario->unidad_activa_id) {
            throw ValidationException::withMessages([
                'equipo_id' => ['Solo puede reportar incidencias sobre equipos de su unidad.'],
            ]);
        }
    }
}
