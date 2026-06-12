<?php

namespace App\Services\Patrimonio;

use App\Models\Equipo;
use App\Models\FichaMantenimiento;
use App\Models\FichaTecnica;
use App\Models\Usuario;
use App\Services\Core\AuditoriaService;
use Illuminate\Support\Facades\DB;

class FichaService
{
    public function __construct(private AuditoriaService $auditoria) {}

    public function guardarFichaTecnica(Equipo $equipo, Usuario $usuario, array $data): FichaTecnica
    {
        return DB::transaction(function () use ($equipo, $usuario, $data) {
            $ficha = FichaTecnica::updateOrCreate(
                ['equipo_id' => $equipo->id],
                [
                    ...$data,
                    'registrado_por' => $usuario->id,
                ]
            );

            $this->auditoria->registrar('MOD-PAT-TI', 'ficha_tecnica', 'equipo', $equipo->id, null, $usuario);

            return $ficha;
        });
    }

    public function registrarMantenimiento(Equipo $equipo, Usuario $usuario, array $data): FichaMantenimiento
    {
        return DB::transaction(function () use ($equipo, $usuario, $data) {
            $ficha = FichaMantenimiento::create([
                'equipo_id' => $equipo->id,
                'tipo' => $data['tipo'],
                'fecha' => $data['fecha'],
                'descripcion' => $data['descripcion'],
                'resultado' => $data['resultado'] ?? null,
                'tecnico' => $data['tecnico'] ?? null,
                'registrado_por' => $usuario->id,
                'created_at' => now(),
            ]);

            $this->auditoria->registrar('MOD-PAT-TI', 'ficha_mantenimiento', 'equipo', $equipo->id, [
                'tipo' => $data['tipo'],
            ], $usuario);

            return $ficha;
        });
    }
}
