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
        Schema::table('empleados', function (Blueprint $table) {
            // Primero eliminamos las restricciones de clave foránea
            $table->dropForeign(['grupo_id']);
            $table->dropForeign(['rol_id']);
            
            // Luego eliminamos las columnas
            $table->dropColumn(['grupo_id', 'rol_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            // Si necesitamos revertir, añadimos las columnas y las claves foráneas de nuevo
            $table->foreignId('grupo_id')->constrained('grupos');
            $table->foreignId('rol_id')->constrained('roles');
        });
    }
};
