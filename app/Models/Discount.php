<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Discount extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
        'value',
        'max_discount_amount',
        'min_purchase_amount',
        'usage_limit',
        'usage_limit_per_user',
        'usage_count',
        'starts_at',
        'expires_at',
        'is_active',
        'applicable_products',
        'applicable_categories',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'min_purchase_amount' => 'decimal:2',
        'usage_limit' => 'integer',
        'usage_limit_per_user' => 'integer',
        'usage_count' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'applicable_products' => 'array',
        'applicable_categories' => 'array',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    // Scope para descuentos activos
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('starts_at')
                          ->orWhere('starts_at', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>=', now());
                    });
    }

    // Scope para descuentos válidos
    public function scopeValid($query)
    {
        return $query->active()
                    ->where(function ($q) {
                        $q->whereNull('usage_limit')
                          ->orWhereRaw('usage_count < usage_limit');
                    });
    }

    // Método para verificar si el descuento es aplicable
    public function isApplicable($productId = null, $categoryId = null, $purchaseAmount = 0)
    {
        // Verificar si está activo
        if (!$this->is_active) {
            return false;
        }

        // Verificar fechas
        $now = now();
        if ($this->starts_at && $this->starts_at > $now) {
            return false;
        }
        if ($this->expires_at && $this->expires_at < $now) {
            return false;
        }

        // Verificar límite de uso
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        // Verificar monto mínimo de compra
        if ($this->min_purchase_amount && $purchaseAmount < $this->min_purchase_amount) {
            return false;
        }

        // Verificar productos aplicables
        if ($this->applicable_products && $productId) {
            if (!in_array($productId, $this->applicable_products)) {
                return false;
            }
        }

        // Verificar categorías aplicables
        if ($this->applicable_categories && $categoryId) {
            if (!in_array($categoryId, $this->applicable_categories)) {
                return false;
            }
        }

        return true;
    }

    // Método para calcular el descuento
    public function calculateDiscount($amount)
    {
        if ($this->type === 'percentage') {
            $discount = ($amount * $this->value) / 100;
        } else {
            $discount = $this->value;
        }

        // Aplicar descuento máximo si está definido
        if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
            $discount = $this->max_discount_amount;
        }

        return min($discount, $amount);
    }

    // Método para incrementar el contador de uso
    public function incrementUsage()
    {
        $this->increment('usage_count');
    }
}
