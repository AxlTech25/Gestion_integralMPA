<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\AuthController;
use App\Models\AuditoriaLog;
use App\Models\Expediente;
use App\Models\Role;
use App\Models\TipoDocumental;
use App\Models\UnidadOrganizacional;
use App\Models\Usuario;
use Database\Seeders\OrganigramaSeeder;
use Database\Seeders\RolePermisoSeeder;
use Database\Seeders\SgmiSeeder;
use Database\Seeders\TiposDocumentalesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NucleoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            OrganigramaSeeder::class,
            RolePermisoSeeder::class,
            TiposDocumentalesSeeder::class,
            SgmiSeeder::class,
        ]);
    }

    public function test_login_exitoso_registra_auditoria(): void
    {
        $admin = Usuario::where('username', 'admin.utis')->firstOrFail();

        $request = Request::create('/api/login', 'POST', [
            'username' => 'admin.utis',
            'password' => 'Admin@123',
        ]);
        $request->setLaravelSession($this->app->make('session.store'));

        app(AuthController::class)->login($request);

        $this->assertDatabaseHas('auditoria_logs', [
            'usuario_id' => $admin->id,
            'modulo' => 'NUCLEO',
            'accion' => 'login_exitoso',
            'entidad' => 'usuario',
        ]);
    }

    public function test_bloqueo_tras_cinco_intentos_fallidos(): void
    {
        $usuario = Usuario::where('username', 'admin.utis')->firstOrFail();

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/login', [
                'username' => 'admin.utis',
                'password' => 'wrong-password',
            ])->assertStatus(422);
        }

        $this->postJson('/api/login', [
            'username' => 'admin.utis',
            'password' => 'Admin@123',
        ])->assertStatus(422);

        $usuario->refresh();
        $this->assertNotNull($usuario->bloqueado_hasta);
        $this->assertTrue($usuario->bloqueado_hasta->isFuture());
    }

    public function test_password_corta_rechazada_al_crear_usuario(): void
    {
        $admin = Usuario::where('username', 'admin.utis')->firstOrFail();
        $unidadId = UnidadOrganizacional::where('codigo_org', 'ORG-061')->value('id');

        $this->actingAs($admin)->postJson('/api/usuarios', [
            'username' => 'test.corto',
            'password' => 'abc',
            'nombre_completo' => 'Test Corto',
            'unidad_activa_id' => $unidadId,
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_traslado_cambia_unidad_y_registra_historial(): void
    {
        $admin = Usuario::where('username', 'admin.utis')->firstOrFail();
        $operador = $this->crearUsuario('operador.planea', 'ORG-051', ['OPERADOR']);
        $utisId = UnidadOrganizacional::where('codigo_org', 'ORG-061')->value('id');

        $this->actingAs($admin)->postJson("/api/usuarios/{$operador->id}/traslado", [
            'unidad_id' => $utisId,
            'motivo' => 'Traslado de prueba',
        ])->assertOk();

        $operador->refresh();
        $this->assertSame($utisId, $operador->unidad_activa_id);

        $this->assertDatabaseHas('usuario_traslados', [
            'usuario_id' => $operador->id,
            'unidad_id' => $utisId,
            'motivo' => 'Traslado de prueba',
        ]);
    }

    public function test_supervisor_ve_expedientes_unidad_tras_traslado_operador(): void
    {
        $planeamientoId = UnidadOrganizacional::where('codigo_org', 'ORG-051')->value('id');
        $utisId = UnidadOrganizacional::where('codigo_org', 'ORG-061')->value('id');

        $operador = $this->crearUsuario('operador.bandeja', 'ORG-051', ['OPERADOR']);
        $supervisor = $this->crearUsuario('supervisor.planea', 'ORG-051', ['SUPERVISOR_UNIDAD']);

        $expediente = $this->crearExpedienteEnUnidad($planeamientoId, $operador);

        $admin = Usuario::where('username', 'admin.utis')->firstOrFail();
        $this->actingAs($admin)->postJson("/api/usuarios/{$operador->id}/traslado", [
            'unidad_id' => $utisId,
            'motivo' => 'Rotación',
        ])->assertOk();

        $this->actingAs($supervisor)->getJson('/api/expedientes/bandeja')
            ->assertOk()
            ->assertJsonFragment(['codigo' => $expediente->codigo]);

        $operador->refresh();
        $bandejaOperador = $this->actingAs($operador)->getJson('/api/expedientes/bandeja')
            ->assertOk()
            ->json('expedientes');

        $this->assertFalse(
            collect($bandejaOperador)->contains(fn ($item) => $item['codigo'] === $expediente->codigo)
        );
    }

    public function test_derivar_rechaza_unidad_inactiva(): void
    {
        $planeamientoId = UnidadOrganizacional::where('codigo_org', 'ORG-051')->value('id');
        $destino = UnidadOrganizacional::where('codigo_org', 'ORG-052')->firstOrFail();
        $operador = $this->crearUsuario('deriv.inactivo', 'ORG-051', ['OPERADOR']);

        $destino->update(['activa' => false]);

        $expediente = $this->crearExpedienteEnUnidad($planeamientoId, $operador);

        $this->actingAs($operador)->postJson("/api/expedientes/{$expediente->id}/derivar", [
            'unidad_destino_id' => $destino->id,
            'proveido' => 'Prueba destino inactivo',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['unidad_destino_id']);
    }

    public function test_actualizar_unidad_desactiva(): void
    {
        $admin = Usuario::where('username', 'admin.utis')->firstOrFail();
        $unidad = UnidadOrganizacional::where('codigo_org', 'ORG-056')->firstOrFail();

        $this->actingAs($admin)->putJson("/api/unidades/{$unidad->id}", [
            'activa' => false,
        ])->assertOk()
            ->assertJsonPath('activa', false);

        $this->assertDatabaseHas('auditoria_logs', [
            'modulo' => 'NUCLEO',
            'accion' => 'actualizar',
            'entidad' => 'unidad',
            'entidad_id' => $unidad->id,
        ]);
    }

    public function test_bandeja_rechaza_vista_ejecutiva(): void
    {
        $ejecutivo = $this->crearUsuario('alcaldia.ejecutivo', 'ORG-002', ['VISTA_EJECUTIVA']);

        $this->actingAs($ejecutivo)->getJson('/api/expedientes/bandeja')
            ->assertForbidden();
    }

    public function test_vista_ejecutiva_puede_consultar_expediente(): void
    {
        $ejecutivo = $this->crearUsuario('alcaldia.consulta', 'ORG-002', ['VISTA_EJECUTIVA']);
        $admin = Usuario::where('username', 'admin.utis')->firstOrFail();
        $planeamientoId = UnidadOrganizacional::where('codigo_org', 'ORG-051')->value('id');
        $expediente = $this->crearExpedienteEnUnidad($planeamientoId, $admin);

        $this->actingAs($ejecutivo)->getJson("/api/expedientes/codigo/{$expediente->codigo}")
            ->assertOk()
            ->assertJsonPath('codigo', $expediente->codigo);
    }

    public function test_auditoria_no_editable_por_api(): void
    {
        $admin = Usuario::where('username', 'admin.utis')->firstOrFail();
        $log = AuditoriaLog::create([
            'usuario_id' => $admin->id,
            'modulo' => 'NUCLEO',
            'accion' => 'test',
            'entidad' => 'usuario',
            'entidad_id' => $admin->id,
            'ip_address' => '127.0.0.1',
        ]);

        $this->assertFalse(
            collect(\Illuminate\Support\Facades\Route::getRoutes())->contains(
                fn ($route) => in_array('PUT', $route->methods(), true)
                    && str_contains($route->uri(), 'auditoria/'.$log->id)
            )
        );
    }

  private function crearUsuario(string $username, string $unidadCodigo, array $roleCodigos): Usuario
    {
        $unidadId = UnidadOrganizacional::where('codigo_org', $unidadCodigo)->value('id');

        $usuario = Usuario::create([
            'username' => $username,
            'email' => $username.'@mpa.gob.pe',
            'password' => Hash::make('Test@1234'),
            'nombre_completo' => 'Usuario '.$username,
            'unidad_activa_id' => $unidadId,
            'activo' => true,
        ]);

        $roleIds = Role::whereIn('codigo', $roleCodigos)->pluck('id');
        $usuario->roles()->sync($roleIds);

        return $usuario;
    }

    private function crearExpedienteEnUnidad(int $unidadId, Usuario $registrador): Expediente
    {
        $usuario = $registrador->fresh();
        $usuario->update(['unidad_activa_id' => $unidadId]);

        $tipo = TipoDocumental::where('codigo', 'MEM')->firstOrFail();

        return app(\App\Services\Documentaria\ExpedienteService::class)->registrar(
            $usuario->fresh(),
            [
                'tipo_documental_id' => $tipo->id,
                'asunto' => 'Expediente prueba núcleo',
                'prioridad' => 'media',
            ]
        );
    }
}
