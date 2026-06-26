<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personal_siga_referencias', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_siga', 50)->unique();
            $table->string('dni', 15)->nullable();
            $table->string('nombre_completo', 200);
            $table->foreignId('unidad_id')->nullable()->constrained('unidades_organizacionales');
            $table->boolean('activo_siga')->default(true);
            $table->foreignId('usuario_id')->nullable()->constrained('usuarios');
            $table->boolean('desactivacion_sugerida')->default(false);
            $table->timestamp('sincronizado_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personal_siga_referencias');
    }
};
