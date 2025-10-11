<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'amount',
        'min_purchase_amount',
        'max_discount_amount',
        'free_shipping',
        'usage_limit',
        'usage_limit_per_user',
        'usage_count',
        'product_ids',
        'category_ids',
        'excluded_product_ids',
        'excluded_category_ids',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'product_ids' => 'array',
        'category_ids' => 'array',
        'excluded_product_ids' => 'array',
        'excluded_category_ids' => 'array',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'free_shipping' => 'boolean',
        'is_active' => 'boolean',
        'amount' => 'decimal:2',
        'min_purchase_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
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

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessors
    public function getIsValidAttribute()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at > now()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at < now()) {
            return false;
        }

        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'percentage' => 'Porcentaje',
            'fixed_cart' => 'Descuento Fijo (Carrito)',
            'fixed_product' => 'Descuento Fijo (Producto)',
            default => $this->type,
        };
    }

    // Methods
    public function canBeUsed()
    {
        return $this->is_valid;
    }

    public function incrementUsage()
    {
        $this->increment('usage_count');
    }

    public function calculateDiscount($amount)
    {
        if (!$this->can_be_used) {
            return 0;
        }

        if ($this->min_purchase_amount && $amount < $this->min_purchase_amount) {
            return 0;
        }

        $discount = match ($this->type) {
            'percentage' => ($amount * $this->amount) / 100,
            'fixed_cart', 'fixed_product' => $this->amount,
            default => 0,
        };

        if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
            $discount = $this->max_discount_amount;
        }

        return min($discount, $amount);
    }
}
