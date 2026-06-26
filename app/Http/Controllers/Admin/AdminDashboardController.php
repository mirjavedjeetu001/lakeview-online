<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\CustomCakeOrder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Branch;
use App\Models\Coupon;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_revenue' => Order::where('status', 'delivered')->sum('total'),
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_branches' => Branch::count(),
            'active_coupons' => Coupon::where('is_active', true)->count(),
            'custom_cake_requests' => CustomCakeOrder::where('status', 'pending')->count(),
        ];

        $recentOrders = Order::with(['branch', 'items'])->latest()->take(5)->get();
        $recentCustomCakes = CustomCakeOrder::with('branch')->latest()->take(5)->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'recentCustomCakes' => $recentCustomCakes,
        ]);
    }
}
