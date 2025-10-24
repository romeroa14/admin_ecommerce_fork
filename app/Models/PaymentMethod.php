<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
        'sort_order',
        'icon',
        'color',
        'requires_gateway',
        'gateway_config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'requires_gateway' => 'boolean',
        'gateway_config' => 'array',
    ];

    // Relaciones
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
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

    // Métodos
    public function getIconAttribute($value)
    {
        return $value ?: $this->getDefaultIcon();
    }

    public function getDefaultIcon()
    {
        return match ($this->code) {
            'credit_card' => 'heroicon-o-credit-card',
            'debit_card' => 'heroicon-o-credit-card',
            'paypal' => 'heroicon-o-globe-alt',
            'stripe' => 'heroicon-o-bolt',
            'bank_transfer' => 'heroicon-o-building-library',
            'cash' => 'heroicon-o-banknotes',
            'crypto' => 'heroicon-o-currency-dollar',
            default => 'heroicon-o-credit-card',
        };
    }
}
