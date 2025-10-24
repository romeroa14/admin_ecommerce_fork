<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;

class OrderObserver
{
    public function created(Order $order): void
    {
        // Crear PAGO automáticamente para cada pedido
        $this->createPayment($order);
    }

    public function updated(Order $order): void
    {
        // Si se actualiza el pedido, no hacer nada automático aquí
        // La lógica de factura y shipment se maneja desde PaymentObserver
    }

    private function createPayment(Order $order): void
    {
        // Buscar método de pago por defecto o el primero activo
        $defaultPaymentMethod = PaymentMethod::active()
            ->where('code', 'credit_card') // Método por defecto
            ->first() ?? PaymentMethod::active()->first();

        if (!$defaultPaymentMethod) {
            // Si no hay métodos de pago configurados, crear uno por defecto
            $defaultPaymentMethod = PaymentMethod::create([
                'name' => 'Tarjeta de Crédito',
                'code' => 'credit_card',
                'description' => 'Pago con tarjeta de crédito',
                'is_active' => true,
                'sort_order' => 1,
            ]);
        }

        Payment::create([
            'order_id' => $order->id,
            'payment_method_id' => $defaultPaymentMethod->id,
            'transaction_id' => Payment::generateTransactionId(),
            'amount' => $order->total_amount,
            'currency' => 'EUR', // Por defecto, se puede hacer dinámico
            'status' => 'pending',
            'payment_date' => now(),
            'notes' => "Pago creado automáticamente para el pedido #{$order->order_number}",
        ]);
    }
}
