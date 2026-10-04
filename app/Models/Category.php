<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'image', 'gallery', 'delivery_mode', 'business_type', 'is_active', 'sort_order'];

    protected $casts = [
        'gallery' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name) . '-' . Str::random(5);
            }
        });
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
