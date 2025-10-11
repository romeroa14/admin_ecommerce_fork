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
        Schema::table('orders', function (Blueprint $table) {
            // Índices para mejorar el rendimiento de consultas comunes
            $table->index(['status', 'payment_status']);
            $table->index(['user_id', 'status']);
            $table->index(['payment_method', 'status']);
            $table->index(['created_at', 'status']);
            $table->index(['total_amount', 'status']);
            $table->index(['order_number']);
            $table->index(['confirmed_at']);
            $table->index(['shipped_at']);
            $table->index(['delivered_at']);
            $table->index(['cancelled_at']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index(['order_id', 'product_id']);
            $table->index(['product_id']);
            $table->index(['product_variant_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index(['order_id', 'status']);
            $table->index(['payment_method', 'status']);
            $table->index(['status', 'paid_at']);
            $table->index(['transaction_id']);
            $table->index(['paid_at']);
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->index(['order_id', 'status']);
            $table->index(['carrier', 'status']);
            $table->index(['tracking_number']);
            $table->index(['status', 'shipped_at']);
            $table->index(['delivered_at']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->index(['order_id', 'status']);
            $table->index(['status', 'invoice_date']);
            $table->index(['invoice_number']);
            $table->index(['due_date', 'status']);
        });

        Schema::table('refunds', function (Blueprint $table) {
            $table->index(['order_id', 'status']);
            $table->index(['payment_id', 'status']);
            $table->index(['status', 'processed_at']);
            $table->index(['refund_number']);
        });

        Schema::table('taxes', function (Blueprint $table) {
            $table->index(['country', 'state', 'is_active']);
            $table->index(['is_active', 'priority']);
            $table->index(['zip_code', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status', 'payment_status']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['payment_method', 'status']);
            $table->dropIndex(['created_at', 'status']);
            $table->dropIndex(['total_amount', 'status']);
            $table->dropIndex(['order_number']);
            $table->dropIndex(['confirmed_at']);
            $table->dropIndex(['shipped_at']);
            $table->dropIndex(['delivered_at']);
            $table->dropIndex(['cancelled_at']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['order_id', 'product_id']);
            $table->dropIndex(['product_id']);
            $table->dropIndex(['product_variant_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['order_id', 'status']);
            $table->dropIndex(['payment_method', 'status']);
            $table->dropIndex(['status', 'paid_at']);
            $table->dropIndex(['transaction_id']);
            $table->dropIndex(['paid_at']);
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->dropIndex(['order_id', 'status']);
            $table->dropIndex(['carrier', 'status']);
            $table->dropIndex(['tracking_number']);
            $table->dropIndex(['status', 'shipped_at']);
            $table->dropIndex(['delivered_at']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['order_id', 'status']);
            $table->dropIndex(['status', 'invoice_date']);
            $table->dropIndex(['invoice_number']);
            $table->dropIndex(['due_date', 'status']);
        });

        Schema::table('refunds', function (Blueprint $table) {
            $table->dropIndex(['order_id', 'status']);
            $table->dropIndex(['payment_id', 'status']);
            $table->dropIndex(['status', 'processed_at']);
            $table->dropIndex(['refund_number']);
        });

        Schema::table('taxes', function (Blueprint $table) {
            $table->dropIndex(['country', 'state', 'is_active']);
            $table->dropIndex(['is_active', 'priority']);
            $table->dropIndex(['zip_code', 'is_active']);
        });
    }
};