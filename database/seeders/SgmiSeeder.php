<?php

namespace Database\Seeders;

use App\Models\UnidadOrganizacional;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SgmiSeeder extends Seeder
{
    public function run(): void
    {
        $gerencia = UnidadOrganizacional::create([
            'codigo_org' => 'ORG-021',
            'nombre' => 'Gerencia de Planeamiento y Presupuesto',
            'tipo' => 'gerencia',
            'permite_derivacion' => true,
            'activa' => true,
        ]);

        $utis = UnidadOrganizacional::create([
            'codigo_org' => 'ORG-061',
            'nombre' => 'Unidad de Tecnología de la Información y Sistemas',
            'tipo' => 'unidad',
            'permite_derivacion' => true,
            'gerencia_id' => $gerencia->id,
            'padre_id' => $gerencia->id,
            'activa' => true,
        ]);

        $roles = [
            ['ADMIN_SISTEMA', 'Administrador del sistema (UTIS)'],
            ['VISTA_EJECUTIVA', 'Vista ejecutiva'],
            ['GERENTE', 'Gerente de línea'],
            ['PATRIMONIO', 'Unidad de Patrimonio'],
            ['UTIS_SOPORTE', 'Soporte TI (UTIS)'],
            ['FINANZAS_SIAF', 'Acceso dashboard SIAF'],
            ['SECRETARIA_GENERAL', 'Secretaría General'],
            ['SUPERVISOR_UNIDAD', 'Supervisor de unidad'],
            ['OPERADOR', 'Operador'],
            ['AUDITOR_OCI', 'Auditor OCI'],
        ];

        foreach ($roles as [$codigo, $nombre]) {
            DB::table('roles')->insert([
                'codigo' => $codigo,
                'nombre' => $nombre,
            ]);
        }

        $admin = Usuario::create([
            'username' => 'admin.utis',
            'email' => 'utis@mpa.gob.pe',
            'password' => Hash::make('Admin@123'),
            'nombre_completo' => 'Administrador UTIS (desarrollo)',
            'unidad_activa_id' => $utis->id,
            'activo' => true,
        ]);

        $adminRoleId = DB::table('roles')->where('codigo', 'ADMIN_SISTEMA')->value('id');
        DB::table('usuario_role')->insert([
            'usuario_id' => $admin->id,
            'role_id' => $adminRoleId,
        ]);
    }
}
