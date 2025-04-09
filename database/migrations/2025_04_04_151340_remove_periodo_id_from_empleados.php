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
            // Primero eliminamos la restricción de clave foránea
            $table->dropForeign(['periodo_id']);
            // Luego eliminamos la columna
            $table->dropColumn('periodo_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            // Si necesitamos revertir, añadimos la columna y la clave foránea de nuevo
            $table->foreignId('periodo_id')->constrained('periodos');
        });
    }
};
