<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    // Scope para tags activos
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope por tipo de tag
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessor para obtener el número de productos
    public function getProductsCountAttribute()
    {
        return $this->products()->count();
    }
}
