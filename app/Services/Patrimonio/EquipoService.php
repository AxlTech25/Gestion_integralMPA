<?php

namespace App\Services\Patrimonio;

use App\Models\Equipo;
use App\Models\Usuario;
use App\Services\Core\AuditoriaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EquipoService
{
    public function __construct(private AuditoriaService $auditoria) {}

    public function registrar(Usuario $usuario, array $data): Equipo
    {
        return DB::transaction(function () use ($usuario, $data) {
            if (Equipo::where('codigo_patrimonial', $data['codigo_patrimonial'])->exists()) {
                $existente = Equipo::where('codigo_patrimonial', $data['codigo_patrimonial'])->first();
                throw ValidationException::withMessages([
                    'codigo_patrimonial' => [
                        'El código patrimonial ya existe: '.$existente->codigo_patrimonial,
                    ],
                ]);
            }

            $equipo = Equipo::create([
                ...$data,
                'registrado_por' => $usuario->id,
            ]);

            $this->auditoria->registrar('MOD-PAT-TI', 'registrar', 'equipo', $equipo->id, [
                'codigo_patrimonial' => $equipo->codigo_patrimonial,
            ], $usuario);

            return $equipo->load(['unidad', 'ultimaPrediccion']);
        });
    }

    public function actualizar(Equipo $equipo, Usuario $usuario, array $data): Equipo
    {
        return DB::transaction(function () use ($equipo, $usuario, $data) {
            if (
                isset($data['codigo_patrimonial'])
                && $data['codigo_patrimonial'] !== $equipo->codigo_patrimonial
                && Equipo::where('codigo_patrimonial', $data['codigo_patrimonial'])->exists()
            ) {
                throw ValidationException::withMessages([
                    'codigo_patrimonial' => ['El código patrimonial ya está en uso.'],
                ]);
            }

            $equipo->update($data);

            $this->auditoria->registrar('MOD-PAT-TI', 'actualizar', 'equipo', $equipo->id, null, $usuario);

            return $equipo->fresh(['unidad', 'fichaTecnica', 'ultimaPrediccion']);
        });
    }
}
