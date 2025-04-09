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
        Schema::dropIfExists('evaluacion');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('evaluacion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('evaluador_id');
            $table->unsignedBigInteger('empleado_id');
            $table->unsignedBigInteger('indicador_id');
            $table->integer('puntuacion');
            $table->unsignedBigInteger('periodo_id');
            $table->string('tipo')->default('Mando');
            $table->timestamps();

            $table->foreign('evaluador_id')
                ->references('id')
                ->on('empleados')
                ->onDelete('restrict');

            $table->foreign('empleado_id')
                ->references('id')
                ->on('empleados')
                ->onDelete('restrict');

            $table->foreign('indicador_id')
                ->references('id')
                ->on('indicadores')
                ->onDelete('restrict');

            $table->foreign('periodo_id')
                ->references('id')
                ->on('periodos')
                ->onDelete('restrict');
        });

        DB::statement("ALTER TABLE evaluacion ADD CONSTRAINT evaluacion_tipo_check CHECK (tipo IN ('Mando', 'Auto', '360', 'Sistema'))");
    }
};
