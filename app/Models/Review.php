<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Review extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'order_id',
        'reviewer_name',
        'reviewer_email',
        'rating',
        'title',
        'comment',
        'image',
        'youtube_url',
        'status',
        'is_verified_purchase',
        'helpful_count',
        'unhelpful_count',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'is_verified_purchase' => 'boolean',
    ];

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Accessors
    public function getReviewerDisplayNameAttribute(): string
    {
        if ($this->user) {
            return $this->user->name;
        }
        return $this->reviewer_name ?? 'Anónimo';
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return Storage::url($this->image);
        }
        return null;
    }

    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        if (!$this->youtube_url) {
            return null;
        }

        // Extract YouTube video ID from various URL formats
        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i';
        if (preg_match($pattern, $this->youtube_url, $matches)) {
            return "https://www.youtube.com/embed/{$matches[1]}";
        }

        return null;
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Methods
    public function approve()
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    public function reject()
    {
        $this->update(['status' => 'rejected']);
    }

    public function markAsHelpful()
    {
        $this->increment('helpful_count');
    }

    public function markAsUnhelpful()
    {
        $this->increment('unhelpful_count');
    }
}
