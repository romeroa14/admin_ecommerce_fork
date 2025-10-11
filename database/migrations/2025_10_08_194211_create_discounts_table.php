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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->nullable(); // Código de descuento
            $table->text('description')->nullable();
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('value', 10, 2); // Porcentaje o monto fijo
            $table->decimal('max_discount_amount', 10, 2)->nullable(); // Descuento máximo
            $table->decimal('min_purchase_amount', 10, 2)->nullable(); // Compra mínima requerida
            $table->integer('usage_limit')->nullable(); // Límite de uso total
            $table->integer('usage_limit_per_user')->nullable(); // Límite por usuario
            $table->integer('usage_count')->default(0); // Veces usado
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('applicable_products')->nullable(); // IDs de productos aplicables
            $table->json('applicable_categories')->nullable(); // IDs de categorías aplicables
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
