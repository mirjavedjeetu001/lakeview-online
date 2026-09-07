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
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'image', 'delivery_mode']);
        $branchId = (int) session('branch_id');
        $branches = Branch::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'address', 'phones', 'image', 'is_active', 'sort_order']);

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
            ->take(8)
            ->get();

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
