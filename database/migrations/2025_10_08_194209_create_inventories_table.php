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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('product_variant_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('location')->nullable(); // Almacén, ubicación física
            $table->integer('quantity')->default(0);
            $table->integer('reserved_quantity')->default(0); // Stock reservado en pedidos
            $table->enum('status', ['in_stock', 'low_stock', 'out_of_stock'])->default('in_stock');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
