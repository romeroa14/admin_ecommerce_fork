<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Dólar Americano, Euro, Bolívar Venezolano
            $table->string('code', 3)->unique(); // USD, EUR, VES
            $table->string('symbol', 5); // $, €, Bs
            $table->enum('symbol_position', ['before', 'after'])->default('before');
            $table->integer('decimal_places')->default(2);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->decimal('exchange_rate', 10, 4)->default(1.0000); // Tasa de cambio respecto a USD
            $table->integer('sort_order')->default(10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};