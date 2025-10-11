<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = [
        'name',
        'country',
        'state',
        'zip_code',
        'rate',
        'is_compound',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'is_compound' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Scope para impuestos activos
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope por país
    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    // Scope por estado
    public function scopeByState($query, $state)
    {
        return $query->where('state', $state);
    }

    // Scope por código postal
    public function scopeByZipCode($query, $zipCode)
    {
        return $query->where('zip_code', $zipCode);
    }

    // Scope para impuestos compuestos
    public function scopeCompound($query)
    {
        return $query->where('is_compound', true);
    }

    // Scope para impuestos simples
    public function scopeSimple($query)
    {
        return $query->where('is_compound', false);
    }

    // Accessor para obtener la tasa como porcentaje
    public function getRatePercentageAttribute()
    {
        return $this->rate . '%';
    }

    // Accessor para obtener el tipo de impuesto
    public function getTypeLabelAttribute()
    {
        return $this->is_compound ? 'Compuesto' : 'Simple';
    }

    // Método para calcular el impuesto sobre un monto
    public function calculateTax($amount)
    {
        return ($amount * $this->rate) / 100;
    }

    // Método para verificar si aplica a una ubicación
    public function appliesTo($country, $state = null, $zipCode = null)
    {
        if ($this->country !== $country) {
            return false;
        }

        if ($this->state && $this->state !== $state) {
            return false;
        }

        if ($this->zip_code && $this->zip_code !== $zipCode) {
            return false;
        }

        return true;
    }

    // Método para obtener la descripción completa
    public function getDescriptionAttribute()
    {
        $description = $this->name . ' (' . $this->rate . '%)';
        
        if ($this->state) {
            $description .= ' - ' . $this->state;
        }
        
        if ($this->zip_code) {
            $description .= ' - ' . $this->zip_code;
        }
        
        return $description;
    }
}
