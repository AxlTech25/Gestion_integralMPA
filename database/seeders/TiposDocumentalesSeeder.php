<?php

namespace Database\Seeders;

use App\Models\TipoDocumental;
use App\Models\UnidadOrganizacional;
use Illuminate\Database\Seeder;

class TiposDocumentalesSeeder extends Seeder
{
    public function run(): void
    {
        $org = fn (string $codigo) => UnidadOrganizacional::where('codigo_org', $codigo)->value('id');

        $tipos = [
            // Gestión interna
            ['MEM', 'Memorándum', 'MEM', 'gestion_interna', 'unidad', null, false, false],
            ['INF', 'Informe', 'INF', 'gestion_interna', 'unidad', null, false, false],
            ['SOL', 'Solicitud', 'SOL', 'gestion_interna', 'unidad', null, false, false],
            // Catálogo PA-29 — normas y documentos legales
            ['ACM', 'Acuerdo Municipal', 'ACM', 'acuerdo', 'concejo', 'ORG-001', true, true],
            ['ACR', 'Acuerdo Regional', 'ACR', 'acuerdo', 'concejo', 'ORG-001', true, true],
            ['ADC', 'Acuerdo de Concejo', 'ADC', 'acuerdo', 'concejo', 'ORG-001', true, true],
            ['ADCM', 'Acuerdo de Concejo Municipal', 'ADCM', 'acuerdo', 'concejo', 'ORG-001', true, true],
            ['DAL', 'Decreto de Alcaldía', 'DAL', 'decreto', 'alcaldia', 'ORG-002', true, true],
            ['DIR', 'Directiva', 'DIR', 'directiva', 'gerencia_municipal', 'ORG-003', true, true],
            ['OM', 'Ordenanza Municipal', 'OM', 'ordenanza', 'concejo', 'ORG-001', true, true],
            ['RES', 'Resolución', 'RES', 'resolucion', 'gerencia', null, false, true],
            ['RGA', 'Resolución General de Administración', 'RGA', 'resolucion', 'gerencia', 'ORG-022', false, true],
            ['RG', 'Resolución Gerencial', 'RG', 'resolucion', 'gerencia', null, false, true],
            ['RGGR', 'Resolución Gerencial General Regional', 'RGGR', 'resolucion', 'gerencia_municipal', 'ORG-003', true, true],
            ['RGDES', 'Resolución Gerencial de Desarrollo Económico', 'RGDES', 'resolucion', 'sub_gerencia', 'ORG-038', false, true],
            ['RGDSS', 'Resolución Gerencial de Desarrollo Social', 'RGDSS', 'resolucion', 'sub_gerencia', 'ORG-039', false, true],
            ['RGPPM', 'Resolución Gerencial de Planeamiento, Presupuesto y Modernización', 'RGPPM', 'resolucion', 'gerencia', 'ORG-021', false, true],
            ['RGRNMA', 'Resolución Gerencial de Recursos Naturales y Medio Ambiente', 'RGRNMA', 'resolucion', 'unidad', 'ORG-046', false, true],
            ['RSG', 'Resolución Sub Gerencial', 'RSG', 'resolucion', 'sub_gerencia', null, false, true],
            ['RAL', 'Resolución de Alcaldía', 'RAL', 'resolucion', 'alcaldia', 'ORG-002', true, true],
            ['RC', 'Resolución de Concejo', 'RC', 'resolucion', 'concejo', 'ORG-001', true, true],
            ['RCM', 'Resolución de Concejo Municipal', 'RCM', 'resolucion', 'concejo', 'ORG-001', true, true],
            ['RCONM', 'Resolución de Consejo Municipal', 'RCONM', 'resolucion', 'concejo', 'ORG-001', true, true],
            ['RGGEN', 'Resolución de Gerencia', 'RGGEN', 'resolucion', 'gerencia', null, false, true],
            ['RGM', 'Resolución de Gerencia Municipal', 'RGM', 'resolucion', 'gerencia_municipal', 'ORG-003', true, true],
            ['RGADM', 'Resolución de Gerencia de Administración', 'RGADM', 'resolucion', 'gerencia', 'ORG-022', false, true],
            ['RGGAS', 'Resolución de Gerencia de Gestión Ambiental y Servicios', 'RGGAS', 'resolucion', 'gerencia', 'ORG-018', false, true],
            ['RGR', 'Resolución de Gerencial Regional', 'RGR', 'resolucion', 'gerencia_municipal', 'ORG-003', true, true],
        ];

        foreach ($tipos as [$codigo, $nombre, $prefijo, $clase, $ambito, $orgCodigo, $secretaria, $requiereFirma]) {
            TipoDocumental::updateOrCreate(
                ['codigo' => $codigo],
                [
                    'nombre' => $nombre,
                    'prefijo_numeracion' => $prefijo,
                    'formato_display' => '{prefijo}-{anio}-{secuencial}',
                    'clase_norma' => $clase,
                    'ambito_emision' => $ambito,
                    'unidad_emisora_id' => $orgCodigo ? $org($orgCodigo) : null,
                    'registro_por_secretaria' => $secretaria,
                    'requiere_firma_antes_derivar' => $requiereFirma,
                    'requiere_recepcion' => true,
                    'activo' => true,
                ]
            );
        }
    }
}
