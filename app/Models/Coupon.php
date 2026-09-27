<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'description', 'category_id', 'branch_id', 'business_type', 'type', 'value', 'min_order_amount', 'max_discount_amount',
        'usage_limit', 'used_count', 'starts_at', 'expires_at', 'is_active'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function isValid($subtotal = 0, $products = null, ?Branch $branch = null): bool
    {
        if (!$this->is_active) return false;
        if ($this->starts_at && now() < $this->starts_at) return false;
        if ($this->expires_at && now() > $this->expires_at) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        if ($subtotal < $this->min_order_amount) return false;
        if ($this->branch_id && (!$branch || (int) $this->branch_id !== (int) $branch->id)) return false;
        if ($this->business_type && $this->business_type !== 'both' && (!$branch || $this->business_type !== $branch->business_type)) return false;
        if ($this->category_id && (!$products || !$products->contains(fn ($product) => (int) $product->category_id === (int) $this->category_id))) return false;
        return true;
    }

    public function calculateDiscount($subtotal): float
    {
        if ($this->type === 'percentage') {
            $discount = ($subtotal * $this->value) / 100;
            if ($this->max_discount_amount) {
                $discount = min($discount, $this->max_discount_amount);
            }
        } else {
            $discount = $this->value;
        }
        return min($discount, $subtotal);
    }
}
