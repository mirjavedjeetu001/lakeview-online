<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'branches'])->withCount('branches');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->string('search') . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'categories' => Category::orderBy('sort_order')->get(),
            'branches' => Branch::orderBy('sort_order')->get(),
            'filters' => $request->only(['search', 'category_id']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(5);
        $product = Product::create($validated);
        $this->syncBranches($product, $this->branchAssignments($request), !$request->has('branch_assignments'));

        return redirect()->back()->with('success', 'Product created and branch availability saved.');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $this->validateProduct($request);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        if ($request->has('branch_assignments')) {
            $this->syncBranches($product, $this->branchAssignments($request), false);
        }

        return redirect()->back()->with('success', 'Product updated and branch availability saved.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->back()->with('success', 'Product deleted successfully.');
    }

    private function validateProduct(Request $request): array
    {
        return $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);
    }

    private function branchAssignments(Request $request): array
    {
        $raw = $request->input('branch_assignments', []);

        if (is_string($raw)) {
            $raw = json_decode($raw, true) ?: [];
        }

        if (!is_array($raw)) {
            return [];
        }

        $branchIds = Branch::pluck('id')->map(fn ($id) => (int) $id)->all();

        return collect($raw)
            ->filter(fn ($assignment) => is_array($assignment) && in_array((int) ($assignment['branch_id'] ?? 0), $branchIds, true))
            ->mapWithKeys(function (array $assignment) {
                $price = $assignment['price'] ?? null;
                $discountPrice = $assignment['discount_price'] ?? null;
                $stock = $assignment['stock'] ?? null;

                return [(int) $assignment['branch_id'] => [
                    'price' => $price === '' || $price === null ? null : (float) $price,
                    'discount_price' => $discountPrice === '' || $discountPrice === null ? null : (float) $discountPrice,
                    'is_available' => filter_var($assignment['is_available'] ?? true, FILTER_VALIDATE_BOOLEAN),
                    'stock' => $stock === '' || $stock === null ? null : (int) $stock,
                ]];
            })
            ->all();
    }

    private function syncBranches(Product $product, array $assignments, bool $fallbackToActive = true): void
    {
        if (!$assignments && $fallbackToActive) {
            $assignments = Branch::activeList()->pluck('id')->mapWithKeys(fn ($id) => [
                $id => ['is_available' => true],
            ])->all();
        }

        $product->branches()->sync($assignments);
    }
}
