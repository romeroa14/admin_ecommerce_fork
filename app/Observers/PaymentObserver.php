<?php

namespace App\Observers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Shipment;

class PaymentObserver
{
    public function created(Payment $payment): void
    {
        // No hacer nada automático al crear el pago
        // Solo cuando cambie el estado a 'completed'
    }

    public function updated(Payment $payment): void
    {
        // Si el pago cambió a 'completed', crear factura y shipment
        if ($payment->wasChanged('status') && $payment->status === 'completed') {
            $this->createInvoice($payment);
            $this->createShipment($payment);
        }
    }

    private function createInvoice(Payment $payment): void
    {
        $order = $payment->order;
        
        // Verificar si ya existe una factura para este pedido
        $existingInvoice = Invoice::where('order_id', $order->id)->first();
        if ($existingInvoice) {
            return; // Ya existe factura
        }

        Invoice::create([
            'order_id' => $order->id,
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'subtotal' => $order->subtotal,
            'tax_amount' => $order->tax_amount,
            'discount_amount' => $order->discount_amount,
            'total_amount' => $order->total_amount,
            'status' => 'sent', // Directamente enviada ya que el pago está completo
            'billing_address' => $order->billingAddress?->getFullAddress(),
            'notes' => "Factura generada automáticamente tras confirmación de pago para el pedido #{$order->order_number}",
        ]);
    }

    private function createShipment(Payment $payment): void
    {
        $order = $payment->order;
        
        // Solo crear shipment si el pedido tiene método de envío
        if (!$order->shipping_id) {
            return;
        }

        // Verificar si ya existe un shipment para este pedido
        $existingShipment = Shipment::where('order_id', $order->id)->first();
        if ($existingShipment) {
            return; // Ya existe shipment
        }

        $shipping = $order->shipping;
        if (!$shipping) {
            return;
        }

        Shipment::create([
            'order_id' => $order->id,
            'tracking_number' => Shipment::generateTrackingNumber(),
            'carrier' => $shipping->name,
            'status' => 'pending',
            'estimated_delivery' => now()->addDays($shipping->estimated_days_min ?? 3),
            'shipping_address' => $order->shippingAddress?->getFullAddress(),
            'notes' => "Envío creado automáticamente tras confirmación de pago para el pedido #{$order->order_number}",
        ]);
    }
}
