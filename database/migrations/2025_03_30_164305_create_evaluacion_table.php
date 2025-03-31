<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('evaluacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mando_id')->constrained('mandos');
            $table->foreignId('empleado_id')->constrained('empleados');
            $table->foreignId('indicador_id')->constrained('indicadores');
            $table->integer('puntuacion');
            $table->foreignId('periodo_id')->constrained('periodos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluacion');
    }
};
