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
        Schema::table('indicadores', function (Blueprint $table) {
            $table->text('tipo_indicador')->nullable();
            $table->text('periodicidad')->nullable();
        });

        // Añadir las restricciones CHECK usando SQL directo ya que Laravel no tiene un método directo para esto
        DB::statement("ALTER TABLE indicadores ADD CONSTRAINT check_tipo_indicador CHECK (tipo_indicador = ANY (ARRAY['Registre', 'Automàtic', 'Enquesta']))");
        DB::statement("ALTER TABLE indicadores ADD CONSTRAINT check_periodicidad CHECK (periodicidad = ANY (ARRAY['Cada 6 mesos', 'Continuat']))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indicadores', function (Blueprint $table) {
            // Primero eliminamos las restricciones
            DB::statement('ALTER TABLE indicadores DROP CONSTRAINT IF EXISTS check_tipo_indicador');
            DB::statement('ALTER TABLE indicadores DROP CONSTRAINT IF EXISTS check_periodicidad');
            
            $table->dropColumn(['tipo_indicador', 'periodicidad']);
        });
    }
};
