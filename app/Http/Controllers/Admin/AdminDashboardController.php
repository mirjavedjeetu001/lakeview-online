<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\CustomCakeOrder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Branch;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $periodFrom = Carbon::now()->subDays(29)->startOfDay();
        $periodTo = Carbon::now()->endOfDay();
        $analytics = app(AdminReportController::class)->analytics($periodFrom, $periodTo, null, 'delivered');

        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_revenue' => Order::where('status', 'delivered')->sum('total'),
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_branches' => Branch::count(),
            'active_coupons' => Coupon::where('is_active', true)->count(),
            'custom_cake_requests' => CustomCakeOrder::where('status', 'pending')->count(),
            'period_sales' => $analytics['summary']['sales'],
            'period_orders' => $analytics['summary']['orders'],
            'average_order' => $analytics['summary']['average_order'],
        ];

        $recentOrders = Order::with(['branch', 'items'])->latest()->take(5)->get();
        $recentCustomCakes = CustomCakeOrder::with('branch')->latest()->take(5)->get();
        $lowStockProducts = DB::table('branch_product as bp')
            ->join('products as p', 'p.id', '=', 'bp.product_id')
            ->join('branches as b', 'b.id', '=', 'bp.branch_id')
            ->whereNotNull('bp.stock')
            ->where('bp.stock', '<=', 5)
            ->select('p.id', 'p.name', 'b.name as branch_name', 'bp.stock', 'bp.is_available')
            ->orderBy('bp.stock')
            ->limit(8)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'recentCustomCakes' => $recentCustomCakes,
            'salesByDay' => $analytics['salesByDay'],
            'branchSales' => $analytics['branchSales'],
            'topProducts' => $analytics['topProducts'],
            'lowStockProducts' => $lowStockProducts,
        ]);
    }
}
