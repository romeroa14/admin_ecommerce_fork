<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    // Scopes
    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    // Methods
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value, $type = 'string', $group = 'general', $description = null, $isPublic = false)
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'description' => $description,
                'is_public' => $isPublic,
            ]
        );
    }
}
