<?php

namespace Tests\Feature;

use App\Models\Expediente;
use App\Models\TipoDocumental;
use App\Models\UnidadOrganizacional;
use App\Models\Usuario;
use Database\Seeders\OrganigramaSeeder;
use Database\Seeders\RolePermisoSeeder;
use Database\Seeders\SgmiSeeder;
use Database\Seeders\SiafDemoSeeder;
use Database\Seeders\TiposDocumentalesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;

    private Usuario $gerente;

    private Usuario $operador;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            OrganigramaSeeder::class,
            RolePermisoSeeder::class,
            TiposDocumentalesSeeder::class,
            SgmiSeeder::class,
            SiafDemoSeeder::class,
        ]);

        $this->admin = Usuario::where('username', 'admin.utis')->firstOrFail();

        $utisId = UnidadOrganizacional::where('codigo_org', 'ORG-061')->value('id');
        $presupuestoId = UnidadOrganizacional::where('codigo_org', 'ORG-052')->value('id');

        $this->gerente = Usuario::create([
            'username' => 'gerente.test',
            'email' => 'gerente@test.local',
            'password' => Hash::make('Gerente@123'),
            'nombre_completo' => 'Gerente de prueba',
            'unidad_activa_id' => $utisId,
            'activo' => true,
        ]);

        $gerenteRoleId = DB::table('roles')->where('codigo', 'GERENTE')->value('id');
        DB::table('usuario_role')->insert([
            'usuario_id' => $this->gerente->id,
            'role_id' => $gerenteRoleId,
        ]);

        $this->operador = Usuario::create([
            'username' => 'operador.dash',
            'email' => 'operador.dash@test.local',
            'password' => Hash::make('Operador@123'),
            'nombre_completo' => 'Operador dashboard',
            'unidad_activa_id' => $presupuestoId,
            'activo' => true,
        ]);

        $operadorRoleId = DB::table('roles')->where('codigo', 'OPERADOR')->value('id');
        DB::table('usuario_role')->insert([
            'usuario_id' => $this->operador->id,
            'role_id' => $operadorRoleId,
        ]);
    }

    public function test_dashboard_operativo_responde_kpis(): void
    {
        $response = $this->actingAs($this->admin)->getJson('/api/dashboard/operativo?dias=30');

        $response->assertOk();
        $response->assertJsonStructure([
            'periodo_dias',
            'alcance',
            'kpis' => ['pendientes', 'urgentes', 'por_recepcionar', 'promedio_dias', 'tramitados_hoy'],
            'actividad_reciente',
        ]);
        $response->assertJsonPath('alcance', 'institucional');
    }

    public function test_dashboard_operativo_incluye_tramitacion_por_unidad(): void
    {
        $this->crearExpedienteEnUnidad($this->gerente->unidad_activa_id);

        $response = $this->actingAs($this->gerente)->getJson('/api/dashboard/operativo?dias=30');

        $response->assertOk();
        $response->assertJsonStructure([
            'tramitacion' => [
                'por_unidad' => [['nombre', 'pendientes', 'promedio_dias', 'heightPct']],
                'por_gerencia',
            ],
        ]);
        $this->assertNotSame('institucional', $response->json('alcance'));
    }

    public function test_gerente_ve_solo_expedientes_de_su_gerencia(): void
    {
        $otraUnidadId = UnidadOrganizacional::where('codigo_org', 'ORG-048')->value('id');

        $this->crearExpedienteEnUnidad($this->gerente->unidad_activa_id);
        $this->crearExpedienteEnUnidad($otraUnidadId);

        $response = $this->actingAs($this->gerente)->getJson('/api/dashboard/operativo?dias=30');

        $response->assertOk();
        $this->assertSame(1, $response->json('kpis.pendientes'));
    }

    public function test_dashboard_estrategico_responde_consolidado(): void
    {
        $response = $this->actingAs($this->admin)->getJson('/api/dashboard/estrategico');

        $response->assertOk();
        $response->assertJsonStructure([
            'alcance',
            'kpis' => ['expedientes_pendientes', 'tramitados_hoy', 'meta_diaria_pct'],
            'tramitacion_gerencias',
            'semaforo_ti',
            'alertas_ti',
            'sugerencia',
        ]);
    }

    public function test_operador_no_accede_dashboard_estrategico(): void
    {
        $response = $this->actingAs($this->operador)->getJson('/api/dashboard/estrategico');

        $response->assertForbidden();
    }

    public function test_dashboard_operativo_incluye_siaf_simulado(): void
    {
        $finanzas = Usuario::create([
            'username' => 'finanzas.test',
            'email' => 'finanzas@test.local',
            'password' => Hash::make('Finanzas@123'),
            'nombre_completo' => 'Finanzas test',
            'unidad_activa_id' => UnidadOrganizacional::where('codigo_org', 'ORG-052')->value('id'),
            'activo' => true,
        ]);

        $roleId = DB::table('roles')->where('codigo', 'FINANZAS_SIAF')->value('id');
        DB::table('usuario_role')->insert([
            'usuario_id' => $finanzas->id,
            'role_id' => $roleId,
        ]);

        $response = $this->actingAs($finanzas)->getJson('/api/dashboard/operativo?dias=30');

        $response->assertOk();
        $response->assertJsonPath('siaf.es_simulacion', true);
        $response->assertJsonStructure(['siaf' => ['pim', 'porcentaje_ejecucion', 'sincronizado_at']]);
    }

    private function crearExpedienteEnUnidad(int $unidadId): Expediente
    {
        $tipo = TipoDocumental::where('codigo', 'MEM')->firstOrFail();

        return Expediente::create([
            'tipo_documental_id' => $tipo->id,
            'anio' => now()->year,
            'secuencial' => random_int(1000, 9999),
            'codigo' => 'MEM-'.now()->year.'-'.random_int(1000, 9999),
            'asunto' => 'Expediente test dashboard',
            'prioridad' => 'media',
            'unidad_origen_id' => $unidadId,
            'unidad_actual_id' => $unidadId,
            'estado' => 'en_tramite',
            'registrado_por' => $this->admin->id,
        ]);
    }
}
