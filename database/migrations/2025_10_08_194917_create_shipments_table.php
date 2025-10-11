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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('tracking_number')->nullable();
            $table->string('carrier')->nullable(); // FedEx, UPS, DHL, etc.
            $table->string('service_level')->nullable(); // Standard, Express, etc.
            $table->enum('status', ['pending', 'processing', 'shipped', 'in_transit', 'delivered', 'failed'])->default('pending');
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->text('shipping_address')->nullable(); // Snapshot de la dirección
            $table->text('notes')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('estimated_delivery')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
