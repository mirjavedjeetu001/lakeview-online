<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::forBranch((int) session('branch_id'))->with('category');

        if ($request->filled('category')) {
            $requestedCategory = (string) $request->input('category');
            $category = Category::where('is_active', true)->where('slug', $requestedCategory)->first();

            // Older shared links may contain an accidental numeric suffix (e.g. ...YBrfW500).
            if (!$category) {
                $normalizedCategory = preg_replace('/\d+$/', '', $requestedCategory);
                if ($normalizedCategory !== $requestedCategory) {
                    $category = Category::where('is_active', true)->where('slug', $normalizedCategory)->first();
                    if ($category) {
                        return redirect()->route('products.index', array_filter([
                            'category' => $category->slug,
                            'search' => $request->input('search'),
                        ]));
                    }
                }
            }

            if ($category) {
                $query->where('products.category_id', $category->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->orderBy('sort_order')->paginate(12);
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['category', 'search']),
        ]);
    }

    public function show($slug)
    {
        $branchId = (int) session('branch_id');
        $product = Product::forBranch($branchId)->with('category')->where('slug', $slug)->firstOrFail();
        $related = Product::forBranch($branchId)->with('category')
            ->where('products.category_id', $product->category_id)
            ->where('products.id', '!=', $product->id)
            ->take(4)
            ->get();

        return Inertia::render('Products/Show', [
            'product' => $product,
            'related' => $related,
        ]);
    }
}
