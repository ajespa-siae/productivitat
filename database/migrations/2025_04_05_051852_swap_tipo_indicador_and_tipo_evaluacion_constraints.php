<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Eliminar las restricciones existentes
        DB::statement('ALTER TABLE indicadores DROP CONSTRAINT IF EXISTS indicadores_tipo_indicador_check');
        DB::statement('ALTER TABLE indicadores DROP CONSTRAINT IF EXISTS indicadores_tipo_evaluacion_check');
        DB::statement('ALTER TABLE indicadores DROP CONSTRAINT IF EXISTS check_tipo_indicador');
        DB::statement('ALTER TABLE indicadores DROP CONSTRAINT IF EXISTS check_tipo_evaluacion');

        // Añadir las nuevas restricciones con los valores intercambiados
        DB::statement("ALTER TABLE indicadores ALTER COLUMN tipo_indicador TYPE text USING tipo_indicador::text");
        DB::statement("ALTER TABLE indicadores ADD CONSTRAINT indicadores_tipo_indicador_check CHECK (tipo_indicador = ANY (ARRAY['Objectiu', 'Subjectiu']))");

        DB::statement("ALTER TABLE indicadores ALTER COLUMN tipo_evaluacion TYPE text USING tipo_evaluacion::text");
        DB::statement("ALTER TABLE indicadores ADD CONSTRAINT indicadores_tipo_evaluacion_check CHECK (tipo_evaluacion = ANY (ARRAY['Registre', 'Automàtic', 'Enquesta']))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // En caso de rollback, eliminamos las restricciones
        DB::statement('ALTER TABLE indicadores DROP CONSTRAINT IF EXISTS indicadores_tipo_indicador_check');
        DB::statement('ALTER TABLE indicadores DROP CONSTRAINT IF EXISTS indicadores_tipo_evaluacion_check');
    }
};
