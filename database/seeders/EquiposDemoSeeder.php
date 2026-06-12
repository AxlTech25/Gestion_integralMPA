<?php

namespace Database\Seeders;

use App\Models\Equipo;
use App\Models\FichaMantenimiento;
use App\Models\FichaTecnica;
use App\Models\Incidencia;
use App\Models\UnidadOrganizacional;
use App\Models\Usuario;
use App\Services\Patrimonio\MlPredictionService;
use Illuminate\Database\Seeder;

class EquiposDemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Usuario::where('username', 'admin.utis')->first();
        if (! $admin) {
            return;
        }

        $utis = UnidadOrganizacional::where('codigo_org', 'ORG-061')->first();
        $patrimonio = UnidadOrganizacional::where('codigo_org', 'ORG-059')->first();
        $planeamiento = UnidadOrganizacional::where('codigo_org', 'ORG-021')->first();

        $equipos = [
            ['PC-UTIS-001', 'pc', 'HP', 'ProDesk 400', $utis?->id, 'operativo', 'Jefe UTIS', 'Unidad de TI', 3200.00, 3, 16, 512, 2],
            ['SRV-BACKUP-01', 'servidor', 'Dell', 'PowerEdge T340', $utis?->id, 'operativo', 'Admin UTIS', 'Responsable sistemas', 18500.00, 5, 32, 2048, 4],
            ['PC-PRES-01', 'pc', 'Lenovo', 'ThinkCentre M70q', $planeamiento?->id, 'reparacion', 'Secretario GM', 'Gerencia Municipal', 2800.00, 4, 8, 256, 5],
            ['IMP-CONT-02', 'impresora', 'Epson', 'L3250', $patrimonio?->id, 'operativo', 'Auxiliar contable', 'Contabilidad', 450.00, 2, null, null, 1],
        ];

        foreach ($equipos as [$codigo, $tipo, $marca, $modelo, $unidadId, $estado, $custodio, $cargo, $valor, $antiguedad, $ram, $disk, $incidencias]) {
            if (! $unidadId) {
                continue;
            }

            $equipo = Equipo::updateOrCreate(
                ['codigo_patrimonial' => $codigo],
                [
                    'codigo_siga' => 'SIGA-'.$codigo,
                    'tipo_equipo' => $tipo,
                    'marca' => $marca,
                    'modelo' => $modelo,
                    'numero_serie' => 'SN-'.substr($codigo, -3),
                    'estado_operativo' => $estado,
                    'unidad_id' => $unidadId,
                    'custodio_nombre' => $custodio,
                    'custodio_cargo' => $cargo,
                    'valor_patrimonial' => $valor,
                    'fecha_adquisicion' => now()->subYears($antiguedad)->subMonths(2),
                    'registrado_por' => $admin->id,
                ]
            );

            if ($ram) {
                FichaTecnica::updateOrCreate(
                    ['equipo_id' => $equipo->id],
                    [
                        'cpu' => 'Intel Core i5',
                        'ram_gb' => $ram,
                        'almacenamiento_gb' => $disk,
                        'sistema_operativo' => 'Windows 11 Pro',
                        'red' => 'Ethernet / Wi-Fi',
                        'antiguedad_anios' => $antiguedad,
                        'registrado_por' => $admin->id,
                    ]
                );
            }

            FichaMantenimiento::firstOrCreate(
                [
                    'equipo_id' => $equipo->id,
                    'fecha' => now()->subMonths(3)->toDateString(),
                    'tipo' => 'preventivo',
                ],
                [
                    'descripcion' => 'Limpieza y actualización de software base',
                    'resultado' => 'Operativo',
                    'tecnico' => 'UTIS',
                    'registrado_por' => $admin->id,
                    'created_at' => now(),
                ]
            );

            if ($incidencias > 0) {
                Incidencia::firstOrCreate(
                    [
                        'equipo_id' => $equipo->id,
                        'descripcion' => 'Incidencia demo: lentitud o falla reportada en '.$codigo,
                    ],
                    [
                        'reportado_por' => $admin->id,
                        'tipo' => $estado === 'reparacion' ? 'averia' : 'falla',
                        'estado' => $estado === 'reparacion' ? 'en_atencion' : 'abierta',
                        'asignado_utis_id' => $admin->id,
                        'created_at' => now()->subDays(10),
                    ]
                );
            }
        }

        app(MlPredictionService::class)->ejecutarBatch();
    }
}
