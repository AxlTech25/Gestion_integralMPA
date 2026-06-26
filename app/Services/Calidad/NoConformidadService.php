<?php

namespace App\Services\Calidad;

use App\Models\AccionCorrectiva;
use App\Models\NoConformidad;
use App\Models\Usuario;
use App\Services\Core\AuditoriaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class NoConformidadService
{
    public function __construct(private AuditoriaService $auditoria) {}

    public function reportar(Usuario $usuario, array $data): NoConformidad
    {
        return DB::transaction(function () use ($usuario, $data) {
            $anio = (int) now()->year;
            $secuencial = $this->siguienteSecuencialNc($anio);
            $codigo = sprintf('NC-%d-%04d', $anio, $secuencial);

            $nc = NoConformidad::create([
                'anio' => $anio,
                'secuencial' => $secuencial,
                'codigo' => $codigo,
                'proceso' => $data['proceso'],
                'severidad' => $data['severidad'] ?? 'moderada',
                'descripcion' => $data['descripcion'],
                'requisito_incumplido' => $data['requisito_incumplido'] ?? null,
                'evidencia' => $data['evidencia'] ?? null,
                'contencion' => $data['contencion'] ?? null,
                'unidad_id' => $data['unidad_id'] ?? $usuario->unidad_activa_id,
                'reportado_por' => $usuario->id,
                'expediente_id' => $data['expediente_id'] ?? null,
                'incidencia_id' => $data['incidencia_id'] ?? null,
                'estado' => 'abierta',
                'requiere_ac' => false,
            ]);

            $this->auditoria->registrar('MOD-CALIDAD', 'nc_reportar', 'no_conformidad', $nc->id, [
                'codigo' => $codigo,
            ], $usuario);

            return $nc->load(['unidad', 'reportador', 'expediente']);
        });
    }

    public function actualizar(NoConformidad $nc, Usuario $usuario, array $data): NoConformidad
    {
        return DB::transaction(function () use ($nc, $usuario, $data) {
            if ($nc->estado === 'cerrada') {
                throw ValidationException::withMessages([
                    'estado' => ['La no conformidad ya está cerrada.'],
                ]);
            }

            $nc->update(collect($data)->only([
                'proceso', 'severidad', 'descripcion', 'requisito_incumplido', 'evidencia',
                'contencion', 'causa_raiz', 'estado', 'responsable_id', 'requiere_ac',
            ])->filter(fn ($v) => $v !== null)->all());

            $this->auditoria->registrar('MOD-CALIDAD', 'nc_actualizar', 'no_conformidad', $nc->id, null, $usuario);

            return $nc->fresh(['unidad', 'reportador', 'responsable', 'accionesCorrectivas.responsable']);
        });
    }

    public function cerrar(NoConformidad $nc, Usuario $usuario, array $data): NoConformidad
    {
        return DB::transaction(function () use ($nc, $usuario, $data) {
            if ($nc->requiere_ac && $nc->accionesCorrectivas()->where('estado', '!=', 'cerrada')->exists()) {
                throw ValidationException::withMessages([
                    'estado' => ['Debe cerrar la acción correctiva antes de cerrar la NC.'],
                ]);
            }

            $nc->update([
                'estado' => 'cerrada',
                'causa_raiz' => $data['causa_raiz'] ?? $nc->causa_raiz,
                'verificacion_eficacia' => $data['verificacion_eficacia'] ?? null,
                'verificada_por' => $usuario->id,
                'verificada_at' => now(),
                'cerrada_at' => now(),
            ]);

            $this->auditoria->registrar('MOD-CALIDAD', 'nc_cerrar', 'no_conformidad', $nc->id, null, $usuario);

            return $nc->fresh(['unidad', 'reportador', 'verificador', 'accionesCorrectivas']);
        });
    }

    public function crearAccionCorrectiva(NoConformidad $nc, Usuario $usuario, array $data): AccionCorrectiva
    {
        return DB::transaction(function () use ($nc, $usuario, $data) {
            if ($nc->estado === 'cerrada') {
                throw ValidationException::withMessages([
                    'no_conformidad_id' => ['No se puede abrir AC sobre una NC cerrada.'],
                ]);
            }

            $anio = (int) now()->year;
            $secuencial = $this->siguienteSecuencialAc($anio);
            $codigo = sprintf('AC-%d-%04d', $anio, $secuencial);

            $ac = AccionCorrectiva::create([
                'anio' => $anio,
                'secuencial' => $secuencial,
                'codigo' => $codigo,
                'no_conformidad_id' => $nc->id,
                'causa_raiz' => $data['causa_raiz'] ?? $nc->causa_raiz,
                'plan_acciones' => $data['plan_acciones'],
                'estado' => 'abierta',
                'responsable_id' => $data['responsable_id'] ?? $usuario->id,
            ]);

            $nc->update([
                'requiere_ac' => true,
                'estado' => 'con_ac',
                'causa_raiz' => $ac->causa_raiz,
            ]);

            $this->auditoria->registrar('MOD-CALIDAD', 'ac_crear', 'accion_correctiva', $ac->id, [
                'nc_codigo' => $nc->codigo,
            ], $usuario);

            return $ac->load(['responsable', 'noConformidad']);
        });
    }

    public function actualizarAccionCorrectiva(AccionCorrectiva $ac, Usuario $usuario, array $data): AccionCorrectiva
    {
        return DB::transaction(function () use ($ac, $usuario, $data) {
            $updates = collect($data)->only([
                'plan_acciones', 'estado', 'responsable_id', 'evidencia_implementacion',
                'metodo_verificacion', 'resultado_verificacion', 'causa_raiz',
            ])->filter(fn ($v) => $v !== null)->all();

            if (($data['estado'] ?? null) === 'en_implementacion' && ! $ac->implementada_at) {
                $updates['implementada_at'] = now();
            }

            if (($data['estado'] ?? null) === 'cerrada') {
                if (empty($data['resultado_verificacion']) && empty($ac->resultado_verificacion)) {
                    throw ValidationException::withMessages([
                        'resultado_verificacion' => ['Indique si la AC fue eficaz o ineficaz.'],
                    ]);
                }
                $updates['cerrada_at'] = now();
                $updates['resultado_verificacion'] = $data['resultado_verificacion'] ?? $ac->resultado_verificacion;

                if ($updates['resultado_verificacion'] === 'ineficaz') {
                    $updates['estado'] = 'ineficaz';
                }
            }

            $ac->update($updates);

            $this->auditoria->registrar('MOD-CALIDAD', 'ac_actualizar', 'accion_correctiva', $ac->id, [
                'estado' => $ac->estado,
            ], $usuario);

            return $ac->fresh(['responsable', 'noConformidad']);
        });
    }

    private function siguienteSecuencialNc(int $anio): int
    {
        $max = NoConformidad::where('anio', $anio)->max('secuencial');

        return ($max ?? 0) + 1;
    }

    private function siguienteSecuencialAc(int $anio): int
    {
        $max = AccionCorrectiva::where('anio', $anio)->max('secuencial');

        return ($max ?? 0) + 1;
    }
}
