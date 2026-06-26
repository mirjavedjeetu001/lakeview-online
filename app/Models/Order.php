<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'order_number', 'branch_id', 'delivery_man_id', 'delivery_area_id', 'coupon_id',
        'customer_name', 'customer_phone', 'customer_address',
        'delivery_type', 'subtotal', 'delivery_charge', 'discount', 'total',
        'payment_method', 'status', 'payment_status', 'notes',
        'advance_amount', 'transaction_id', 'payment_verified'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . date('Ymd') . '-' . Str::upper(Str::random(6));
            }
        });
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function deliveryArea()
    {
        return $this->belongsTo(DeliveryArea::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function deliveryMan()
    {
        return $this->belongsTo(DeliveryMan::class);
    }
}
