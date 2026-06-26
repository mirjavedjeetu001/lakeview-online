<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
}
