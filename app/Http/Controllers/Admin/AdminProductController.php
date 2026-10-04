<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'branches'])->withCount('branches');
        $adminBranchId = $request->user()->adminBranchId();

        if ($adminBranchId) {
            $query->whereHas('branches', fn ($builder) => $builder->whereKey($adminBranchId));
        }

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
            'branches' => Branch::when($adminBranchId, fn ($builder) => $builder->whereKey($adminBranchId))->orderBy('sort_order')->get(),
            'filters' => $request->only(['search', 'category_id']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);
        if (is_array($validated['cake_sizes'] ?? null)) {
            $validated['cake_sizes'] = $this->storeCakeSizeImages($request, $validated['cake_sizes'], []);
        }
        $gallery = $this->storeGallery($request);
        unset($validated['gallery'], $validated['remove_gallery'], $validated['cake_size_images']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }
        $validated['gallery'] = $gallery;
        if (!$request->hasFile('image') && $gallery) {
            $validated['image'] = $gallery[0];
        }

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(5);
        $product = Product::create($validated);
        $this->syncBranches($product, $this->branchAssignments($request), !$request->has('branch_assignments'));

        return redirect()->back()->with('success', 'Product created and branch availability saved.');
    }

    public function update(Request $request, Product $product)
    {
        $this->ensureProductAccess($request, $product);
        $oldImage = $product->image;
        $currentCakeSizes = $product->cake_sizes ?: [];
        $validated = $this->validateProduct($request);
        if (is_array($validated['cake_sizes'] ?? null)) {
            $validated['cake_sizes'] = $this->storeCakeSizeImages($request, $validated['cake_sizes'], $currentCakeSizes);
        }
        $currentGallery = $product->gallery ?: [];
        $removeGallery = json_decode($request->input('remove_gallery', '[]'), true) ?: [];
        $remainingGallery = array_values(array_diff($currentGallery, $removeGallery));
        foreach (array_diff($currentGallery, $remainingGallery) as $removedGalleryImage) {
            if (is_string($removedGalleryImage) && $removedGalleryImage !== '' && !str_starts_with($removedGalleryImage, 'http')) {
                Storage::disk('public')->delete($removedGalleryImage);
            }
        }
        $newGallery = $this->storeGallery($request);
        unset($validated['gallery'], $validated['remove_gallery'], $validated['cake_size_images']);

        $validated['gallery'] = array_values(array_merge($remainingGallery, $newGallery));
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        } elseif ($oldImage && in_array($oldImage, $removeGallery, true)) {
            $validated['image'] = $newGallery[0] ?? ($remainingGallery[0] ?? null);
        } elseif (!$oldImage && $newGallery) {
            $validated['image'] = $newGallery[0];
        }

        $product->update($validated);

        if ($oldImage && $oldImage !== $product->image && !in_array($oldImage, $validated['gallery'], true) && !in_array($oldImage, $removeGallery, true) && !str_starts_with($oldImage, 'http')) {
            Storage::disk('public')->delete($oldImage);
        }

        if ($request->has('branch_assignments')) {
            $this->syncBranches($product, $this->branchAssignments($request), false);
        }

        return redirect()->back()->with('success', 'Product updated and branch availability saved.');
    }

    public function destroy(Product $product)
    {
        $this->ensureProductAccess(request(), $product);
        $imagePaths = collect([$product->image])
            ->merge($product->gallery ?: [])
            ->merge(collect($product->cake_sizes ?: [])->flatMap(fn ($size) => is_array($size) ? ($size['images'] ?? []) : []))
            ->filter(fn ($path) => is_string($path) && $path !== '' && !str_starts_with($path, 'http'))
            ->unique()
            ->values()
            ->all();
        $product->delete();
        Storage::disk('public')->delete($imagePaths);

        return redirect()->back()->with('success', 'Product deleted successfully.');
    }

    private function validateProduct(Request $request): array
    {
        if (is_string($request->input('cake_sizes'))) {
            $request->merge(['cake_sizes' => json_decode($request->input('cake_sizes'), true) ?: []]);
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|max:4096',
            'remove_gallery' => 'nullable|json',
            'delivery_mode' => 'required|in:inherit,both,pickup,home_delivery',
            'national_delivery' => 'boolean',
            'customization_mode' => 'required|in:ready_only,ready_and_customization,customization_only',
            'cake_sizes' => 'nullable|array',
            'cake_sizes.*.label' => 'required|string|max:40',
            'cake_sizes.*.price' => 'required|numeric|min:0',
            'cake_sizes.*.discount_price' => 'nullable|numeric|min:0',
            'cake_sizes.*.images' => 'nullable|array',
            'cake_sizes.*.images.*' => 'string|max:500',
            'cake_size_images' => 'nullable|array',
            'cake_size_images.*' => 'nullable|array',
            'cake_size_images.*.*' => 'image|max:4096',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $categoryName = strtolower((string) Category::whereKey($validated['category_id'])->value('name'));
        $this->validateCategoryForBranches($request, (int) $validated['category_id']);
        $isCake = str_contains($categoryName, 'cake');
        if (!$isCake) {
            $validated['customization_mode'] = 'ready_only';
            $validated['cake_sizes'] = null;
        } else {
            $validated['cake_sizes'] = collect($validated['cake_sizes'] ?? [])
                ->map(fn (array $size) => [
                    'label' => trim($size['label']),
                    'price' => (float) $size['price'],
                    'discount_price' => ($size['discount_price'] ?? null) === null || ($size['discount_price'] ?? null) === '' ? null : (float) $size['discount_price'],
                    'images' => collect($size['images'] ?? [])
                        ->filter(fn ($image) => is_string($image) && trim($image) !== '')
                        ->map(fn ($image) => trim($image))
                        ->values()
                        ->all(),
                ])
                ->filter(fn (array $size) => $size['label'] !== '')
                ->values()
                ->all();
        }

        return $validated;
    }

    private function validateCategoryForBranches(Request $request, int $categoryId): void
    {
        $rawAssignments = $request->input('branch_assignments');
        $hasExplicitAssignments = $request->has('branch_assignments');
        if (is_string($rawAssignments)) {
            $rawAssignments = json_decode($rawAssignments, true) ?: [];
        }

        $adminBranchId = $request->user()->adminBranchId();
        $allowedBranchIds = Branch::when($adminBranchId, fn ($builder) => $builder->whereKey($adminBranchId))
            ->pluck('id')->map(fn ($id) => (int) $id)->all();
        $selectedBranchIds = collect(is_array($rawAssignments) ? $rawAssignments : [])
            ->filter(fn ($assignment) => is_array($assignment) && in_array((int) ($assignment['branch_id'] ?? 0), $allowedBranchIds, true))
            ->pluck('branch_id')->map(fn ($id) => (int) $id)->unique()->values();

        if ($hasExplicitAssignments && $selectedBranchIds->isEmpty()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'branch_assignments' => 'Select at least one branch for this product.',
            ]);
        }

        if (!$hasExplicitAssignments) {
            $selectedBranchIds = collect($allowedBranchIds);
        }

        $categoryType = Category::whereKey($categoryId)->value('business_type') ?: 'both';
        if ($selectedBranchIds->isEmpty()) {
            return;
        }

        $foodTypes = ['bakery', 'fast_food', 'restaurant'];
        $invalidBranch = Branch::whereIn('id', $selectedBranchIds->all())
            ->get()
            ->first(function (Branch $branch) use ($categoryType, $foodTypes) {
                $branchType = $branch->business_type ?: 'both';

                if ($branchType === 'both') {
                    return in_array($categoryType, ['pharmacy', 'clinic'], true);
                }

                if (in_array($branchType, $foodTypes, true)) {
                    return !in_array($categoryType, array_merge($foodTypes, ['both']), true);
                }

                return $branchType !== $categoryType;
            });

        if ($invalidBranch) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'category_id' => "The {$categoryType} category cannot be assigned to the {$invalidBranch->business_type} branch. Select a matching branch or use an All types category.",
            ]);
        }
    }

    private function storeGallery(Request $request): array
    {
        return collect($request->file('gallery', []))
            ->filter()
            ->map(fn ($file) => $file->store('products/gallery', 'public'))
            ->values()
            ->all();
    }

    private function storeCakeSizeImages(Request $request, array $sizes, array $currentSizes): array
    {
        $oldImages = collect($currentSizes)
            ->flatMap(fn ($size) => is_array($size) ? ($size['images'] ?? []) : [])
            ->filter(fn ($image) => is_string($image) && $image !== '')
            ->values();
        $allowedExisting = $oldImages->flip();
        $storedImages = [];

        $normalized = collect($sizes)
            ->values()
            ->map(function (array $size, int $index) use ($request, $allowedExisting, &$storedImages) {
                $existing = collect($size['images'] ?? [])
                    ->filter(fn ($image) => is_string($image) && ($allowedExisting->has($image) || str_starts_with($image, 'http')))
                    ->values()
                    ->all();
                $uploaded = collect($request->file("cake_size_images.$index", []))
                    ->filter()
                    ->map(fn ($file) => $file->store('products/cake-sizes', 'public'))
                    ->values()
                    ->all();
                $images = array_values(array_merge($existing, $uploaded));
                $storedImages = array_merge($storedImages, $images);

                return [
                    'label' => trim((string) ($size['label'] ?? '')),
                    'price' => (float) ($size['price'] ?? 0),
                    'discount_price' => ($size['discount_price'] ?? null) === null || ($size['discount_price'] ?? null) === '' ? null : (float) $size['discount_price'],
                    'images' => $images,
                ];
            })
            ->filter(fn (array $size) => $size['label'] !== '')
            ->values()
            ->all();

        $keep = collect($storedImages);
        $oldImages
            ->filter(fn ($image) => !str_starts_with($image, 'http') && !$keep->contains($image))
            ->each(fn ($image) => Storage::disk('public')->delete($image));

        return $normalized;
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

        $adminBranchId = $request->user()->adminBranchId();
        $branchIds = Branch::when($adminBranchId, fn ($builder) => $builder->whereKey($adminBranchId))
            ->pluck('id')->map(fn ($id) => (int) $id)->all();

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
        $adminBranchId = request()->user()->adminBranchId();

        // A branch-restricted admin may update its own pivot row, but must not
        // detach this product from another outlet while saving the form.
        if ($adminBranchId && !$assignments && $fallbackToActive) {
            $assignments = [$adminBranchId => ['is_available' => true]];
        }

        if ($adminBranchId) {
            if ($assignments) {
                $product->branches()->syncWithoutDetaching($assignments);
            }
            return;
        }

        if (!$assignments && $fallbackToActive) {
            $assignments = Branch::activeList()->pluck('id')->mapWithKeys(fn ($id) => [
                $id => ['is_available' => true],
            ])->all();
        }

        $product->branches()->sync($assignments);
    }

    private function ensureProductAccess(Request $request, Product $product): void
    {
        $adminBranchId = $request->user()->adminBranchId();

        if ($adminBranchId && !$product->branches()->whereKey($adminBranchId)->exists()) {
            abort(403, 'This product is not assigned to your branch.');
        }
    }
}
