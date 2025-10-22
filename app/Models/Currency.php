<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    protected $fillable = [
        'name',
        'code',
        'symbol',
        'symbol_position',
        'decimal_places',
        'is_active',
        'is_default',
        'exchange_rate',
        'sort_order',
    ];

    protected $casts = [
        'decimal_places' => 'integer',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'exchange_rate' => 'decimal:4',
    ];

    // Relaciones
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // Métodos de utilidad
    public function formatAmount($amount)
    {
        $formatted = number_format($amount, $this->decimal_places);
        
        if ($this->symbol_position === 'before') {
            return $this->symbol . $formatted;
        }
        
        return $formatted . ' ' . $this->symbol;
    }

    public function convertTo($amount, Currency $targetCurrency)
    {
        if ($this->id === $targetCurrency->id) {
            return $amount;
        }

        // Convertir a la moneda base (USD) y luego a la moneda objetivo
        $baseAmount = $amount / $this->exchange_rate;
        return $baseAmount * $targetCurrency->exchange_rate;
    }

    public function getFormattedExchangeRateAttribute()
    {
        return '1 ' . $this->code . ' = ' . number_format($this->exchange_rate, 4) . ' USD';
    }

    // Boot method para asegurar solo una moneda por defecto
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($currency) {
            if ($currency->is_default) {
                static::where('is_default', true)->update(['is_default' => false]);
            }
        });

        static::updating(function ($currency) {
            if ($currency->is_default) {
                static::where('is_default', true)
                    ->where('id', '!=', $currency->id)
                    ->update(['is_default' => false]);
            }
        });
    }
}
