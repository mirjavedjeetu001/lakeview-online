<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DeliveryArea extends Model
{
    use HasFactory;

    protected $fillable = ['branch_id', 'name', 'zone_type', 'delivery_charge', 'is_active'];

    public function scopeAvailableForBranch(Builder $query, int $branchId): Builder
    {
        return $query->where(function (Builder $builder) use ($branchId) {
            $builder->whereNull('branch_id')->orWhere('branch_id', $branchId);
        });
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
