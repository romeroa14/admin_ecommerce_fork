<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'price',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relaciones
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Métodos de cálculo
    public function calculateSubtotal(): void
    {
        $this->subtotal = $this->price * $this->quantity;
        $this->save();
    }

    public function getFormattedPrice(): string
    {
        return '€' . number_format($this->price, 2);
    }

    public function getFormattedSubtotal(): string
    {
        return '€' . number_format($this->subtotal, 2);
    }
}
