<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdminStockController extends Controller
{
    private const LOW_STOCK_LIMIT = 5;

    public function index(Request $request)
    {
        $branches = $this->availableBranches($request);
        $branchId = $request->filled('branch_id')
            ? $request->integer('branch_id')
            : ($branches->firstWhere('is_active', true)?->id ?: $branches->first()?->id);
        $filters = $request->only(['branch_id', 'search', 'category_id', 'stock_status']);

        return Inertia::render('Admin/Stock/Index', [
            'branches' => $branches,
            'categories' => Category::orderBy('sort_order')->get(['id', 'name']),
            'products' => $this->productRows($branchId, $filters),
            'summary' => $this->summary($branchId),
            'filters' => [
                'branch_id' => $branchId,
                'search' => $request->input('search', ''),
                'category_id' => $request->input('category_id', ''),
                'stock_status' => $request->input('stock_status', ''),
            ],
            'lowStockLimit' => self::LOW_STOCK_LIMIT,
        ]);
    }

    public function update(Request $request, int $product)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'stock' => 'nullable|integer|min:0',
            'is_available' => 'required|boolean',
        ]);

        abort_unless($request->user()->canAccessBranch((int) $validated['branch_id']), 403, 'This branch is outside your access.');

        $exists = DB::table('branch_product')
            ->where('branch_id', $validated['branch_id'])
            ->where('product_id', $product)
            ->exists();

        if (!$exists) {
            return redirect()->back()->withErrors(['stock' => 'This product is not assigned to the selected branch.']);
        }

        DB::table('branch_product')
            ->where('branch_id', $validated['branch_id'])
            ->where('product_id', $product)
            ->update([
                'stock' => $validated['stock'] === '' ? null : ($validated['stock'] ?? null),
                'is_available' => (bool) $validated['is_available'],
                'updated_at' => now(),
            ]);

        return redirect()->back()->with('success', 'Stock updated successfully.');
    }

    public function export(Request $request)
    {
        $branches = $this->availableBranches($request);
        $branchId = $request->filled('branch_id')
            ? $request->integer('branch_id')
            : ($branches->firstWhere('is_active', true)?->id ?: $branches->first()?->id);
        $rows = $this->productRows($branchId, $request->only(['search', 'category_id', 'stock_status']));
        $branchName = $branches->firstWhere('id', $branchId)?->name ?: 'branch';

        return response()->streamDownload(function () use ($rows) {
            echo "\xEF\xBB\xBF";
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Product', 'Category', 'Stock', 'Stock status', 'Branch price', 'Discount price', 'Available']);
            foreach ($rows as $row) {
                fputcsv($handle, [$row['name'], $row['category'], $row['stock'] ?? 'Unlimited', $row['stock_status'], $row['price'], $row['discount_price'], $row['is_available'] ? 'Yes' : 'No']);
            }
            fclose($handle);
        }, 'lakeview-stock-' . preg_replace('/[^A-Za-z0-9]+/', '-', strtolower($branchName)) . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function productRows(?int $branchId, array $filters)
    {
        if (!$branchId) {
            return collect();
        }

        $query = DB::table('branch_product as bp')
            ->join('products as p', 'p.id', '=', 'bp.product_id')
            ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
            ->where('bp.branch_id', $branchId)
            ->select([
                'p.id', 'p.name', 'p.image', 'c.name as category',
                'bp.stock', 'bp.price', 'bp.discount_price', 'bp.is_available',
            ])
            ->when($filters['search'] ?? null, fn ($q, $search) => $q->where('p.name', 'like', '%' . $search . '%'))
            ->when($filters['category_id'] ?? null, fn ($q, $categoryId) => $q->where('p.category_id', $categoryId));

        if (($filters['stock_status'] ?? '') === 'out') {
            $query->where('bp.stock', 0);
        } elseif (($filters['stock_status'] ?? '') === 'low') {
            $query->whereBetween('bp.stock', [1, self::LOW_STOCK_LIMIT]);
        } elseif (($filters['stock_status'] ?? '') === 'healthy') {
            $query->where(function ($q) {
                $q->whereNull('bp.stock')->orWhere('bp.stock', '>', self::LOW_STOCK_LIMIT);
            });
        }

        return $query->orderBy('p.name')->get()->map(function ($row) {
            $stock = $row->stock === null ? null : (int) $row->stock;
            return [
                'id' => (int) $row->id,
                'name' => $row->name,
                'image' => $row->image,
                'category' => $row->category ?: 'Uncategorized',
                'stock' => $stock,
                'stock_status' => $stock === null ? 'unlimited' : ($stock === 0 ? 'out' : ($stock <= self::LOW_STOCK_LIMIT ? 'low' : 'healthy')),
                'price' => $row->price,
                'discount_price' => $row->discount_price,
                'is_available' => (bool) $row->is_available,
            ];
        })->values();
    }

    private function summary(?int $branchId): array
    {
        if (!$branchId) {
            return ['assigned' => 0, 'live' => 0, 'units' => 0, 'low' => 0, 'out' => 0];
        }

        $base = DB::table('branch_product')->where('branch_id', $branchId);
        return [
            'assigned' => (clone $base)->count(),
            'live' => (clone $base)->where('is_available', true)->count(),
            'units' => (int) ((clone $base)->whereNotNull('stock')->sum('stock')),
            'low' => (clone $base)->whereBetween('stock', [1, self::LOW_STOCK_LIMIT])->count(),
            'out' => (clone $base)->where('stock', 0)->count(),
        ];
    }

    private function availableBranches(Request $request)
    {
        return Branch::when($request->user()->adminBranchId(), fn ($builder, $branchId) => $builder->whereKey($branchId))
            ->orderBy('sort_order')->get(['id', 'name', 'is_active']);
    }
}
