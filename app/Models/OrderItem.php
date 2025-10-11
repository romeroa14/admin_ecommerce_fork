<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'product_name',
        'product_sku',
        'product_attributes',
        'quantity',
        'unit_price',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total',
    ];

    protected $casts = [
        'product_attributes' => 'array',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    // Accessor para obtener el precio con descuento
    public function getDiscountedPriceAttribute()
    {
        return $this->unit_price - $this->discount_amount;
    }

    // Accessor para obtener el precio total con impuestos
    public function getTotalWithTaxAttribute()
    {
        return $this->subtotal + $this->tax_amount;
    }

    // Método para calcular el total del item
    public function calculateTotal()
    {
        $this->subtotal = $this->unit_price * $this->quantity;
        $this->total = $this->subtotal - $this->discount_amount + $this->tax_amount;
        $this->save();
    }

    // Scope para items con descuento
    public function scopeWithDiscount($query)
    {
        return $query->where('discount_amount', '>', 0);
    }

    // Scope para items con impuestos
    public function scopeWithTax($query)
    {
        return $query->where('tax_amount', '>', 0);
    }
}
