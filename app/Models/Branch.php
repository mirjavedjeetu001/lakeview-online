<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'address', 'phones', 'image', 'is_active', 'sort_order'];

    protected $casts = [
        'phones' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($branch) {
            if (empty($branch->slug)) {
                $branch->slug = Str::slug($branch->name) . '-' . Str::random(5);
            }
        });
        static::saved(fn () => Cache::forget('branches.active.v3'));
        static::deleted(fn () => Cache::forget('branches.active.v3'));
    }

    public static function activeList()
    {
        return static::hydrate(Cache::remember('branches.active.v3', now()->addMinutes(5), function () {
            return static::where('is_active', true)->orderBy('sort_order')->get()
                ->map(fn (self $branch) => $branch->getAttributes())
                ->all();
        }));
    }

    public function deliveryAreas()
    {
        return $this->hasMany(DeliveryArea::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function customCakeOrders()
    {
        return $this->hasMany(CustomCakeOrder::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'branch_product')
            ->withPivot(['price', 'discount_price', 'is_available', 'stock'])
            ->withTimestamps();
    }
}
