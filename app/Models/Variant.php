<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Variant extends Model
{
    protected $fillable = [
        'name',
        'variant_group_id',
        'description',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'status' => 'string',
        'sort_order' => 'integer',
    ];

    protected $recordTitleAttribute = 'name';

    public function variantGroup(): BelongsTo
    {
        return $this->belongsTo(VariantGroup::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_variants')
                    ->withPivot('variant_group_id')
                    ->withTimestamps();
    }

    // Scope para variantes activas
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope por grupo
    public function scopeByGroup($query, $groupId)
    {
        return $query->where('variant_group_id', $groupId);
    }

    // Scope ordenado
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
