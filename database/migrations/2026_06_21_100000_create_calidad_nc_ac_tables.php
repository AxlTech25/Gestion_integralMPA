<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('no_conformidades', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('anio');
            $table->unsignedInteger('secuencial');
            $table->string('codigo', 20)->unique();
            $table->string('proceso', 40);
            $table->string('severidad', 20)->default('moderada');
            $table->text('descripcion');
            $table->text('requisito_incumplido')->nullable();
            $table->text('evidencia')->nullable();
            $table->text('contencion')->nullable();
            $table->text('causa_raiz')->nullable();
            $table->string('estado', 30)->default('abierta');
            $table->foreignId('unidad_id')->nullable()->constrained('unidades_organizacionales');
            $table->foreignId('reportado_por')->constrained('usuarios');
            $table->foreignId('responsable_id')->nullable()->constrained('usuarios');
            $table->foreignId('expediente_id')->nullable()->constrained('expedientes')->nullOnDelete();
            $table->foreignId('incidencia_id')->nullable()->constrained('incidencias')->nullOnDelete();
            $table->boolean('requiere_ac')->default(false);
            $table->text('verificacion_eficacia')->nullable();
            $table->foreignId('verificada_por')->nullable()->constrained('usuarios');
            $table->timestamp('cerrada_at')->nullable();
            $table->timestamp('verificada_at')->nullable();
            $table->timestamps();
        });

        Schema::create('acciones_correctivas', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('anio');
            $table->unsignedInteger('secuencial');
            $table->string('codigo', 20)->unique();
            $table->foreignId('no_conformidad_id')->constrained('no_conformidades')->cascadeOnDelete();
            $table->text('causa_raiz')->nullable();
            $table->text('plan_acciones');
            $table->string('estado', 30)->default('abierta');
            $table->foreignId('responsable_id')->constrained('usuarios');
            $table->text('evidencia_implementacion')->nullable();
            $table->text('metodo_verificacion')->nullable();
            $table->string('resultado_verificacion', 20)->nullable();
            $table->timestamp('implementada_at')->nullable();
            $table->timestamp('cerrada_at')->nullable();
            $table->timestamps();
        });

        $permisos = [
            ['calidad.nc.consultar', 'MOD-CALIDAD', 'Consultar no conformidades y AC'],
            ['calidad.nc.reportar', 'MOD-CALIDAD', 'Reportar no conformidades'],
            ['calidad.nc.gestionar', 'MOD-CALIDAD', 'Gestionar NC, AC y cierre ISO'],
        ];

        foreach ($permisos as [$codigo, $modulo, $descripcion]) {
            if (! DB::table('permisos')->where('codigo', $codigo)->exists()) {
                DB::table('permisos')->insert([
                    'codigo' => $codigo,
                    'modulo' => $modulo,
                    'descripcion' => $descripcion,
                ]);
            }
        }

        $adminId = DB::table('roles')->where('codigo', 'ADMIN_SISTEMA')->value('id');
        if ($adminId) {
            foreach (DB::table('permisos')->where('modulo', 'MOD-CALIDAD')->pluck('id') as $permisoId) {
                DB::table('role_permiso')->insertOrIgnore([
                    'role_id' => $adminId,
                    'permiso_id' => $permisoId,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('acciones_correctivas');
        Schema::dropIfExists('no_conformidades');

        $permisoIds = DB::table('permisos')->where('modulo', 'MOD-CALIDAD')->pluck('id');
        DB::table('role_permiso')->whereIn('permiso_id', $permisoIds)->delete();
        DB::table('permisos')->where('modulo', 'MOD-CALIDAD')->delete();
    }
};
