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
        // Eliminar la restricción antigua tipo_check
        DB::statement('ALTER TABLE indicadores DROP CONSTRAINT IF EXISTS indicadores_tipo_check');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No necesitamos recrear la restricción antigua ya que fue reemplazada por tipo_evaluacion_check
    }
};
