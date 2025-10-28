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
        'payment_id',
        'shipping_address_id',
        'billing_address_id',
        'coupon_id',
        'shipping_id',
        'status',
        'is_paid',
        'paid_at',
        'is_delivered',
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
        'is_paid' => 'boolean',
        'is_delivered' => 'boolean',
        'paid_at' => 'datetime',
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

    public function shipping(): BelongsTo
    {
        return $this->belongsTo(Shipping::class);
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

    /**
     * Obtener descripción del estado del pedido
     */
    public function getStatusDescriptionAttribute()
    {
        return match ($this->status) {
            'pending' => 'El pedido está pendiente de confirmación',
            'processing' => 'El pedido está siendo procesado',
            'confirmed' => 'El pedido ha sido confirmado y está en preparación',
            'shipped' => 'El pedido ha sido enviado',
            'delivered' => 'El pedido ha sido entregado',
            'cancelled' => 'El pedido ha sido cancelado',
            'refunded' => 'El pedido ha sido reembolsado',
            default => 'Estado desconocido',
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
        return $this->getAttribute('is_paid') ?? false;
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

    /**
     * Procesar pedido (cambiar de pending a processing)
     */
    public function process()
    {
        if ($this->status !== 'pending') {
            throw new \Exception('Solo se pueden procesar pedidos pendientes');
        }
        
        $this->status = 'processing';
        $this->save();
        
        return $this;
    }

    /**
     * Confirmar pedido
     */
    public function confirm()
    {
        if (!in_array($this->status, ['pending', 'processing'])) {
            throw new \Exception('Solo se pueden confirmar pedidos pendientes o en procesamiento');
        }
        
        $this->status = 'confirmed';
        $this->confirmed_at = now();
        $this->save();
        
        return $this;
    }

    /**
     * Marcar como enviado
     */
    public function markAsShipped()
    {
        if ($this->status !== 'confirmed') {
            throw new \Exception('Solo se pueden enviar pedidos confirmados');
        }
        
        $this->status = 'shipped';
        $this->shipped_at = now();
        $this->save();
        
        return $this;
    }

    /**
     * Marcar como entregado
     */
    public function markAsDelivered()
    {
        if ($this->status !== 'shipped') {
            throw new \Exception('Solo se pueden entregar pedidos enviados');
        }
        
        $this->status = 'delivered';
        $this->delivered_at = now();
        $this->save();
        
        return $this;
    }

    /**
     * Cancelar pedido
     */
    public function cancel($reason = null)
    {
        if (!$this->canBeCancelled()) {
            throw new \Exception('Este pedido no se puede cancelar');
        }
        
        $this->status = 'cancelled';
        $this->cancelled_at = now();
        if ($reason) {
            $this->admin_notes = $reason;
        }
        $this->save();
        
        return $this;
    }

    /**
     * Procesar pago exitoso
     */
    public function markAsPaid()
    {
        $this->payment_status = 'paid';
        $this->save();
        
        // Si el pedido está pendiente, procesarlo automáticamente
        if ($this->status === 'pending') {
            $this->process();
        }
        
        return $this;
    }

    /**
     * Marcar pago como fallido
     */
    public function markPaymentFailed()
    {
        $this->payment_status = 'failed';
        $this->save();
        
        return $this;
    }

    /**
     * Calcular totales automáticamente desde el carrito
     */
    public function calculateTotals()
    {
        if ($this->cart) {
            // Obtener totales del carrito
            $cartTotals = $this->cart->getTotals();
            
            $this->subtotal = $cartTotals['subtotal'];
            $this->discount_amount = $cartTotals['discount_amount'];
            $this->tax_amount = $cartTotals['tax_amount'];
            
            // Calcular envío si hay método de envío seleccionado
            if ($this->shipping_id) {
                $this->calculateShippingCost();
            }
            
            // El total del carrito ya incluye impuestos, solo agregamos envío
            $this->total_amount = $cartTotals['total'] + $this->shipping_amount;
        } else {
            // Fallback: calcular desde items del pedido
            $this->subtotal = $this->items()->sum('subtotal');
            $this->total_amount = $this->subtotal - $this->discount_amount + $this->tax_amount + $this->shipping_amount;
        }
        
        $this->save();
    }

    /**
     * Calcular costo de envío basado en el método seleccionado
     */
    public function calculateShippingCost()
    {
        if (!$this->shipping) {
            $this->shipping_amount = 0;
            return;
        }

        // Obtener peso total del carrito (si está disponible)
        $totalWeight = $this->getTotalWeight();
        
        // Obtener zona de envío (por defecto España)
        $zone = $this->shippingAddress->country ?? 'España';
        
        // Calcular costo usando el método de envío
        $this->shipping_amount = $this->shipping->calculateShippingCost(
            $this->subtotal,
            $totalWeight,
            $zone
        );
    }

    /**
     * Obtener peso total del carrito
     */
    public function getTotalWeight()
    {
        if (!$this->cart || empty($this->cart->items)) {
            return 0;
        }

        $totalWeight = 0;
        foreach ($this->cart->items as $item) {
            $product = \App\Models\Product::find($item['product_id']);
            if ($product && isset($product->weight)) {
                $totalWeight += ($product->weight ?? 0) * $item['quantity'];
            }
        }

        return $totalWeight;
    }

    /**
     * Crear pedido desde carrito
     */
    public static function createFromCart(Cart $cart, array $additionalData = [])
    {
        $order = self::create(array_merge([
            'order_number' => self::generateOrderNumber(),
            'user_id' => $cart->user_id,
            'cart_id' => $cart->id,
            'coupon_id' => $cart->coupon_id,
            'shipping_id' => $additionalData['shipping_id'] ?? null,
            'status' => 'pending',
            'payment_status' => 'pending',
        ], $additionalData));

        // Calcular totales automáticamente (incluye envío si está seleccionado)
        $order->calculateTotals();

        // Crear items del pedido desde el carrito
        $order->createOrderItemsFromCart();

        return $order;
    }

    /**
     * Crear items del pedido desde el carrito
     */
    public function createOrderItemsFromCart()
    {
        if (!$this->cart || empty($this->cart->items)) {
            return;
        }

        foreach ($this->cart->items as $item) {
            $product = \App\Models\Product::find($item['product_id']);
            if (!$product) continue;

            $itemSubtotal = ($item['price'] - ($item['price'] * $item['discount_percentage'] / 100)) * $item['quantity'];

            $this->items()->create([
                'product_id' => $item['product_id'],
                'cart_id' => $this->cart_id,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'discount_percentage' => $item['discount_percentage'],
                'subtotal' => $itemSubtotal,
                'variants' => $item['variants'] ?? [],
            ]);
        }
    }

    /**
     * Verificar si se puede cancelar
     */
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'processing', 'confirmed']);
    }

    /**
     * Verificar si se puede reembolsar
     */
    public function canBeRefunded()
    {
        return $this->is_paid && !$this->is_cancelled;
    }

    /**
     * Obtener el total de reembolsos
     */
    public function getTotalRefundedAttribute()
    {
        return $this->refunds()->where('status', 'completed')->sum('amount');
    }

    /**
     * Obtener el progreso del pedido (0-100)
     */
    public function getProgressAttribute()
    {
        return match ($this->status) {
            'pending' => 10,
            'processing' => 25,
            'confirmed' => 50,
            'shipped' => 75,
            'delivered' => 100,
            'cancelled', 'refunded' => 0,
            default => 0,
        };
    }

    /**
     * Obtener el siguiente estado posible
     */
    public function getNextPossibleStatuses()
    {
        return match ($this->status) {
            'pending' => ['processing', 'cancelled'],
            'processing' => ['confirmed', 'cancelled'],
            'confirmed' => ['shipped', 'cancelled'],
            'shipped' => ['delivered'],
            'delivered' => ['refunded'],
            'cancelled' => [],
            'refunded' => [],
            default => [],
        };
    }

    /**
     * Verificar si el pedido está en progreso
     */
    public function isInProgress()
    {
        return in_array($this->status, ['pending', 'processing', 'confirmed', 'shipped']);
    }

    /**
     * Verificar si el pedido está completado
     */
    public function isCompleted()
    {
        return $this->status === 'delivered';
    }

    /**
     * Verificar si el pedido está finalizado (completado o cancelado)
     */
    public function isFinalized()
    {
        return in_array($this->status, ['delivered', 'cancelled', 'refunded']);
    }

    /**
     * Obtener tiempo transcurrido desde la creación
     */
    public function getTimeElapsedAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Obtener tiempo estimado de entrega
     */
    public function getEstimatedDeliveryAttribute()
    {
        if ($this->status === 'shipped' && $this->shipped_at) {
            return $this->shipped_at->addDays(3)->format('d/m/Y');
        }
        
        if ($this->status === 'confirmed' && $this->confirmed_at) {
            return $this->confirmed_at->addDays(5)->format('d/m/Y');
        }
        
        return null;
    }
}
