<?php

namespace App\Services\Core;

use App\Models\UnidadOrganizacional;
use App\Models\Usuario;
use App\Models\UsuarioTraslado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UsuarioAdminService
{
    public function __construct(private AuditoriaService $auditoria) {}

    public function crear(array $data, Usuario $registrador): Usuario
    {
        return DB::transaction(function () use ($data, $registrador) {
            $usuario = Usuario::create([
                'username' => $data['username'],
                'email' => $data['email'] ?? null,
                'password' => Hash::make($data['password']),
                'nombre_completo' => $data['nombre_completo'],
                'unidad_activa_id' => $data['unidad_activa_id'],
                'activo' => $data['activo'] ?? true,
            ]);

            if (! empty($data['role_ids'])) {
                $usuario->roles()->sync($data['role_ids']);
            }

            UsuarioTraslado::create([
                'usuario_id' => $usuario->id,
                'unidad_id' => $usuario->unidad_activa_id,
                'fecha_inicio' => now()->toDateString(),
                'registrado_por' => $registrador->id,
                'motivo' => 'Registro inicial',
            ]);

            $this->auditoria->registrar('NUCLEO', 'crear', 'usuario', $usuario->id, [
                'username' => $usuario->username,
            ], $registrador);

            return $usuario->load(['unidadActiva', 'roles']);
        });
    }

    public function actualizar(Usuario $usuario, array $data, Usuario $editor): Usuario
    {
        return DB::transaction(function () use ($usuario, $data, $editor) {
            $usuario->fill([
                'email' => $data['email'] ?? $usuario->email,
                'nombre_completo' => $data['nombre_completo'] ?? $usuario->nombre_completo,
                'activo' => $data['activo'] ?? $usuario->activo,
            ]);

            if (! empty($data['password'])) {
                $usuario->password = Hash::make($data['password']);
            }

            $usuario->save();

            if (isset($data['role_ids'])) {
                $usuario->roles()->sync($data['role_ids']);
            }

            $this->auditoria->registrar('NUCLEO', 'actualizar', 'usuario', $usuario->id, null, $editor);

            return $usuario->load(['unidadActiva', 'roles']);
        });
    }

    public function trasladar(Usuario $usuario, int $nuevaUnidadId, ?string $motivo, Usuario $registrador): Usuario
    {
        return DB::transaction(function () use ($usuario, $nuevaUnidadId, $motivo, $registrador) {
            $unidadOrigenId = $usuario->unidad_activa_id;

            if ($unidadOrigenId === $nuevaUnidadId) {
                throw ValidationException::withMessages([
                    'unidad_id' => ['El usuario ya pertenece a esa unidad.'],
                ]);
            }

            UsuarioTraslado::where('usuario_id', $usuario->id)
                ->whereNull('fecha_fin')
                ->update(['fecha_fin' => now()->toDateString()]);

            UsuarioTraslado::create([
                'usuario_id' => $usuario->id,
                'unidad_id' => $nuevaUnidadId,
                'fecha_inicio' => now()->toDateString(),
                'motivo' => $motivo,
                'registrado_por' => $registrador->id,
            ]);

            $usuario->update(['unidad_activa_id' => $nuevaUnidadId]);

            $this->auditoria->registrar('NUCLEO', 'trasladar', 'usuario', $usuario->id, [
                'unidad_origen_id' => $unidadOrigenId,
                'unidad_destino_id' => $nuevaUnidadId,
                'motivo' => $motivo,
            ], $registrador);

            return $usuario->load(['unidadActiva', 'roles']);
        });
    }
}
