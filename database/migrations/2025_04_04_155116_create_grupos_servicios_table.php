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
        Schema::create('grupos_servicios', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_grupo');
            $table->string('servicio');
            $table->timestamps();

            // Añadimos índices para mejorar el rendimiento
            $table->index('codigo_grupo');
            
            // Añadimos una restricción única para evitar duplicados
            $table->unique(['codigo_grupo', 'servicio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupos_servicios');
    }
};
