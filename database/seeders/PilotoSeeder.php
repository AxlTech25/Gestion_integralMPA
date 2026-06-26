<?php

namespace Database\Seeders;

use App\Models\UnidadOrganizacional;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PilotoSeeder extends Seeder
{
    /** Contraseña temporal piloto — cambiar en producción. */
    public const PASSWORD = 'Piloto@2026';

    public function run(): void
    {
        $roles = DB::table('roles')->pluck('id', 'codigo');
        $unidad = fn (string $codigoOrg) => UnidadOrganizacional::where('codigo_org', $codigoOrg)->value('id');

        $usuarios = [
            ['operador.presupuesto', 'Operador Piloto — Presupuesto', 'operador.presupuesto@mpa.gob.pe', 'OPERADOR', 'ORG-052'],
            ['supervisor.presupuesto', 'Supervisor Piloto — Presupuesto', 'supervisor.presupuesto@mpa.gob.pe', 'SUPERVISOR_UNIDAD', 'ORG-052'],
            ['operador.tramite', 'Operador Piloto — Trámite Documentario', 'operador.tramite@mpa.gob.pe', 'OPERADOR', 'ORG-048'],
            ['gerente.planeamiento', 'Gerente Piloto — Planeamiento y Presupuesto', 'gerente.planeamiento@mpa.gob.pe', 'GERENTE', 'ORG-021'],
            ['finanzas.siaf', 'Finanzas Piloto — SIAF', 'finanzas.siaf@mpa.gob.pe', 'FINANZAS_SIAF', 'ORG-052'],
            ['patrimonio.unidad', 'Patrimonio Piloto', 'patrimonio.unidad@mpa.gob.pe', 'PATRIMONIO', 'ORG-059'],
            ['auditor.oci', 'Auditor Piloto — OCI', 'auditor.oci@mpa.gob.pe', 'AUDITOR_OCI', 'ORG-015'],
            ['vista.ejecutiva', 'Vista Ejecutiva Piloto — Gerencia Municipal', 'vista.ejecutiva@mpa.gob.pe', 'VISTA_EJECUTIVA', 'ORG-003'],
        ];

        foreach ($usuarios as [$username, $nombre, $email, $roleCodigo, $unidadOrg]) {
            $unidadId = $unidad($unidadOrg);
            if (! $unidadId) {
                $this->command?->warn("Unidad {$unidadOrg} no encontrada — omitiendo {$username}");

                continue;
            }

            $roleId = $roles[$roleCodigo] ?? null;
            if (! $roleId) {
                $this->command?->warn("Rol {$roleCodigo} no encontrado — omitiendo {$username}");

                continue;
            }

            $usuario = Usuario::firstOrCreate(
                ['username' => $username],
                [
                    'email' => $email,
                    'password' => Hash::make(self::PASSWORD),
                    'nombre_completo' => $nombre,
                    'unidad_activa_id' => $unidadId,
                    'activo' => true,
                ]
            );

            DB::table('usuario_role')->updateOrInsert(
                ['usuario_id' => $usuario->id, 'role_id' => $roleId],
                ['usuario_id' => $usuario->id, 'role_id' => $roleId]
            );
        }

        $this->command?->info('Usuarios piloto listos. Contraseña: '.self::PASSWORD);
    }
}
