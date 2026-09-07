<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access to admin panel.');
        }

        $user = $request->user();
        $routeName = (string) $request->route()?->getName();
        $permission = match (true) {
            str_starts_with($routeName, 'admin.products.') => 'products',
            str_starts_with($routeName, 'admin.categories.') => 'categories',
            str_starts_with($routeName, 'admin.orders.') => 'orders',
            str_starts_with($routeName, 'admin.reports.') => 'reports',
            str_starts_with($routeName, 'admin.stock.') => 'stock',
            str_starts_with($routeName, 'admin.custom-cakes.') => 'custom_cakes',
            str_starts_with($routeName, 'admin.branches.') => 'branches',
            str_starts_with($routeName, 'admin.delivery-areas.') => 'delivery_areas',
            str_starts_with($routeName, 'admin.delivery-men.') => 'delivery_men',
            str_starts_with($routeName, 'admin.coupons.') => 'coupons',
            str_starts_with($routeName, 'admin.users.') => 'users',
            str_starts_with($routeName, 'admin.settings.') => 'settings',
            $routeName === 'admin.dashboard' => 'dashboard',
            default => null,
        };

        if ($permission && !$user->hasPermission($permission)) {
            abort(403, 'You do not have permission to access this section.');
        }

        return $next($request);
    }
}
