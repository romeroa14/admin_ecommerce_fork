<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'mobile_image',
        'link',
        'button_text',
        'position',
        'order',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('starts_at')
                          ->orWhere('starts_at', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>=', now());
                    });
    }

    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }

    // Accessors
    public function getIsValidAttribute()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at > now()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at < now()) {
            return false;
        }

        return true;
    }

    public function getPositionLabelAttribute()
    {
        return match ($this->position) {
            'header' => 'Encabezado',
            'hero' => 'Hero Section',
            'sidebar' => 'Barra Lateral',
            'footer' => 'Pie de Página',
            'popup' => 'Popup',
            default => $this->position,
        };
    }

    // Methods
    public function getImageUrl()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function getMobileImageUrl()
    {
        return $this->mobile_image ? asset('storage/' . $this->mobile_image) : $this->getImageUrl();
    }
}
