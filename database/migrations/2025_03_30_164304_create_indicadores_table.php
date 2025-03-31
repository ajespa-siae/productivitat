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
        Schema::create('indicadores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('competencia_id')->constrained('competencias');
            $table->foreignId('grupo_id')->constrained('grupos');
            $table->foreignId('rol_id')->constrained('roles');
            $table->enum('sentido', ['positivo', 'negativo']);
            $table->integer('valor_minimo');
            $table->integer('valor_maximo');
            $table->foreignId('periodo_id')->constrained('periodos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indicadores');
    }
};
