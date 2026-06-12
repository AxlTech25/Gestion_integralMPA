<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermisoSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = DB::table('permisos')->pluck('id', 'codigo');
        $roles = DB::table('roles')->pluck('id', 'codigo');

        $map = [
            'VISTA_EJECUTIVA' => [
                'dash.estrategico.ver',
                'dash.tramitacion.ver',
                'doc.expediente.consultar',
            ],
            'GERENTE' => [
                'dash.tramitacion.ver',
                'dash.estrategico.ver',
                'doc.expediente.consultar',
                'doc.expediente.derivar',
                'doc.expediente.devolver',
                'doc.expediente.recepcionar',
            ],
            'SECRETARIA_GENERAL' => [
                'doc.expediente.registrar',
                'doc.expediente.consultar',
                'doc.expediente.derivar',
                'doc.expediente.devolver',
                'doc.expediente.recepcionar',
                'doc.expediente.archivar',
                'doc.documento.firmar',
                'doc.tipos.gestionar',
                'dash.tramitacion.ver',
            ],
            'SUPERVISOR_UNIDAD' => [
                'doc.expediente.registrar',
                'doc.expediente.consultar',
                'doc.expediente.derivar',
                'doc.expediente.devolver',
                'doc.expediente.recepcionar',
                'doc.documento.firmar',
                'dash.tramitacion.ver',
            ],
            'OPERADOR' => [
                'doc.expediente.registrar',
                'doc.expediente.consultar',
                'doc.expediente.derivar',
                'doc.expediente.devolver',
                'doc.expediente.recepcionar',
                'doc.documento.firmar',
            ],
            'PATRIMONIO' => [
                'pat.equipo.registrar',
                'pat.equipo.consultar',
                'pat.ficha.gestionar',
                'doc.expediente.consultar',
            ],
            'UTIS_SOPORTE' => [
                'core.usuarios.gestionar',
                'pat.equipo.consultar',
                'pat.ficha.gestionar',
                'pat.incidencia.gestionar',
                'int.sync.ejecutar',
                'doc.expediente.consultar',
                'dash.estrategico.ver',
                'dash.tramitacion.ver',
                'dash.siaf.ver',
            ],
            'FINANZAS_SIAF' => [
                'dash.siaf.ver',
                'dash.tramitacion.ver',
                'doc.expediente.consultar',
            ],
            'AUDITOR_OCI' => [
                'core.auditoria.consultar',
            ],
        ];

        DB::table('role_permiso')->delete();

        $adminId = $roles['ADMIN_SISTEMA'] ?? null;
        if ($adminId) {
            foreach ($permisos as $codigo => $permisoId) {
                DB::table('role_permiso')->insertOrIgnore([
                    'role_id' => $adminId,
                    'permiso_id' => $permisoId,
                ]);
            }
        }

        foreach ($map as $roleCodigo => $permisoCodigos) {
            $roleId = $roles[$roleCodigo] ?? null;
            if (! $roleId) {
                continue;
            }

            foreach ($permisoCodigos as $codigo) {
                $permisoId = $permisos[$codigo] ?? null;
                if ($permisoId) {
                    DB::table('role_permiso')->insertOrIgnore([
                        'role_id' => $roleId,
                        'permiso_id' => $permisoId,
                    ]);
                }
            }
        }
    }
}
