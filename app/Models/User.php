<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'phone', 'is_active', 'branch_id', 'permissions'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    public const ADMIN_PERMISSIONS = [
        'dashboard', 'products', 'categories', 'orders', 'reports', 'stock',
        'custom_cakes', 'branches', 'delivery_areas', 'delivery_men',
        'coupons', 'users', 'settings',
    ];

    public const PERMISSION_LABELS = [
        'dashboard' => 'Dashboard',
        'products' => 'Products',
        'categories' => 'Categories',
        'orders' => 'Orders & payments',
        'reports' => 'Sales reports',
        'stock' => 'Stock management',
        'custom_cakes' => 'Custom cake orders',
        'branches' => 'Branches',
        'delivery_areas' => 'Delivery areas',
        'delivery_men' => 'Delivery men',
        'coupons' => 'Coupons',
        'users' => 'User & role management',
        'settings' => 'Settings',
    ];

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'permissions' => 'array',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->is_active !== false && in_array($this->role, ['admin', 'super_admin'], true);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function hasPermission(string $permission): bool
    {
        if (!$this->isAdmin()) {
            return false;
        }

        if ($this->role === 'super_admin' || $this->permissions === null) {
            return true;
        }

        return in_array($permission, $this->permissions, true);
    }

    public function canAccessBranch(?int $branchId): bool
    {
        return !$branchId || $this->role === 'super_admin' || !$this->branch_id || (int) $this->branch_id === (int) $branchId;
    }

    public function adminBranchId(): ?int
    {
        return $this->role === 'super_admin' ? null : ($this->branch_id ? (int) $this->branch_id : null);
    }
}
