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
        DB::statement('DROP INDEX IF EXISTS indicadores_nombre_unique');
        DB::statement('ALTER TABLE indicadores DROP CONSTRAINT IF EXISTS indicadores_nombre_unique');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indicadores', function (Blueprint $table) {
            // No necesitamos hacer nada aquí ya que el nombre no era único originalmente
        });
    }
};
