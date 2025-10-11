<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'sku',
        'description',
        'short_description',
        'price',
        'compare_price',
        'cost',
        'category_id',
        'brand_id',
        'stock',
        'low_stock_threshold',
        'track_inventory',
        'status',
        'is_featured',
        'images',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'meta_keywords' => 'array',
        'images' => 'array',
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'cost' => 'decimal:2',
        'is_featured' => 'boolean',
        'track_inventory' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function discounts(): BelongsToMany
    {
        return $this->belongsToMany(Discount::class);
    }

    // Accessor para obtener la imagen principal
    public function getPrimaryImageAttribute()
    {
        return $this->images()->where('is_primary', true)->first() 
            ?? $this->images()->ordered()->first();
    }

    // Accessor para obtener el precio con descuento
    public function getDiscountedPriceAttribute()
    {
        $activeDiscounts = $this->discounts()->valid()->get();
        
        if ($activeDiscounts->isEmpty()) {
            return $this->price;
        }

        $maxDiscount = 0;
        foreach ($activeDiscounts as $discount) {
            $discountAmount = $discount->calculateDiscount($this->price);
            $maxDiscount = max($maxDiscount, $discountAmount);
        }

        return max(0, $this->price - $maxDiscount);
    }

    // Accessor para verificar si tiene descuento
    public function getHasDiscountAttribute()
    {
        return $this->discounted_price < $this->price;
    }

    // Accessor para obtener el stock total
    public function getTotalStockAttribute()
    {
        if ($this->variants()->exists()) {
            return $this->variants()->sum('stock');
        }
        
        return $this->inventories()->sum('quantity');
    }

    // Accessor para verificar si está en stock
    public function getInStockAttribute()
    {
        return $this->total_stock > 0;
    }

    // Accessor para verificar si tiene stock bajo
    public function getLowStockAttribute()
    {
        return $this->total_stock <= $this->low_stock_threshold;
    }

    // Scope para productos activos
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope para productos destacados
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Scope para productos en stock
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    // Scope para productos con stock bajo
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'low_stock_threshold');
    }

    // Scope para productos por categoría
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // Scope para productos por marca
    public function scopeByBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    // Scope para productos por rango de precio
    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    // Scope para productos con descuento
    public function scopeWithDiscount($query)
    {
        return $query->whereHas('discounts', function ($q) {
            $q->valid();
        });
    }

    // Método para actualizar el stock
    public function updateStock($quantity)
    {
        $this->stock = max(0, $this->stock + $quantity);
        $this->save();
    }

    // Método para verificar disponibilidad
    public function isAvailable($quantity = 1)
    {
        return $this->in_stock && $this->total_stock >= $quantity;
    }

    // Método para obtener el precio final
    public function getFinalPrice()
    {
        return $this->has_discount ? $this->discounted_price : $this->price;
    }
}
