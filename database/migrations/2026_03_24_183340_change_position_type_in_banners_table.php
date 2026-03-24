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
        // Forzar el cambio a string (varchar) para evitar errores de enum restrictivos
        try {
            Schema::table('banners', function (Blueprint $table) {
                $table->string('position')->change();
            });
        } catch (\Exception $e) {
            // Si estalla en Postgres, usa raw SQL:
            DB::statement('ALTER TABLE banners ALTER COLUMN position TYPE VARCHAR(255)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Opcional: no revertir para evitar fallos si hay nuevos datos
    }
};
