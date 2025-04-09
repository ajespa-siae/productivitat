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
        Schema::create('alegaciones_resultado', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resultado_id')->unique()->constrained('resultados_evaluacion');
            $table->foreignId('empleado_id')->constrained('empleados');
            $table->text('texto_alegacion');
            $table->timestamp('fecha_alegacion')->useCurrent();
            $table->string('estado')->default('Pendiente')->check("estado in ('Pendiente', 'Aceptada', 'Rechazada')");
            $table->text('respuesta')->nullable();
            $table->timestamp('fecha_respuesta')->nullable();
            $table->foreignId('evaluador_id')->nullable()->constrained('empleados');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alegaciones_resultado');
    }
};
