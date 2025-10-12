<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'variant_id',
        'sku',
        'price',
        'stock',
        'attributes',
        'image',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'attributes' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relaciones
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class);
    }

    // Métodos de utilidad
    public function getFormattedPrice(): string
    {
        return '€' . number_format($this->price ?? 0, 2);
    }

    public function getAttributesString(): string
    {
        if (!$this->attributes) {
            return '';
        }
        
        return collect($this->attributes)
            ->map(fn($value, $key) => "{$key}: {$value}")
            ->join(', ');
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    public function isLowStock(int $threshold = 10): bool
    {
        return $this->stock <= $threshold;
    }
}
