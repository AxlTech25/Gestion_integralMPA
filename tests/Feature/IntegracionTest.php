<?php

namespace Tests\Feature;

use App\Models\Equipo;
use App\Models\PersonalSigaReferencia;
use App\Models\SiafEjecucionSnapshot;
use App\Models\SyncLog;
use App\Models\UnidadOrganizacional;
use App\Models\Usuario;
use Database\Seeders\OrganigramaSeeder;
use Database\Seeders\RolePermisoSeeder;
use Database\Seeders\SgmiSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class IntegracionTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'integrations.siga.driver' => 'simulator',
            'integrations.siaf.driver' => 'simulator',
        ]);

        $this->seed([
            OrganigramaSeeder::class,
            RolePermisoSeeder::class,
            SgmiSeeder::class,
        ]);

        $this->admin = Usuario::where('username', 'admin.utis')->firstOrFail();
    }

    public function test_sync_siga_patrimonio_desde_simulador(): void
    {
        Artisan::call('sgmi:sync-siga', ['tipo' => 'patrimonio']);

        $this->assertGreaterThanOrEqual(20, Equipo::count());
        $this->assertDatabaseHas('sync_logs', [
            'sistema' => 'siga',
            'tipo_sync' => 'patrimonio',
            'estado' => 'ok',
        ]);

        $this->assertDatabaseHas('equipos', ['codigo_siga' => 'SIGA-EQ-001']);
        $this->assertEquals(22, Equipo::count());
    }

    public function test_sync_siga_organigrama_actualiza_codigo_siga(): void
    {
        Artisan::call('sgmi:sync-siga', ['tipo' => 'organigrama']);

        $utis = UnidadOrganizacional::where('codigo_org', 'ORG-061')->firstOrFail();
        $this->assertSame('SIGA-U-061', $utis->codigo_siga);

        $this->assertDatabaseHas('sync_logs', [
            'sistema' => 'siga',
            'tipo_sync' => 'organigrama',
        ]);
    }

    public function test_sync_siga_personal_crea_referencias(): void
    {
        Artisan::call('sgmi:sync-siga', ['tipo' => 'personal']);

        $this->assertGreaterThanOrEqual(8, PersonalSigaReferencia::count());
        $this->assertTrue(
            PersonalSigaReferencia::where('codigo_siga', 'SIGA-P-007')->where('desactivacion_sugerida', true)->exists()
        );
    }

    public function test_sync_siaf_crea_snapshot_simulado(): void
    {
        Artisan::call('sgmi:sync-siaf');

        $snapshot = SiafEjecucionSnapshot::query()->latest('id')->first();
        $this->assertNotNull($snapshot);
        $this->assertTrue($snapshot->es_simulacion);
        $this->assertGreaterThan(0, (float) $snapshot->pim);

        $this->assertDatabaseHas('sync_logs', [
            'sistema' => 'siaf',
            'tipo_sync' => 'ejecucion',
            'estado' => 'ok',
        ]);
    }

    public function test_api_sync_patrimonio_requiere_permiso(): void
    {
        $operadorRoleId = DB::table('roles')->where('codigo', 'OPERADOR')->value('id');
        $unidadId = UnidadOrganizacional::where('codigo_org', 'ORG-052')->value('id');

        $operador = Usuario::create([
            'username' => 'operador.int',
            'email' => 'operador.int@test.local',
            'password' => Hash::make('Operador@123'),
            'nombre_completo' => 'Operador sin permiso INT',
            'unidad_activa_id' => $unidadId,
            'activo' => true,
        ]);

        DB::table('usuario_role')->insert([
            'usuario_id' => $operador->id,
            'role_id' => $operadorRoleId,
        ]);

        $this->actingAs($operador)
            ->postJson('/api/integraciones/siga/patrimonio')
            ->assertForbidden();
    }

    public function test_api_sync_patrimonio_manual(): void
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/integraciones/siga/patrimonio');

        $response->assertOk()
            ->assertJsonPath('es_simulacion', true)
            ->assertJsonPath('registros_ok', 22);

        $this->assertSame(1, SyncLog::where('modo', 'manual')->where('tipo_sync', 'patrimonio')->count());
    }

    public function test_api_estado_integraciones(): void
    {
        $response = $this->actingAs($this->admin)->getJson('/api/integraciones/estado');

        $response->assertOk()
            ->assertJsonPath('siga.driver', 'simulator')
            ->assertJsonPath('siga.es_simulacion', true)
            ->assertJsonPath('siaf.es_simulacion', true);
    }
}
