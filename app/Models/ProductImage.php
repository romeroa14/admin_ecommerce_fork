<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'product_variant_id',
        'image',
        'alt_text',
        'order',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    // Scope para imágenes primarias
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    // Scope para ordenar por orden
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    // Accessor para obtener la URL completa de la imagen
    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image);
    }
}
