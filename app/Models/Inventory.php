<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    protected $fillable = [
        'product_id',
        'product_variant_id',
        'location',
        'quantity',
        'reserved_quantity',
        'status',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'reserved_quantity' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    // Scope para inventario en stock
    public function scopeInStock($query)
    {
        return $query->where('status', 'in_stock');
    }

    // Scope para inventario con stock bajo
    public function scopeLowStock($query)
    {
        return $query->where('status', 'low_stock');
    }

    // Scope para inventario agotado
    public function scopeOutOfStock($query)
    {
        return $query->where('status', 'out_of_stock');
    }

    // Accessor para stock disponible
    public function getAvailableStockAttribute()
    {
        return $this->quantity - $this->reserved_quantity;
    }

    // Método para actualizar el estado del inventario
    public function updateStatus()
    {
        $availableStock = $this->available_stock;
        
        if ($availableStock <= 0) {
            $this->status = 'out_of_stock';
        } elseif ($availableStock <= $this->product->low_stock_threshold) {
            $this->status = 'low_stock';
        } else {
            $this->status = 'in_stock';
        }
        
        $this->save();
    }
}
