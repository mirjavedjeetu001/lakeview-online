<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Setting;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $branchId = (int) session('branch_id');
        $featuredProducts = ($branchId ? Product::forBranch($branchId) : Product::query())
            ->with('category')
            ->where('products.is_available', true)
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
        $bestSellingProducts = ($branchId ? Product::forBranch($branchId) : Product::query())
            ->with('category')
            ->where('products.is_available', true)
            ->whereIn('products.id', $topSellingIds)
            ->get()
            ->sortBy(fn ($product) => $topSellingIds->search($product->id))
            ->values();
        if ($bestSellingProducts->isEmpty()) {
            $bestSellingProducts = $featuredProducts;
        }
        $allProducts = ($branchId ? Product::forBranch($branchId) : Product::query())
            ->with('category')
            ->where('products.is_available', true)
            ->orderBy('sort_order')
            ->take(8)
            ->get();
        $branches = Branch::activeList();

        return Inertia::render('Home', [
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
            'bestSellingProducts' => $bestSellingProducts,
            'allProducts' => $allProducts,
            'branches' => $branches,
            'selectedBranch' => $branches->firstWhere('id', $branchId),
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
