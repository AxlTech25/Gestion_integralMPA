<?php

namespace Tests\Feature;

use App\Models\AuditoriaLog;
use App\Models\Equipo;
use App\Models\Incidencia;
use App\Models\UnidadOrganizacional;
use App\Models\Usuario;
use Database\Seeders\OrganigramaSeeder;
use Database\Seeders\RolePermisoSeeder;
use Database\Seeders\SgmiSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PatrimonioTiTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;

    private Usuario $operador;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            OrganigramaSeeder::class,
            RolePermisoSeeder::class,
            SgmiSeeder::class,
        ]);

        $this->admin = Usuario::where('username', 'admin.utis')->firstOrFail();

        $utisId = UnidadOrganizacional::where('codigo_org', 'ORG-061')->value('id');
        $this->operador = Usuario::create([
            'username' => 'operador.test',
            'email' => 'operador@test.local',
            'password' => Hash::make('Operador@123'),
            'nombre_completo' => 'Operador de prueba',
            'unidad_activa_id' => $utisId,
            'activo' => true,
        ]);

        $operadorRoleId = DB::table('roles')->where('codigo', 'OPERADOR')->value('id');
        DB::table('usuario_role')->insert([
            'usuario_id' => $this->operador->id,
            'role_id' => $operadorRoleId,
        ]);
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

    public function test_operador_reporta_incidencia_en_su_unidad(): void
    {
        $equipo = Equipo::create([
            'codigo_patrimonial' => 'PC-OP-001',
            'tipo_equipo' => 'pc',
            'marca' => 'Lenovo',
            'modelo' => 'ThinkCentre',
            'estado_operativo' => 'operativo',
            'unidad_id' => $this->operador->unidad_activa_id,
            'registrado_por' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->operador)->postJson('/api/incidencias', [
            'equipo_id' => $equipo->id,
            'tipo' => 'falla',
            'descripcion' => 'El equipo no enciende desde hoy.',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('incidencias', [
            'equipo_id' => $equipo->id,
            'reportado_por' => $this->operador->id,
            'estado' => 'abierta',
        ]);
    }

    public function test_operador_no_reporta_equipo_de_otra_unidad(): void
    {
        $otraUnidadId = UnidadOrganizacional::where('codigo_org', 'ORG-021')->value('id');

        $equipo = Equipo::create([
            'codigo_patrimonial' => 'PC-OTRA-001',
            'tipo_equipo' => 'pc',
            'marca' => 'Dell',
            'modelo' => 'Optiplex',
            'estado_operativo' => 'operativo',
            'unidad_id' => $otraUnidadId,
            'registrado_por' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->operador)->postJson('/api/incidencias', [
            'equipo_id' => $equipo->id,
            'tipo' => 'requerimiento',
            'descripcion' => 'Solicito instalación de software.',
        ]);

        $response->assertStatus(422);
    }

    public function test_operador_ve_solo_sus_incidencias(): void
    {
        $equipo = Equipo::create([
            'codigo_patrimonial' => 'PC-HIST-01',
            'tipo_equipo' => 'pc',
            'marca' => 'HP',
            'modelo' => 'ProDesk',
            'estado_operativo' => 'operativo',
            'unidad_id' => $this->operador->unidad_activa_id,
            'registrado_por' => $this->admin->id,
        ]);

        Incidencia::create([
            'equipo_id' => $equipo->id,
            'reportado_por' => $this->operador->id,
            'tipo' => 'falla',
            'descripcion' => 'Incidencia del operador',
            'estado' => 'abierta',
            'created_at' => now(),
        ]);

        Incidencia::create([
            'equipo_id' => $equipo->id,
            'reportado_por' => $this->admin->id,
            'tipo' => 'averia',
            'descripcion' => 'Incidencia de admin',
            'estado' => 'abierta',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->operador)->getJson('/api/incidencias');

        $response->assertOk();
        $this->assertCount(1, $response->json());
        $this->assertSame('Incidencia del operador', $response->json()[0]['descripcion']);
    }

    public function test_operador_no_puede_cerrar_incidencia(): void
    {
        $equipo = Equipo::create([
            'codigo_patrimonial' => 'PC-CERRAR-01',
            'tipo_equipo' => 'pc',
            'marca' => 'HP',
            'modelo' => 'EliteDesk',
            'estado_operativo' => 'operativo',
            'unidad_id' => $this->operador->unidad_activa_id,
            'registrado_por' => $this->admin->id,
        ]);

        $incidencia = Incidencia::create([
            'equipo_id' => $equipo->id,
            'reportado_por' => $this->operador->id,
            'tipo' => 'falla',
            'descripcion' => 'Pantalla sin señal',
            'estado' => 'abierta',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->operador)->putJson("/api/incidencias/{$incidencia->id}", [
            'estado' => 'cerrada',
            'solucion' => 'Cable reemplazado',
        ]);

        $response->assertForbidden();
    }

    public function test_utis_cierra_incidencia_y_actualiza_equipo(): void
    {
        $equipo = Equipo::create([
            'codigo_patrimonial' => 'PC-UTIS-01',
            'tipo_equipo' => 'pc',
            'marca' => 'Dell',
            'modelo' => 'Optiplex',
            'estado_operativo' => 'operativo',
            'unidad_id' => $this->operador->unidad_activa_id,
            'registrado_por' => $this->admin->id,
        ]);

        $incidencia = Incidencia::create([
            'equipo_id' => $equipo->id,
            'reportado_por' => $this->operador->id,
            'tipo' => 'falla',
            'descripcion' => 'No arranca el sistema',
            'estado' => 'en_atencion',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->putJson("/api/incidencias/{$incidencia->id}", [
            'estado' => 'cerrada',
            'solucion' => 'Se reinstaló el sistema operativo',
            'estado_operativo_equipo' => 'operativo',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('incidencias', [
            'id' => $incidencia->id,
            'estado' => 'cerrada',
        ]);
        $this->assertDatabaseHas('equipos', [
            'id' => $equipo->id,
            'estado_operativo' => 'operativo',
        ]);
    }

    public function test_ml_command_ejecuta_y_audita(): void
    {
        Equipo::create([
            'codigo_patrimonial' => 'ML-CMD-01',
            'tipo_equipo' => 'pc',
            'marca' => 'Dell',
            'modelo' => 'Optiplex',
            'estado_operativo' => 'operativo',
            'unidad_id' => $this->admin->unidad_activa_id,
            'registrado_por' => $this->admin->id,
        ]);

        Artisan::call('sgmi:ml-predict');

        $this->assertDatabaseHas('auditoria_logs', [
            'modulo' => 'MOD-PAT-TI',
            'accion' => 'ml_ejecutar_batch',
        ]);
    }

    public function test_ml_omite_equipo_sin_datos_suficientes(): void
    {
        Equipo::create([
            'codigo_patrimonial' => 'ML-SIN-DATOS',
            'tipo_equipo' => 'pc',
            'marca' => 'Nuevo',
            'modelo' => 'Sin ficha',
            'estado_operativo' => 'operativo',
            'unidad_id' => $this->admin->unidad_activa_id,
            'registrado_por' => $this->admin->id,
        ]);

        $resultado = app(\App\Services\Patrimonio\MlPredictionService::class)->ejecutarBatch();

        $this->assertSame(0, $resultado['procesados']);
        $this->assertSame(1, $resultado['omitidos']);
        $this->assertDatabaseMissing('ml_predicciones', ['equipo_id' => Equipo::where('codigo_patrimonial', 'ML-SIN-DATOS')->value('id')]);
    }

    public function test_ml_batch_usa_servicio_fastapi(): void
    {
        config([
            'sgmi.ml.service_url' => 'http://ml.test',
            'sgmi.ml.api_token' => 'test-token',
        ]);

        $equipo = Equipo::create([
            'codigo_patrimonial' => 'ML-API-01',
            'tipo_equipo' => 'pc',
            'marca' => 'HP',
            'modelo' => 'ProDesk',
            'estado_operativo' => 'reparacion',
            'unidad_id' => $this->admin->unidad_activa_id,
            'registrado_por' => $this->admin->id,
        ]);

        Incidencia::create([
            'equipo_id' => $equipo->id,
            'reportado_por' => $this->admin->id,
            'tipo' => 'falla',
            'descripcion' => 'Pantalla intermitente',
        ]);

        Http::fake([
            'ml.test/predict/batch' => Http::response([
                'predicciones' => [
                    ['equipo_id' => $equipo->id, 'probabilidad' => 0.72],
                ],
            ]),
        ]);

        $resultado = app(\App\Services\Patrimonio\MlPredictionService::class)->ejecutarBatch();

        $this->assertSame(1, $resultado['procesados']);
        $this->assertSame('fastapi', $resultado['modo']);
        $this->assertDatabaseHas('ml_predicciones', [
            'equipo_id' => $equipo->id,
            'nivel_riesgo' => 'rojo',
        ]);

        Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer test-token')
            && str_contains($request->url(), '/predict/batch'));
    }
}
