<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('evaluaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluado_id')->constrained('empleados')->restrictOnDelete();
            $table->foreignId('evaluador_id')->constrained('empleados')->restrictOnDelete();
            $table->foreignId('periodo_id')->constrained('periodos')->restrictOnDelete();
            $table->date('fecha')->default(DB::raw('CURRENT_DATE'));
            $table->string('tipo')->default('Registro');
            $table->boolean('finalizada')->default(false);
            $table->timestamps();
        });

        // Añadir la restricción CHECK para el campo tipo
        DB::statement("ALTER TABLE evaluaciones ADD CONSTRAINT evaluaciones_tipo_check CHECK (tipo IN ('Registro', 'Automatico', 'Encuesta'))");

        Schema::create('resultados_evaluacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluacion_id')->constrained('evaluaciones')->restrictOnDelete();
            $table->foreignId('indicador_id')->constrained('indicadores')->restrictOnDelete();
            $table->decimal('puntuacion', 5, 2)->nullable();
            $table->text('comentario')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resultados_evaluacion');
        Schema::dropIfExists('evaluaciones');
    }
};
