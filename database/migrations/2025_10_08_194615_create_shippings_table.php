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
        Schema::create('shippings', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre del método de envío
            $table->string('code')->unique(); // Código único (standard, express, free)
            $table->text('description')->nullable(); // Descripción del envío
            $table->decimal('base_price', 10, 2)->default(0); // Precio base
            $table->decimal('price_per_kg', 10, 2)->default(0); // Precio por kg adicional
            $table->decimal('free_shipping_threshold', 10, 2)->nullable(); // Umbral para envío gratis
            $table->integer('estimated_days_min')->default(1); // Días mínimos estimados
            $table->integer('estimated_days_max')->default(3); // Días máximos estimados
            $table->json('zones')->nullable(); // Zonas de envío (países, regiones)
            $table->json('weight_limits')->nullable(); // Límites de peso
            $table->boolean('is_active')->default(true); // Si está activo
            $table->integer('sort_order')->default(0); // Orden de aparición
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shippings');
    }
};
