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
        Schema::table('evaluacion', function (Blueprint $table) {
            // Paso 1: Añadir los nuevos campos
            $table->unsignedBigInteger('evaluador_id')->nullable();
            $table->string('tipo')->default('Mando');

            // Paso 2: Hacer evaluador_id obligatorio después de crearlo
            $table->unsignedBigInteger('evaluador_id')->nullable(false)->change();

            // Paso 3: Crear relación con empleados
            $table->foreign('evaluador_id')
                ->references('id')
                ->on('empleados')
                ->onDelete('restrict');

            // Paso 4: Eliminar mando_id
            $table->dropForeign(['mando_id']);
            $table->dropColumn('mando_id');
        });

        // Añadir la restricción CHECK para el campo tipo
        DB::statement("ALTER TABLE evaluacion ADD CONSTRAINT evaluacion_tipo_check CHECK (tipo IN ('Mando', 'Auto', '360', 'Sistema'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluacion', function (Blueprint $table) {
            // Eliminar la restricción CHECK
            DB::statement('ALTER TABLE evaluacion DROP CONSTRAINT IF EXISTS evaluacion_tipo_check');

            // Restaurar mando_id
            $table->unsignedBigInteger('mando_id')->nullable();
            $table->foreign('mando_id')
                ->references('id')
                ->on('mandos')
                ->onDelete('restrict');

            // Eliminar los nuevos campos
            $table->dropForeign(['evaluador_id']);
            $table->dropColumn(['evaluador_id', 'tipo']);
        });
    }
};
