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
        Schema::create('roles_empleados', function (Blueprint $table) {
            $table->id();
            $table->string('nif');
            $table->foreignId('grupo_id')->constrained('grupos');
            $table->foreignId('rol_id')->constrained('roles');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->timestamps();

            // Añadimos índices para mejorar el rendimiento
            $table->index('nif');
            $table->index('fecha_inicio');
            $table->index('fecha_fin');

            // Añadimos una restricción única para evitar duplicados
            $table->unique(['nif', 'grupo_id', 'rol_id', 'fecha_inicio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles_empleados');
    }
};
