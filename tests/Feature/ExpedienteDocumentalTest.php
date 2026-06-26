<?php

namespace Tests\Feature;

use App\Models\Expediente;
use App\Models\ExpedienteMovimiento;
use App\Models\TipoDocumental;
use App\Models\TramiteConstancia;
use App\Models\Usuario;
use Database\Seeders\OrganigramaSeeder;
use Database\Seeders\RolePermisoSeeder;
use Database\Seeders\SellosInstitucionalesSeeder;
use Database\Seeders\SgmiSeeder;
use Database\Seeders\TiposDocumentalesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpedienteDocumentalTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            OrganigramaSeeder::class,
            RolePermisoSeeder::class,
            SellosInstitucionalesSeeder::class,
            TiposDocumentalesSeeder::class,
            SgmiSeeder::class,
        ]);

        $this->admin = Usuario::where('username', 'admin.utis')->firstOrFail();
    }

    public function test_registrar_expediente_genera_codigo_y_movimiento(): void
    {
        $tipo = TipoDocumental::where('codigo', 'MEM')->firstOrFail();

        $expediente = app(\App\Services\Documentaria\ExpedienteService::class)->registrar(
            $this->admin,
            [
                'tipo_documental_id' => $tipo->id,
                'asunto' => 'Prueba unitaria registro',
                'prioridad' => 'media',
            ]
        );

        $this->assertMatchesRegularExpression('/^MEM-\d{4}-\d{4}$/', $expediente->codigo);
        $this->assertSame('en_tramite', $expediente->estado);
        $this->assertDatabaseHas('expediente_movimientos', [
            'expediente_id' => $expediente->id,
            'tipo_movimiento' => 'registro',
        ]);
    }

    public function test_derivar_crea_constancia_digital(): void
    {
        $tipo = TipoDocumental::where('codigo', 'MEM')->firstOrFail();
        $service = app(\App\Services\Documentaria\ExpedienteService::class);

        $expediente = $service->registrar($this->admin, [
            'tipo_documental_id' => $tipo->id,
            'asunto' => 'Derivación con constancia',
            'prioridad' => 'media',
        ]);

        $destinoId = $this->admin->unidad_activa_id === $expediente->unidad_actual_id
            ? \App\Models\UnidadOrganizacional::where('codigo_org', 'ORG-021')->value('id')
            : $this->admin->unidad_activa_id;

        $expediente = $service->derivar(
            $expediente->load('tipoDocumental'),
            $this->admin,
            $destinoId,
            'Proveído de prueba'
        );

        $this->assertSame('por_recepcionar', $expediente->estado);

        $movimiento = ExpedienteMovimiento::where('expediente_id', $expediente->id)
            ->where('tipo_movimiento', 'derivacion')
            ->first();

        $this->assertNotNull($movimiento);
        $this->assertDatabaseHas('tramite_constancias', [
            'expediente_movimiento_id' => $movimiento->id,
            'tipo_acto' => 'proveido_salida',
        ]);

        $constancia = TramiteConstancia::where('expediente_movimiento_id', $movimiento->id)->first();
        $this->assertNotEmpty($constancia->firma_hash);
        $this->assertNotEmpty($constancia->sello_texto);
    }

    public function test_archivar_expediente_en_unidad(): void
    {
        $tipo = TipoDocumental::where('codigo', 'MEM')->firstOrFail();
        $service = app(\App\Services\Documentaria\ExpedienteService::class);

        $expediente = $service->registrar($this->admin, [
            'tipo_documental_id' => $tipo->id,
            'asunto' => 'Archivo de expediente',
            'prioridad' => 'baja',
        ]);

        $archivado = $service->archivar($expediente, $this->admin);

        $this->assertSame('archivado', $archivado->estado);
        $this->assertNotNull($archivado->archivado_at);
        $this->assertDatabaseHas('expedientes', [
            'id' => $expediente->id,
            'estado' => 'archivado',
        ]);
    }

    public function test_buscar_expediente_por_codigo_api(): void
    {
        $tipo = TipoDocumental::where('codigo', 'MEM')->firstOrFail();
        $expediente = app(\App\Services\Documentaria\ExpedienteService::class)->registrar(
            $this->admin,
            [
                'tipo_documental_id' => $tipo->id,
                'asunto' => 'Búsqueda API',
                'prioridad' => 'media',
            ]
        );

        $response = $this->actingAs($this->admin)->getJson('/api/expedientes/buscar?q='.$expediente->codigo);

        $response->assertOk();
        $response->assertJsonFragment(['codigo' => $expediente->codigo]);
    }

    public function test_historial_incluye_permanencia_en_oficina(): void
    {
        $tipo = TipoDocumental::where('codigo', 'MEM')->firstOrFail();
        $service = app(\App\Services\Documentaria\ExpedienteService::class);

        $expediente = $service->registrar($this->admin, [
            'tipo_documental_id' => $tipo->id,
            'asunto' => 'Permanencia historial',
            'prioridad' => 'media',
        ]);

        $response = $this->actingAs($this->admin)->getJson("/api/expedientes/codigo/{$expediente->codigo}");

        $response->assertOk();
        $historial = $response->json('historial');
        $this->assertNotEmpty($historial);
        $this->assertArrayHasKey('permanencia_dias', $historial[0]);
        $this->assertArrayHasKey('permanencia_texto', $historial[0]);
    }

    public function test_bandeja_filtra_por_antiguedad_minima(): void
    {
        $utisId = \App\Models\UnidadOrganizacional::where('codigo_org', 'ORG-061')->value('id');
        $this->admin->update(['unidad_activa_id' => $utisId]);

        $tipo = TipoDocumental::where('codigo', 'MEM')->firstOrFail();
        $expediente = app(\App\Services\Documentaria\ExpedienteService::class)->registrar(
            $this->admin->fresh(),
            [
                'tipo_documental_id' => $tipo->id,
                'asunto' => 'Antiguo para filtro',
                'prioridad' => 'media',
            ]
        );

        Expediente::where('id', $expediente->id)->update([
            'created_at' => now()->subDays(15),
            'unidad_actual_id' => $utisId,
        ]);

        $response = $this->actingAs($this->admin->fresh())->getJson('/api/expedientes/bandeja?antiguedad_min=10');

        $response->assertOk();
        $codigos = collect($response->json('expedientes'))->pluck('codigo');
        $this->assertTrue($codigos->contains($expediente->codigo));
    }

    public function test_registrar_rechaza_archivo_ejecutable(): void
    {
        $tipo = TipoDocumental::where('codigo', 'MEM')->firstOrFail();

        $response = $this->actingAs($this->admin)->postJson('/api/expedientes', [
            'tipo_documental_id' => $tipo->id,
            'asunto' => 'Con archivo malicioso',
            'prioridad' => 'media',
            'archivo' => \Illuminate\Http\UploadedFile::fake()->create('malware.exe', 100, 'application/octet-stream'),
        ]);

        $response->assertStatus(422);
    }

    public function test_crear_tipo_documental_con_permiso_gestionar(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/api/tipos-documentales', [
            'codigo' => 'TST',
            'nombre' => 'Tipo de prueba',
            'prefijo_numeracion' => 'TST',
            'clase_norma' => 'gestion_interna',
            'ambito_emision' => 'unidad',
            'activo' => true,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('tipos_documentales', ['codigo' => 'TST']);
    }
}
