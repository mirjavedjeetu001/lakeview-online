<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'group'];

    public static function get($key, $default = null)
    {
        return static::getAllByGroup()[$key] ?? $default;
    }

    public static function set($key, $value, $group = 'general')
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );

        Cache::forget('settings.all');
        Cache::forget('settings.group.' . $group);

        return $setting;
    }

    public static function getAllByGroup($group = null)
    {
        $cacheKey = $group ? 'settings.group.' . $group : 'settings.all';

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($group) {
            $query = static::query();
            if ($group) {
                $query->where('group', $group);
            }
            return $query->pluck('value', 'key')->toArray();
        });
    }
}
