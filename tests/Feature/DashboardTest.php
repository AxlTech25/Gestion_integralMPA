<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Database\Seeders\OrganigramaSeeder;
use Database\Seeders\RolePermisoSeeder;
use Database\Seeders\SgmiSeeder;
use Database\Seeders\SiafDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            OrganigramaSeeder::class,
            RolePermisoSeeder::class,
            SgmiSeeder::class,
            SiafDemoSeeder::class,
        ]);

        $this->admin = Usuario::where('username', 'admin.utis')->firstOrFail();
    }

    public function test_dashboard_operativo_responde_kpis(): void
    {
        $response = $this->actingAs($this->admin)->getJson('/api/dashboard/operativo?dias=30');

        $response->assertOk();
        $response->assertJsonStructure([
            'periodo_dias',
            'kpis' => ['pendientes', 'urgentes', 'por_recepcionar', 'promedio_dias', 'tramitados_hoy'],
            'actividad_reciente',
        ]);
    }

    public function test_dashboard_estrategico_responde_consolidado(): void
    {
        $response = $this->actingAs($this->admin)->getJson('/api/dashboard/estrategico');

        $response->assertOk();
        $response->assertJsonStructure([
            'kpis' => ['expedientes_pendientes', 'tramitados_hoy', 'meta_diaria_pct'],
            'tramitacion_gerencias',
            'semaforo_ti',
            'alertas_ti',
            'sugerencia',
        ]);
    }
}
