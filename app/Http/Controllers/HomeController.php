<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Setting;
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
        $branches = Branch::activeList();

        return Inertia::render('Home', [
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
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
