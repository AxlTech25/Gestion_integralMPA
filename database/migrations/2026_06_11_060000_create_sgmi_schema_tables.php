<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unidades_organizacionales', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_org', 20)->unique();
            $table->string('codigo_siga', 50)->nullable();
            $table->string('nombre', 200);
            $table->enum('tipo', ['gerencia', 'unidad', 'comite']);
            $table->boolean('permite_derivacion')->default(false);
            $table->unsignedBigInteger('gerencia_id')->nullable();
            $table->unsignedBigInteger('padre_id')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });

        Schema::table('unidades_organizacionales', function (Blueprint $table) {
            $table->foreign('gerencia_id')->references('id')->on('unidades_organizacionales');
            $table->foreign('padre_id')->references('id')->on('unidades_organizacionales');
        });

        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('email', 100)->nullable();
            $table->string('password');
            $table->string('nombre_completo', 200);
            $table->foreignId('unidad_activa_id')->constrained('unidades_organizacionales');
            $table->boolean('activo')->default(true);
            $table->unsignedSmallInteger('intentos_fallidos')->default(0);
            $table->timestamp('bloqueado_hasta')->nullable();
            $table->timestamp('ultimo_login')->nullable();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('permisos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 100)->unique();
            $table->string('modulo', 50);
            $table->text('descripcion')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('role_permiso', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('permiso_id')->constrained('permisos')->cascadeOnDelete();
            $table->primary(['role_id', 'permiso_id']);
        });

        Schema::create('usuario_role', function (Blueprint $table) {
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->primary(['usuario_id', 'role_id']);
        });

        Schema::create('usuario_traslados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->foreignId('unidad_id')->constrained('unidades_organizacionales');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->text('motivo')->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('usuarios');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('auditoria_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios');
            $table->string('modulo', 50);
            $table->string('accion', 50);
            $table->string('entidad', 50);
            $table->unsignedBigInteger('entidad_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('tipos_documentales', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 150);
            $table->string('prefijo_numeracion', 20);
            $table->string('formato_display', 50)->default('{prefijo}-{anio}-{secuencial}');
            $table->boolean('requiere_firma_antes_derivar')->default(true);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('numeraciones_expediente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_documental_id')->constrained('tipos_documentales');
            $table->smallInteger('anio');
            $table->integer('ultimo_secuencial')->default(0);
            $table->unique(['tipo_documental_id', 'anio']);
        });

        Schema::create('expedientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_documental_id')->constrained('tipos_documentales');
            $table->smallInteger('anio');
            $table->integer('secuencial');
            $table->string('codigo', 50)->index();
            $table->string('asunto', 500);
            $table->enum('prioridad', ['baja', 'media', 'alta', 'urgente'])->default('media');
            $table->foreignId('unidad_origen_id')->constrained('unidades_organizacionales');
            $table->foreignId('unidad_actual_id')->constrained('unidades_organizacionales');
            $table->enum('estado', ['registrado', 'en_tramite', 'archivado'])->default('registrado');
            $table->foreignId('registrado_por')->constrained('usuarios');
            $table->timestamps();
            $table->unique(['tipo_documental_id', 'anio', 'secuencial']);
            $table->index(['unidad_actual_id', 'estado']);
        });

        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expedientes')->cascadeOnDelete();
            $table->smallInteger('version')->default(1);
            $table->string('titulo', 300);
            $table->string('archivo_path', 500)->nullable();
            $table->string('hash_contenido', 64)->nullable();
            $table->enum('estado', ['borrador', 'pendiente_firma', 'firmado'])->default('borrador');
            $table->foreignId('creado_por')->constrained('usuarios');
            $table->timestamps();
        });

        Schema::create('documento_firmas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_id')->unique()->constrained('documentos')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->string('firma_hash', 128);
            $table->json('firma_metadata')->nullable();
            $table->timestamp('firmado_at')->useCurrent();
        });

        Schema::create('documento_sellos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_id')->unique()->constrained('documentos')->cascadeOnDelete();
            $table->string('sello_imagen_path', 500);
            $table->json('sello_metadata')->nullable();
            $table->timestamp('aplicado_at')->useCurrent();
        });

        Schema::create('expediente_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expedientes')->cascadeOnDelete();
            $table->enum('tipo_movimiento', ['registro', 'derivacion', 'devolucion']);
            $table->foreignId('unidad_origen_id')->nullable()->constrained('unidades_organizacionales');
            $table->foreignId('unidad_destino_id')->nullable()->constrained('unidades_organizacionales');
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->text('observacion')->nullable();
            $table->text('proveido')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('expediente_adjuntos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expedientes')->cascadeOnDelete();
            $table->string('nombre_archivo', 255);
            $table->string('path', 500);
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('tamano_bytes');
            $table->foreignId('subido_por')->constrained('usuarios');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('equipos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_patrimonial', 50)->nullable()->unique();
            $table->string('codigo_siga', 50)->nullable();
            $table->enum('tipo_equipo', ['pc', 'servidor', 'impresora', 'red', 'otro']);
            $table->string('marca', 100);
            $table->string('modelo', 100);
            $table->string('numero_serie', 100)->nullable();
            $table->enum('estado_operativo', ['operativo', 'reparacion', 'baja', 'almacen'])->default('operativo');
            $table->foreignId('unidad_id')->constrained('unidades_organizacionales');
            $table->string('custodio_nombre', 200)->nullable();
            $table->string('custodio_cargo', 150)->nullable();
            $table->decimal('valor_patrimonial', 12, 2)->nullable();
            $table->date('fecha_adquisicion')->nullable();
            $table->foreignId('registrado_por')->constrained('usuarios');
            $table->timestamps();
        });

        Schema::create('fichas_tecnicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_id')->unique()->constrained('equipos')->cascadeOnDelete();
            $table->string('cpu', 100)->nullable();
            $table->smallInteger('ram_gb')->nullable();
            $table->integer('almacenamiento_gb')->nullable();
            $table->string('sistema_operativo', 100)->nullable();
            $table->string('red', 100)->nullable();
            $table->decimal('antiguedad_anios', 4, 1)->nullable();
            $table->json('componentes_json')->nullable();
            $table->foreignId('registrado_por')->constrained('usuarios');
            $table->timestamps();
        });

        Schema::create('fichas_mantenimiento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_id')->constrained('equipos')->cascadeOnDelete();
            $table->enum('tipo', ['preventivo', 'correctivo']);
            $table->date('fecha');
            $table->text('descripcion');
            $table->text('resultado')->nullable();
            $table->string('tecnico', 150)->nullable();
            $table->foreignId('registrado_por')->constrained('usuarios');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('incidencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_id')->constrained('equipos');
            $table->foreignId('reportado_por')->constrained('usuarios');
            $table->enum('tipo', ['falla', 'averia', 'requerimiento']);
            $table->text('descripcion');
            $table->enum('estado', ['abierta', 'en_atencion', 'cerrada'])->default('abierta');
            $table->text('solucion')->nullable();
            $table->foreignId('asignado_utis_id')->nullable()->constrained('usuarios');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('cerrada_at')->nullable();
        });

        Schema::create('ml_modelos', function (Blueprint $table) {
            $table->id();
            $table->string('version', 20)->unique();
            $table->string('algoritmo', 50)->default('random_forest');
            $table->json('parametros_json')->nullable();
            $table->json('metricas_json')->nullable();
            $table->string('modelo_path', 500)->nullable();
            $table->timestamp('entrenado_at')->useCurrent();
        });

        Schema::create('ml_predicciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_id')->constrained('equipos');
            $table->foreignId('ml_modelo_id')->constrained('ml_modelos');
            $table->decimal('probabilidad_falla', 5, 4);
            $table->enum('nivel_riesgo', ['verde', 'amarillo', 'rojo']);
            $table->json('factores_json')->nullable();
            $table->timestamp('calculado_at')->useCurrent();
        });

        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('sistema', ['siga', 'siaf']);
            $table->string('tipo_sync', 50);
            $table->enum('modo', ['automatico', 'manual']);
            $table->enum('estado', ['ok', 'parcial', 'error']);
            $table->integer('registros_ok')->default(0);
            $table->integer('registros_error')->default(0);
            $table->text('mensaje')->nullable();
            $table->foreignId('ejecutado_por')->nullable()->constrained('usuarios');
            $table->timestamp('ejecutado_at')->useCurrent();
        });

        Schema::create('siaf_ejecucion_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('periodo', 20);
            $table->decimal('pim', 14, 2);
            $table->decimal('ejecucion_total', 14, 2);
            $table->decimal('porcentaje_ejecucion', 5, 2);
            $table->json('detalle_resumido_json')->nullable();
            $table->boolean('es_simulacion')->default(false);
            $table->timestamp('sincronizado_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siaf_ejecucion_snapshots');
        Schema::dropIfExists('sync_logs');
        Schema::dropIfExists('ml_predicciones');
        Schema::dropIfExists('ml_modelos');
        Schema::dropIfExists('incidencias');
        Schema::dropIfExists('fichas_mantenimiento');
        Schema::dropIfExists('fichas_tecnicas');
        Schema::dropIfExists('equipos');
        Schema::dropIfExists('expediente_adjuntos');
        Schema::dropIfExists('expediente_movimientos');
        Schema::dropIfExists('documento_sellos');
        Schema::dropIfExists('documento_firmas');
        Schema::dropIfExists('documentos');
        Schema::dropIfExists('expedientes');
        Schema::dropIfExists('numeraciones_expediente');
        Schema::dropIfExists('tipos_documentales');
        Schema::dropIfExists('auditoria_logs');
        Schema::dropIfExists('usuario_traslados');
        Schema::dropIfExists('usuario_role');
        Schema::dropIfExists('role_permiso');
        Schema::dropIfExists('permisos');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('unidades_organizacionales');
    }
};
