<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'price',
        'compare_price',
        'stock',
        'attributes',
        'image',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'attributes' => 'array',
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Accessor para obtener el precio efectivo
    public function getEffectivePriceAttribute()
    {
        return $this->price ?? $this->product->price;
    }

    // Accessor para obtener el stock total
    public function getTotalStockAttribute()
    {
        return $this->inventories()->sum('quantity');
    }

    // Scope para variantes activas
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope para variantes por defecto
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
