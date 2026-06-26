<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! DB::table('permisos')->where('codigo', 'pat.incidencia.reportar')->exists()) {
            DB::table('permisos')->insert([
                'codigo' => 'pat.incidencia.reportar',
                'modulo' => 'MOD-PAT-TI',
                'descripcion' => 'Reportar incidencias de soporte TI',
            ]);
        }
    }

    public function down(): void
    {
        $permisoId = DB::table('permisos')->where('codigo', 'pat.incidencia.reportar')->value('id');
        if ($permisoId) {
            DB::table('role_permiso')->where('permiso_id', $permisoId)->delete();
            DB::table('permisos')->where('id', $permisoId)->delete();
        }
    }
};
