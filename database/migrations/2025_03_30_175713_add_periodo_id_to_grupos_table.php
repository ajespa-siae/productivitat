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
        Schema::table('grupos', function (Blueprint $table) {
            $table->foreignId('periodo_id')->after('nombre')->constrained('periodos');
            // Modificar la restricción unique para incluir periodo_id
            $table->dropUnique(['codigo']);
            $table->unique(['codigo', 'periodo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            $table->dropForeign(['periodo_id']);
            $table->dropColumn('periodo_id');
            // Restaurar la restricción unique original
            $table->dropUnique(['codigo', 'periodo_id']);
            $table->unique(['codigo']);
        });
    }
};
