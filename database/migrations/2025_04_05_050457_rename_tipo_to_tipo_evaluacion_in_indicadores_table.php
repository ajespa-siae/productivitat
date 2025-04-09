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
        Schema::table('indicadores', function (Blueprint $table) {
            $table->renameColumn('tipo', 'tipo_evaluacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indicadores', function (Blueprint $table) {
            $table->renameColumn('tipo_evaluacion', 'tipo');
        });
    }
};
