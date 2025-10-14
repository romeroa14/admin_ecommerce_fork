<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'cart_id',
        'shipping_address_id',
        'billing_address_id',
        'coupon_id',
        'status',
        'payment_status',
        'payment_method',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'shipping_amount',
        'total_amount',
        'customer_notes',
        'admin_notes',
        'confirmed_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // Accessor para obtener el estado en español
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'Pendiente',
            'processing' => 'Procesando',
            'confirmed' => 'Confirmado',
            'shipped' => 'Enviado',
            'delivered' => 'Entregado',
            'cancelled' => 'Cancelado',
            'refunded' => 'Reembolsado',
            default => 'Desconocido',
        };
    }

    // Accessor para obtener el estado de pago en español
    public function getPaymentStatusLabelAttribute()
    {
        return match ($this->payment_status) {
            'pending' => 'Pendiente',
            'paid' => 'Pagado',
            'failed' => 'Fallido',
            'refunded' => 'Reembolsado',
            default => 'Desconocido',
        };
    }

    // Accessor para verificar si está pagado
    public function getIsPaidAttribute()
    {
        return $this->payment_status === 'paid';
    }

    // Accessor para verificar si está entregado
    public function getIsDeliveredAttribute()
    {
        return $this->status === 'delivered';
    }

    // Accessor para verificar si está cancelado
    public function getIsCancelledAttribute()
    {
        return $this->status === 'cancelled';
    }

    // Accessor para obtener el total de items
    public function getTotalItemsAttribute()
    {
        return $this->items()->sum('quantity');
    }

    // Scope para pedidos pagados
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    // Scope para pedidos pendientes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope para pedidos entregados
    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    // Scope para pedidos cancelados
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Scope por rango de fechas
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Scope por método de pago
    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    // Método para generar número de pedido
    public static function generateOrderNumber()
    {
        $lastOrder = self::orderBy('id', 'desc')->first();
        if ($lastOrder && preg_match('/ORD-(\d+)/', $lastOrder->order_number, $matches)) {
            $number = (int) $matches[1] + 1;
        } else {
            $number = 1;
        }
        return 'ORD-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    // Método para confirmar pedido
    public function confirm()
    {
        $this->status = 'confirmed';
        $this->confirmed_at = now();
        $this->save();
    }

    // Método para marcar como enviado
    public function markAsShipped()
    {
        $this->status = 'shipped';
        $this->shipped_at = now();
        $this->save();
    }

    // Método para marcar como entregado
    public function markAsDelivered()
    {
        $this->status = 'delivered';
        $this->delivered_at = now();
        $this->save();
    }

    // Método para cancelar pedido
    public function cancel($reason = null)
    {
        $this->status = 'cancelled';
        $this->cancelled_at = now();
        if ($reason) {
            $this->admin_notes = $reason;
        }
        $this->save();
    }

    // Método para calcular totales
    public function calculateTotals()
    {
        $this->subtotal = $this->items()->sum('subtotal');
        $this->total_amount = $this->subtotal - $this->discount_amount + $this->tax_amount + $this->shipping_amount;
        $this->save();
    }

    // Método para verificar si se puede cancelar
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'processing', 'confirmed']);
    }

    // Método para verificar si se puede reembolsar
    public function canBeRefunded()
    {
        return $this->is_paid && !$this->is_cancelled;
    }

    // Método para obtener el total de reembolsos
    public function getTotalRefundedAttribute()
    {
        return $this->refunds()->where('status', 'completed')->sum('amount');
    }
}
