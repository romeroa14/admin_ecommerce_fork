<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NewsletterSubscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'status',
        'subscription_token',
        'subscribed_at',
        'unsubscribed_at',
        'source',
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeUnsubscribed($query)
    {
        return $query->where('status', 'unsubscribed');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'active' => 'Activo',
            'inactive' => 'Inactivo',
            'unsubscribed' => 'Desuscrito',
            default => $this->status,
        };
    }

    // Methods
    public function subscribe()
    {
        $this->update([
            'status' => 'active',
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
        ]);
    }

    public function unsubscribe()
    {
        $this->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now(),
        ]);
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isUnsubscribed()
    {
        return $this->status === 'unsubscribed';
    }
}
