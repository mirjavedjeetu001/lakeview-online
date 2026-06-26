<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CustomCakeOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'order_number', 'branch_id', 'delivery_man_id', 'customer_name', 'customer_phone', 'customer_address',
        'delivery_type', 'delivery_area_id', 'cake_type', 'cake_size', 'cake_flavor',
        'message_on_cake', 'delivery_date', 'delivery_time', 'design_image',
        'estimated_price', 'delivery_charge', 'total', 'payment_method', 'status', 'payment_status',
        'notes', 'admin_notes', 'advance_amount', 'transaction_id', 'payment_verified'
    ];

    protected $casts = [
        'delivery_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'CC-' . date('Ymd') . '-' . Str::upper(Str::random(6));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function deliveryArea()
    {
        return $this->belongsTo(DeliveryArea::class);
    }

    public function deliveryMan()
    {
        return $this->belongsTo(DeliveryMan::class);
    }
}
