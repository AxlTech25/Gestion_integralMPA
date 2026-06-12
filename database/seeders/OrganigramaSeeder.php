<?php

namespace Database\Seeders;

use App\Models\UnidadOrganizacional;
use Illuminate\Database\Seeder;

class OrganigramaSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['ORG-001', 'Concejo Municipal', 'politico', false, null, null],
            ['ORG-002', 'Alcaldía', 'ejecutivo', false, null, null],
            ['ORG-003', 'Gerencia Municipal', 'ejecutivo', true, null, null],
            ['ORG-004', 'Mesa de Concertación', 'comite', false, null, null],
            ['ORG-015', 'Oficina de Control Institucional (OCI)', 'unidad', false, 'ORG-002', 'ORG-002'],
            ['ORG-016', 'Gerencia de Desarrollo Urbano e Infraestructura', 'gerencia', true, null, 'ORG-003'],
            ['ORG-017', 'Gerencia de Servicios Públicos y Administración Tributaria', 'gerencia', true, null, 'ORG-003'],
            ['ORG-018', 'Gerencia de Desarrollo Social, Económico y Gestión Ambiental', 'gerencia', true, null, 'ORG-003'],
            ['ORG-019', 'Secretaría General', 'gerencia', true, null, 'ORG-003'],
            ['ORG-020', 'Gerencia de Asesoría Legal', 'gerencia', true, null, 'ORG-003'],
            ['ORG-021', 'Gerencia de Planeamiento y Presupuesto', 'gerencia', true, null, 'ORG-003'],
            ['ORG-022', 'Gerencia de Administración', 'gerencia', true, null, 'ORG-003'],
            ['ORG-048', 'Unidad de Trámite Documentario y Archivo', 'unidad', true, 'ORG-019', 'ORG-019'],
            ['ORG-051', 'Unidad de Planeamiento y Racionalización', 'unidad', true, 'ORG-021', 'ORG-021'],
            ['ORG-052', 'Unidad de Presupuesto', 'unidad', true, 'ORG-021', 'ORG-021'],
            ['ORG-056', 'Unidad de Abastecimiento', 'unidad', true, 'ORG-021', 'ORG-021'],
            ['ORG-057', 'Unidad de Contabilidad', 'unidad', true, 'ORG-021', 'ORG-021'],
            ['ORG-058', 'Unidad de Tesorería', 'unidad', true, 'ORG-021', 'ORG-021'],
            ['ORG-059', 'Unidad de Patrimonio', 'unidad', true, 'ORG-021', 'ORG-021'],
            ['ORG-061', 'Unidad de Tecnología de la Información y Sistemas (UTIS)', 'unidad', true, 'ORG-021', 'ORG-021'],
            ['ORG-038', 'Sub Gerencia de Desarrollo Económico', 'unidad', true, 'ORG-018', 'ORG-018'],
            ['ORG-039', 'Sub Gerencia de Desarrollo Social y Poblaciones Vulnerables', 'unidad', true, 'ORG-018', 'ORG-018'],
            ['ORG-046', 'Unidad de Gestión Ambiental', 'unidad', true, 'ORG-018', 'ORG-018'],
        ];

        $ids = [];

        foreach ($units as [$codigo, $nombre, $tipo, $derivacion, $gerenciaCodigo, $padreCodigo]) {
            $ids[$codigo] = UnidadOrganizacional::firstOrCreate(
                ['codigo_org' => $codigo],
                [
                    'nombre' => $nombre,
                    'tipo' => $tipo,
                    'permite_derivacion' => $derivacion,
                    'activa' => true,
                ]
            )->id;
        }

        foreach ($units as [$codigo, $nombre, $tipo, $derivacion, $gerenciaCodigo, $padreCodigo]) {
            UnidadOrganizacional::where('codigo_org', $codigo)->update([
                'gerencia_id' => $gerenciaCodigo ? $ids[$gerenciaCodigo] : null,
                'padre_id' => $padreCodigo ? $ids[$padreCodigo] : null,
            ]);
        }
    }
}
