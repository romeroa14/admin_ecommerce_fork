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
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('country');
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->decimal('rate', 5, 2); // Porcentaje de impuesto
            $table->boolean('is_compound')->default(false); // Si se aplica sobre otros impuestos
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};
