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
        'items',
        'expires_at',
    ];

    protected $casts = [
        'items' => 'array',
        'expires_at' => 'datetime',
    ];

    // Relaciones
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function order(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Order::class);
    }

    public function orderItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Agregar producto al carrito
     */
    public function addProduct(int $productId, int $quantity = 1, array $variants = []): void
    {
        $items = $this->items ?? [];
        
        // Buscar si el producto ya existe
        $existingIndex = $this->findItemIndex($productId, $variants);
        
        if ($existingIndex !== null) {
            // Actualizar cantidad
            $items[$existingIndex]['quantity'] += $quantity;
        } else {
            // Agregar nuevo item
            $product = \App\Models\Product::find($productId);
            if ($product) {
                $items[] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'discount_percentage' => $product->discount_percentage ?? 0,
                    'variants' => $variants,
                    'added_at' => now()->toISOString(),
                ];
            }
        }
        
        $this->items = $items;
        $this->save();
    }

    /**
     * Calcular totales del carrito (solo para mostrar, no se guardan)
     */
    public function getTotals(): array
    {
        $items = $this->items ?? [];
        $subtotal = 0;
        $totalDiscount = 0;
        
        foreach ($items as $item) {
            $price = $item['price'] ?? 0;
            $quantity = $item['quantity'] ?? 1;
            $discountPercentage = $item['discount_percentage'] ?? 0;
            
            $itemDiscount = ($price * $discountPercentage) / 100;
            $totalDiscount += $itemDiscount * $quantity;
            
            $itemSubtotal = ($price - $itemDiscount) * $quantity;
            $subtotal += $itemSubtotal;
        }
        
        $taxAmount = $subtotal * 0.21; // IVA 21%
        $total = $subtotal + $taxAmount;
        
        return [
            'subtotal' => round($subtotal, 2),
            'discount_amount' => round($totalDiscount, 2),
            'tax_amount' => round($taxAmount, 2),
            'total' => round($total, 2),
        ];
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function getFormattedTotal(): string
    {
        $totals = $this->getTotals();
        return '€' . number_format($totals['total'], 2);
    }

    public function getItemsCount(): int
    {
        $items = $this->items ?? [];
        return array_sum(array_column($items, 'quantity'));
    }

    private function findItemIndex(int $productId, array $variants): ?int
    {
        $items = $this->items ?? [];
        
        foreach ($items as $index => $item) {
            if ($item['product_id'] == $productId && 
                json_encode($item['variants'] ?? []) === json_encode($variants)) {
                return $index;
            }
        }
        
        return null;
    }
}