<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Support\Facades\Log;

class Shipping extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'base_price',
        'price_per_kg',
        'free_shipping_threshold',
        'estimated_days_min',
        'estimated_days_max',
        'zones',
        'weight_limits',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'price_per_kg' => 'decimal:2',
        'free_shipping_threshold' => 'decimal:2',
        'zones' => 'array',
        'weight_limits' => 'array',
        'is_active' => 'boolean',
    ];

    // Relaciones
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
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

    // Métodos de cálculo
    public function calculateShippingCost($orderTotal, $totalWeight = 0, $zone = null)
    {
        // Debug: Log para verificar valores
        Log::info('Calculando costo de envío', [
            'shipping_method' => $this->name,
            'order_total' => $orderTotal,
            'base_price' => $this->base_price,
            'free_threshold' => $this->free_shipping_threshold,
            'price_per_kg' => $this->price_per_kg,
            'total_weight' => $totalWeight
        ]);

        // Verificar si aplica envío gratis
        if ($this->free_shipping_threshold && $orderTotal >= $this->free_shipping_threshold) {
            Log::info('Envío gratis aplicado', ['threshold' => $this->free_shipping_threshold, 'order_total' => $orderTotal]);
            return 0;
        }

        // Calcular costo base
        $cost = $this->base_price;

        // Agregar costo por peso si hay peso adicional
        if ($totalWeight > 0 && $this->price_per_kg > 0) {
            $weightCost = $totalWeight * $this->price_per_kg;
            $cost += $weightCost;
            Log::info('Costo por peso agregado', ['weight' => $totalWeight, 'price_per_kg' => $this->price_per_kg, 'weight_cost' => $weightCost]);
        }

        Log::info('Costo final calculado', ['final_cost' => $cost]);
        return $cost;
    }

    public function getEstimatedDeliveryDays()
    {
        if ($this->estimated_days_min === $this->estimated_days_max) {
            return $this->estimated_days_min . ' días';
        }
        
        return $this->estimated_days_min . '-' . $this->estimated_days_max . ' días';
    }

    public function isAvailableForZone($zone)
    {
        if (!$this->zones) {
            return true; // Sin restricciones de zona
        }

        return in_array($zone, $this->zones);
    }

    public function isWithinWeightLimit($weight)
    {
        if (!$this->weight_limits) {
            return true; // Sin límites de peso
        }

        $maxWeight = $this->weight_limits['max'] ?? null;
        return !$maxWeight || $weight <= $maxWeight;
    }
}
