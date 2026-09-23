<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Setting;
use App\Models\OrderItem;
use App\Models\Coupon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'image', 'delivery_mode']);
        $branchId = (int) session('branch_id');
        $branches = Branch::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'name_bn', 'address', 'phones', 'image', 'is_active', 'sort_order']);
        $activeCoupons = Coupon::query()
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->orderByDesc('created_at')
            ->take(8)
            ->get(['id', 'code', 'type', 'value', 'min_order_amount', 'expires_at']);

        // The outlet picker opens before the customer can shop. Avoid loading
        // the complete catalog for that first paint; the selected outlet visit
        // loads only the products relevant to that branch.
        if (!$branchId) {
            return Inertia::render('Home', [
                'categories' => $categories,
                'featuredProducts' => [],
                'bestSellingProducts' => [],
                'allProducts' => [],
                'branches' => $branches,
                'selectedBranch' => null,
                'activeCoupons' => $activeCoupons,
            ]);
        }

        $productQuery = fn () => Product::forBranch($branchId)
            ->with('category:id,name,slug,delivery_mode')
            ->where('products.is_available', true);

        $featuredProducts = $productQuery()
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->take(8)
            ->get();
        $topSellingIds = OrderItem::query()
            ->whereHas('order', function ($query) use ($branchId) {
                $query->whereNotIn('status', ['cancelled']);
                if ($branchId) {
                    $query->where('branch_id', $branchId);
                }
            })
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(8)
            ->pluck('product_id');
        $bestSellingProducts = $productQuery()
            ->whereIn('products.id', $topSellingIds)
            ->get()
            ->sortBy(fn ($product) => $topSellingIds->search($product->id))
            ->values();
        if ($bestSellingProducts->isEmpty()) {
            $bestSellingProducts = $featuredProducts;
        }
        $allProducts = $productQuery()
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Home', [
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
            'bestSellingProducts' => $bestSellingProducts,
            'allProducts' => $allProducts,
            'branches' => $branches,
            'selectedBranch' => $branches->firstWhere('id', $branchId),
            'activeCoupons' => $activeCoupons,
        ]);
    }

    public function about()
    {
        return Inertia::render('About');
    }

    public function contact()
    {
        $branches = Branch::activeList();
        return Inertia::render('Contact', [
            'branches' => $branches,
        ]);
    }
}
