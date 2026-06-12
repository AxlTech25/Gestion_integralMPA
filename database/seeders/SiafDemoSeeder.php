<?php

namespace Database\Seeders;

use App\Models\SiafEjecucionSnapshot;
use Illuminate\Database\Seeder;

class SiafDemoSeeder extends Seeder
{
    public function run(): void
    {
        SiafEjecucionSnapshot::updateOrCreate(
            ['periodo' => '2026'],
            [
                'pim' => 45200000.00,
                'ejecucion_total' => 29349600.00,
                'porcentaje_ejecucion' => 64.80,
                'es_simulacion' => true,
                'detalle_resumido_json' => [
                    'personal' => 42.5,
                    'servicios' => 18.2,
                    'inversion' => 15.5,
                ],
                'sincronizado_at' => now(),
            ]
        );
    }
}
