<?php

namespace Tests\Feature;

use App\Models\NoConformidad;
use App\Models\Usuario;
use Database\Seeders\OrganigramaSeeder;
use Database\Seeders\RolePermisoSeeder;
use Database\Seeders\SgmiSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CalidadTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;

    private Usuario $auditor;

    private Usuario $gerente;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            OrganigramaSeeder::class,
            RolePermisoSeeder::class,
            SgmiSeeder::class,
        ]);

        $this->admin = Usuario::where('username', 'admin.utis')->firstOrFail();

        $this->auditor = Usuario::create([
            'username' => 'oci.test',
            'email' => 'oci@test.local',
            'password' => Hash::make('Oci@12345'),
            'nombre_completo' => 'Auditor OCI prueba',
            'unidad_activa_id' => $this->admin->unidad_activa_id,
            'activo' => true,
        ]);

        $ociRoleId = DB::table('roles')->where('codigo', 'AUDITOR_OCI')->value('id');
        DB::table('usuario_role')->insert([
            'usuario_id' => $this->auditor->id,
            'role_id' => $ociRoleId,
        ]);

        $this->gerente = Usuario::create([
            'username' => 'gerente.nc',
            'email' => 'gerente.nc@test.local',
            'password' => Hash::make('Gerente@123'),
            'nombre_completo' => 'Gerente NC',
            'unidad_activa_id' => $this->admin->unidad_activa_id,
            'activo' => true,
        ]);

        $gerenteRoleId = DB::table('roles')->where('codigo', 'GERENTE')->value('id');
        DB::table('usuario_role')->insert([
            'usuario_id' => $this->gerente->id,
            'role_id' => $gerenteRoleId,
        ]);
    }

    public function test_gerente_reporta_no_conformidad(): void
    {
        $response = $this->actingAs($this->gerente)->postJson('/api/no-conformidades', [
            'proceso' => 'documentaria',
            'severidad' => 'moderada',
            'descripcion' => 'Expediente sin firma en unidad actuante durante auditoría muestral.',
        ]);

        $response->assertCreated();
        $this->assertMatchesRegularExpression('/^NC-\d{4}-\d{4}$/', $response->json('codigo'));
        $this->assertDatabaseHas('no_conformidades', ['reportado_por' => $this->gerente->id]);
    }

    public function test_auditor_crea_accion_correctiva_y_cierra_nc(): void
    {
        $nc = NoConformidad::create([
            'anio' => (int) now()->year,
            'secuencial' => 1,
            'codigo' => 'NC-'.now()->year.'-0001',
            'proceso' => 'nucleo',
            'severidad' => 'grave',
            'descripcion' => 'Acceso sin permiso detectado en revisión.',
            'estado' => 'abierta',
            'reportado_por' => $this->gerente->id,
            'unidad_id' => $this->gerente->unidad_activa_id,
            'requiere_ac' => false,
        ]);

        $acResponse = $this->actingAs($this->auditor)->postJson("/api/no-conformidades/{$nc->id}/acciones-correctivas", [
            'causa_raiz' => 'Matriz de permisos desactualizada',
            'plan_acciones' => 'Actualizar RolePermisoSeeder y recertificar accesos trimestralmente.',
        ]);

        $acResponse->assertCreated();
        $this->assertMatchesRegularExpression('/^AC-\d{4}-\d{4}$/', $acResponse->json('codigo'));

        $acId = $acResponse->json('id');
        $this->actingAs($this->auditor)->putJson("/api/acciones-correctivas/{$acId}", [
            'estado' => 'en_implementacion',
        ])->assertOk();

        $this->actingAs($this->auditor)->putJson("/api/acciones-correctivas/{$acId}", [
            'estado' => 'cerrada',
            'resultado_verificacion' => 'eficaz',
            'evidencia_implementacion' => 'Matriz actualizada y usuarios recertificados.',
            'metodo_verificacion' => 'Muestra de 10 cuentas sin hallazgos.',
        ])->assertOk();

        $cerrar = $this->actingAs($this->auditor)->postJson("/api/no-conformidades/{$nc->id}/cerrar", [
            'verificacion_eficacia' => 'La AC fue eficaz; no se repitió el hallazgo en verificación.',
        ]);

        $cerrar->assertOk();
        $this->assertDatabaseHas('no_conformidades', [
            'id' => $nc->id,
            'estado' => 'cerrada',
        ]);
    }

    public function test_resumen_calidad_requiere_permiso_consultar(): void
    {
        $operador = Usuario::create([
            'username' => 'operador.sin.calidad',
            'email' => 'op.nc@test.local',
            'password' => Hash::make('Operador@123'),
            'nombre_completo' => 'Operador sin calidad',
            'unidad_activa_id' => $this->admin->unidad_activa_id,
            'activo' => true,
        ]);

        $operadorRoleId = DB::table('roles')->where('codigo', 'OPERADOR')->value('id');
        DB::table('usuario_role')->insert([
            'usuario_id' => $operador->id,
            'role_id' => $operadorRoleId,
        ]);

        $this->actingAs($operador)->getJson('/api/calidad/resumen')->assertForbidden();
        $this->actingAs($this->gerente)->getJson('/api/calidad/resumen')->assertOk();
        $this->actingAs($this->auditor)->getJson('/api/calidad/resumen')->assertOk();
    }

    public function test_cerrar_nc_con_ac_pendiente_falla(): void
    {
        $nc = NoConformidad::create([
            'anio' => (int) now()->year,
            'secuencial' => 2,
            'codigo' => 'NC-'.now()->year.'-0002',
            'proceso' => 'documentaria',
            'severidad' => 'leve',
            'descripcion' => 'NC con AC abierta para prueba.',
            'estado' => 'con_ac',
            'reportado_por' => $this->gerente->id,
            'unidad_id' => $this->gerente->unidad_activa_id,
            'requiere_ac' => true,
        ]);

        $this->actingAs($this->auditor)->postJson("/api/no-conformidades/{$nc->id}/acciones-correctivas", [
            'plan_acciones' => 'Plan de acción pendiente de implementar en sistema.',
        ])->assertCreated();

        $response = $this->actingAs($this->auditor)->postJson("/api/no-conformidades/{$nc->id}/cerrar", [
            'verificacion_eficacia' => 'Intento de cierre prematuro sin cerrar AC.',
        ]);

        $response->assertStatus(422);
    }
}
