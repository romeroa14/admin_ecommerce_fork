<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'coupon_id',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total',
        'expires_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    // Relaciones
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // Métodos de cálculo automático
    public function calculateTotals(): void
    {
        $items = $this->items;
        
        $this->subtotal = $items->sum('subtotal');
        $this->tax_amount = $this->subtotal * 0.21; // IVA 21%
        $this->total = $this->subtotal + $this->tax_amount - $this->discount_amount;
        
        $this->save();
    }

    public function isEmpty(): bool
    {
        return $this->items()->count() === 0;
    }

    public function getFormattedTotal(): string
    {
        return '€' . number_format($this->total, 2);
    }

    public function getItemsCount(): int
    {
        return $this->items()->sum('quantity');
    }
}
