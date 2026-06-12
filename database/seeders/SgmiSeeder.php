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
        $utis = UnidadOrganizacional::where('codigo_org', 'ORG-061')->firstOrFail();

        $admin = Usuario::firstOrCreate(
            ['username' => 'admin.utis'],
            [
                'email' => 'utis@mpa.gob.pe',
                'password' => Hash::make('Admin@123'),
                'nombre_completo' => 'Administrador UTIS (desarrollo)',
                'unidad_activa_id' => $utis->id,
                'activo' => true,
            ]
        );

        $adminRoleId = DB::table('roles')->where('codigo', 'ADMIN_SISTEMA')->value('id');
        if ($adminRoleId) {
            DB::table('usuario_role')->updateOrInsert(
                ['usuario_id' => $admin->id, 'role_id' => $adminRoleId],
                ['usuario_id' => $admin->id, 'role_id' => $adminRoleId]
            );
        }
    }
}
