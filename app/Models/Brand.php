<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'website',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // Scope para marcas activas
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessor para obtener la URL completa del logo
    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    // Accessor para obtener el número de productos
    public function getProductsCountAttribute()
    {
        return $this->products()->count();
    }
}
