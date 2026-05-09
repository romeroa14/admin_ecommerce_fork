<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order->load([
            'items.product',
            'shippingAddress',
            'shipping',
            'payments.paymentMethod',
        ]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "✅ Pedido #{$this->order->order_number} recibido - EquipoContainer",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.confirmation',
            with: [
                'order'       => $this->order,
                'address'     => $this->order->shippingAddress,
                'items'       => $this->order->items,
                'shipping'    => $this->order->shipping,
                'total'       => $this->order->total_amount,
                'orderNumber' => $this->order->order_number,
            ],
        );
    }
}
