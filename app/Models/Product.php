<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'price', 'discount_price',
        'image', 'gallery', 'delivery_mode', 'is_available', 'is_featured', 'sort_order'
    ];

    protected $casts = [
        'gallery' => 'array',
    ];

    protected $appends = ['effective_price', 'effective_delivery_mode', 'allow_pickup', 'allow_home_delivery'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name) . '-' . Str::random(5);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_product')
            ->withPivot(['price', 'discount_price', 'is_available', 'stock'])
            ->withTimestamps();
    }

    public function scopeForBranch(Builder $query, int $branchId): Builder
    {
        return $query
            ->join('branch_product', function ($join) use ($branchId) {
                $join->on('branch_product.product_id', '=', 'products.id')
                    ->where('branch_product.branch_id', '=', $branchId);
            })
            ->where('branch_product.is_available', true)
            ->where('products.is_available', true)
            ->select('products.*')
            ->addSelect([
                'branch_price' => DB::table('branch_product')
                    ->select('price')
                    ->whereColumn('branch_product.product_id', 'products.id')
                    ->where('branch_product.branch_id', $branchId)
                    ->limit(1),
                'branch_discount_price' => DB::table('branch_product')
                    ->select('discount_price')
                    ->whereColumn('branch_product.product_id', 'products.id')
                    ->where('branch_product.branch_id', $branchId)
                    ->limit(1),
                'branch_stock' => DB::table('branch_product')
                    ->select('stock')
                    ->whereColumn('branch_product.product_id', 'products.id')
                    ->where('branch_product.branch_id', $branchId)
                    ->limit(1),
            ]);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getEffectivePriceAttribute()
    {
        $discountPrice = $this->attributes['branch_discount_price'] ?? $this->discount_price;
        $price = $this->attributes['branch_price'] ?? $this->price;

        return $discountPrice !== null && (float) $discountPrice > 0
            ? $discountPrice
            : $price;
    }

    public function getEffectiveDeliveryModeAttribute(): string
    {
        $mode = $this->delivery_mode && $this->delivery_mode !== 'inherit'
            ? $this->delivery_mode
            : ($this->category?->delivery_mode ?: 'both');

        return in_array($mode, ['pickup', 'home_delivery', 'both'], true) ? $mode : 'both';
    }

    public function getAllowPickupAttribute(): bool
    {
        return in_array($this->effective_delivery_mode, ['pickup', 'both'], true);
    }

    public function getAllowHomeDeliveryAttribute(): bool
    {
        return in_array($this->effective_delivery_mode, ['home_delivery', 'both'], true);
    }
}
