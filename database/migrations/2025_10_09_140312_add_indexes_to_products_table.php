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
        Schema::table('products', function (Blueprint $table) {
            // Índices para mejorar el rendimiento de consultas comunes
            $table->index(['status', 'is_featured']);
            $table->index(['category_id', 'status']);
            $table->index(['brand_id', 'status']);
            $table->index(['price', 'status']);
            $table->index(['stock', 'status']);
            $table->index(['created_at']);
            $table->index(['updated_at']);
        });

        // Schema::table('product_variants', function (Blueprint $table) {
        //     $table->index(['product_id', 'variant_id']);
        // });

        Schema::table('product_images', function (Blueprint $table) {
            $table->index(['product_id', 'is_primary']);
            $table->index(['product_variant_id']);
            $table->index(['order']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index(['parent_id', 'is_active']);
            $table->index(['is_active', 'order']);
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->index(['is_active']);
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->index(['type', 'is_active']);
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->index(['product_id', 'status']);
            $table->index(['product_variant_id', 'status']);
            $table->index(['status']);
        });

        Schema::table('discounts', function (Blueprint $table) {
            $table->index(['is_active', 'starts_at', 'expires_at']);
            $table->index(['code']);
            $table->index(['type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['status', 'is_featured']);
            $table->dropIndex(['category_id', 'status']);
            $table->dropIndex(['brand_id', 'status']);
            $table->dropIndex(['price', 'status']);
            $table->dropIndex(['stock', 'status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
        });

        // Schema::table('product_variants', function (Blueprint $table) {
        //     $table->dropIndex(['product_id', 'variant_id']);
        // });

        Schema::table('product_images', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'is_primary']);
            $table->dropIndex(['product_variant_id']);
            $table->dropIndex(['order']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['parent_id', 'is_active']);
            $table->dropIndex(['is_active', 'order']);
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropIndex(['type', 'is_active']);
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'status']);
            $table->dropIndex(['product_variant_id', 'status']);
            $table->dropIndex(['status']);
        });

        Schema::table('discounts', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'starts_at', 'expires_at']);
            $table->dropIndex(['code']);
            $table->dropIndex(['type']);
        });
    }
};