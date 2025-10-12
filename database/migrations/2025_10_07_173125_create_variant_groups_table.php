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
        Schema::create('variant_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('status');
            $table->index('sort_order');
        });

      
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover variant_group_id de la tabla variants
        Schema::table('variants', function (Blueprint $table) {
            $table->dropForeign(['variant_group_id']);
            $table->dropColumn(['variant_group_id', 'sort_order']);
        });

        Schema::dropIfExists('variant_groups');
    }
};
