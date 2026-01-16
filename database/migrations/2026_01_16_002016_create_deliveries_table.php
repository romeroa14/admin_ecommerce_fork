<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('zone');
            $table->string('destination');
            $table->decimal('amount', 10, 2);
            $table->foreignId('delivery_man_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('person_id')->nullable()->constrained('people')->onDelete('set null');
            $table->enum('status', ['pending', 'assigned', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
