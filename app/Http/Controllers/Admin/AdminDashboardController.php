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
    public function index(\Illuminate\Http\Request $request)
    {
        $periodFrom = Carbon::now()->subDays(29)->startOfDay();
        $periodTo = Carbon::now()->endOfDay();
        $branchId = $request->user()->adminBranchId();
        $analytics = app(AdminReportController::class)->analytics($periodFrom, $periodTo, $branchId, 'delivered');
        $orderScope = fn ($query) => $branchId ? $query->where('branch_id', $branchId) : $query;

        $stats = [
            'total_orders' => $orderScope(Order::query())->count(),
            'pending_orders' => $orderScope(Order::where('status', 'pending'))->count(),
            'total_revenue' => $orderScope(Order::where('status', 'delivered'))->sum('total'),
            'total_products' => $branchId ? DB::table('branch_product')->where('branch_id', $branchId)->count() : Product::count(),
            'total_categories' => Category::count(),
            'total_branches' => $branchId ? 1 : Branch::count(),
            'active_coupons' => Coupon::where('is_active', true)->count(),
            'custom_cake_requests' => $orderScope(CustomCakeOrder::where('status', 'pending'))->count(),
            'period_sales' => $analytics['summary']['sales'],
            'period_orders' => $analytics['summary']['orders'],
            'average_order' => $analytics['summary']['average_order'],
        ];

        $recentOrders = $orderScope(Order::with(['branch', 'items']))->latest()->take(5)->get();
        $recentCustomCakes = $orderScope(CustomCakeOrder::with('branch'))->latest()->take(5)->get();
        $lowStockProducts = DB::table('branch_product as bp')
            ->join('products as p', 'p.id', '=', 'bp.product_id')
            ->join('branches as b', 'b.id', '=', 'bp.branch_id')
            ->when($branchId, fn ($query) => $query->where('bp.branch_id', $branchId))
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
