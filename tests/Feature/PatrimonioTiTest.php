<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\Usuario;
use Database\Seeders\OrganigramaSeeder;
use Database\Seeders\RolePermisoSeeder;
use Database\Seeders\SgmiSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatrimonioTiTest extends TestCase
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
        ]);

        $this->admin = Usuario::where('username', 'admin.utis')->firstOrFail();
    }

    public function test_registrar_equipo_via_api(): void
    {
        $unidadId = $this->admin->unidad_activa_id;

        $response = $this->actingAs($this->admin)->postJson('/api/equipos', [
            'codigo_patrimonial' => 'PC-TEST-001',
            'tipo_equipo' => 'pc',
            'marca' => 'HP',
            'modelo' => 'EliteDesk',
            'unidad_id' => $unidadId,
            'custodio_nombre' => 'Test Custodio',
            'valor_patrimonial' => 1500,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('equipos', ['codigo_patrimonial' => 'PC-TEST-001']);
    }

    public function test_ml_semaforo_endpoint(): void
    {
        Equipo::create([
            'codigo_patrimonial' => 'ML-TEST-01',
            'tipo_equipo' => 'pc',
            'marca' => 'Dell',
            'modelo' => 'Optiplex',
            'estado_operativo' => 'operativo',
            'unidad_id' => $this->admin->unidad_activa_id,
            'registrado_por' => $this->admin->id,
        ]);

        $this->actingAs($this->admin)->postJson('/api/ml/ejecutar')->assertOk();

        $response = $this->actingAs($this->admin)->getJson('/api/ml/semaforo');
        $response->assertOk();
        $response->assertJsonStructure(['verde', 'amarillo', 'rojo', 'total']);
    }
}
