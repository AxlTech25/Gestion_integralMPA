<?php

namespace App\Services\Core;

use App\Models\Usuario;

class MenuService
{
    public function __construct(private AccesoService $acceso) {}

    /** @return array<int, array<string, mixed>> */
    public function forUsuario(Usuario $usuario): array
    {
        $items = [
            [
                'key' => 'dashboard',
                'label' => 'Panel de Control',
                'icon' => 'dashboard',
                'route' => 'dashboard',
                'permission' => null,
            ],
            [
                'key' => 'dashboard-estrategico',
                'label' => 'Dashboard Estratégico',
                'icon' => 'analytics',
                'route' => 'dashboard-estrategico',
                'permission' => 'dash.estrategico.ver',
            ],
            [
                'key' => 'nucleo',
                'label' => 'Núcleo',
                'icon' => 'group',
                'route' => 'nucleo',
                'permission' => null,
                'any_permission' => [
                    'core.usuarios.gestionar',
                    'core.auditoria.consultar',
                ],
            ],
            [
                'key' => 'gestion-documental',
                'label' => 'Gestión Documental',
                'icon' => 'description',
                'route' => 'bandeja-pendientes',
                'requires_operacion_documentaria' => true,
            ],
            [
                'key' => 'patrimonio',
                'label' => 'Inventario IT',
                'icon' => 'inventory_2',
                'route' => 'patrimonio-inventario',
                'permission' => 'pat.equipo.consultar',
            ],
            [
                'key' => 'soporte-ti',
                'label' => 'Soporte TI',
                'icon' => 'build',
                'route' => 'patrimonio-incidencias',
                'permission' => 'pat.incidencia.reportar',
            ],
            [
                'key' => 'calidad',
                'label' => 'Calidad SGC',
                'icon' => 'verified',
                'route' => 'calidad',
                'any_permission' => [
                    'calidad.nc.consultar',
                    'calidad.nc.reportar',
                    'calidad.nc.gestionar',
                ],
            ],
            [
                'key' => 'integraciones',
                'label' => 'Integraciones',
                'icon' => 'sync',
                'route' => 'integraciones',
                'permission' => 'int.sync.ejecutar',
            ],
        ];

        return array_values(array_filter($items, function (array $item) use ($usuario) {
            if (! empty($item['requires_operacion_documentaria'])) {
                return $this->acceso->puedeOperarDocumentaria($usuario);
            }

            if (! empty($item['permission']) && $usuario->hasPermiso($item['permission'])) {
                return true;
            }

            if (! empty($item['any_permission']) && $usuario->hasAnyPermiso($item['any_permission'])) {
                return true;
            }

            if (empty($item['permission']) && empty($item['any_permission'])) {
                return true;
            }

            return false;
        }));
    }

    /** @return array<int, array<string, mixed>> */
    public function nucleoSubmenu(Usuario $usuario): array
    {
        $items = [
            [
                'key' => 'nucleo-usuarios',
                'label' => 'Usuarios',
                'route' => 'nucleo-usuarios',
                'permission' => 'core.usuarios.gestionar',
            ],
            [
                'key' => 'nucleo-unidades',
                'label' => 'Organigrama',
                'route' => 'nucleo-unidades',
                'permission' => 'core.usuarios.gestionar',
            ],
            [
                'key' => 'nucleo-auditoria',
                'label' => 'Auditoría',
                'route' => 'nucleo-auditoria',
                'permission' => 'core.auditoria.consultar',
            ],
        ];

        return array_values(array_filter($items, fn (array $item) => $usuario->hasPermiso($item['permission'])));
    }

    /** @return array<int, array<string, mixed>> */
    public function patrimonioSubmenu(Usuario $usuario): array
    {
        $items = [
            [
                'key' => 'pat-inventario',
                'label' => 'Inventario',
                'route' => 'patrimonio-inventario',
                'permission' => 'pat.equipo.consultar',
            ],
            [
                'key' => 'pat-incidencias',
                'label' => 'Incidencias',
                'route' => 'patrimonio-incidencias',
                'any_permission' => ['pat.incidencia.gestionar', 'pat.incidencia.reportar'],
            ],
            [
                'key' => 'pat-semaforo',
                'label' => 'Semáforo ML',
                'route' => 'patrimonio-semaforo',
                'permission' => 'pat.equipo.consultar',
            ],
        ];

        return array_values(array_filter($items, function (array $item) use ($usuario) {
            if (! empty($item['any_permission']) && $usuario->hasAnyPermiso($item['any_permission'])) {
                return true;
            }

            return empty($item['permission']) || $usuario->hasPermiso($item['permission']);
        }));
    }

    /** @return array<int, array<string, mixed>> */
    public function calidadSubmenu(Usuario $usuario): array
    {
        $items = [
            [
                'key' => 'calidad-hub',
                'label' => 'Inicio',
                'route' => 'calidad',
                'any_permission' => ['calidad.nc.consultar', 'calidad.nc.reportar', 'calidad.nc.gestionar'],
            ],
            [
                'key' => 'calidad-nc',
                'label' => 'No conformidades',
                'route' => 'calidad-no-conformidades',
                'any_permission' => ['calidad.nc.consultar', 'calidad.nc.reportar', 'calidad.nc.gestionar'],
            ],
        ];

        return array_values(array_filter($items, function (array $item) use ($usuario) {
            if (! empty($item['any_permission']) && $usuario->hasAnyPermiso($item['any_permission'])) {
                return true;
            }

            return empty($item['permission']) || $usuario->hasPermiso($item['permission']);
        }));
    }
}
