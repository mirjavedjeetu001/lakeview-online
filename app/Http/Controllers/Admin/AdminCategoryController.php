<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('sort_order')->get();
        return Inertia::render('Admin/Categories/Index', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateCategory($request);
        $gallery = $this->storeGallery($request);
        unset($validated['gallery'], $validated['remove_gallery']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        } elseif ($gallery) {
            $validated['image'] = $gallery[0];
        }
        $validated['gallery'] = $gallery;
        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(5);

        Category::create($validated);
        return redirect()->back()->with('success', 'Category created successfully.');
    }

    public function update(Request $request, Category $category)
    {
        $oldImage = $category->image;
        $currentGallery = $category->gallery ?: [];
        $removeGallery = json_decode($request->input('remove_gallery', '[]'), true) ?: [];
        $remainingGallery = array_values(array_diff($currentGallery, $removeGallery));

        foreach (array_diff($currentGallery, $remainingGallery) as $removedImage) {
            if (is_string($removedImage) && $removedImage !== '' && !str_starts_with($removedImage, 'http')) {
                Storage::disk('public')->delete($removedImage);
            }
        }

        $validated = $this->validateCategory($request);
        $newGallery = $this->storeGallery($request);
        unset($validated['gallery'], $validated['remove_gallery']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        } elseif ($oldImage && in_array($oldImage, $removeGallery, true)) {
            $validated['image'] = $newGallery[0] ?? ($remainingGallery[0] ?? null);
        } elseif (!$oldImage && $newGallery) {
            $validated['image'] = $newGallery[0];
        }
        $validated['gallery'] = array_values(array_merge($remainingGallery, $newGallery));

        $category->update($validated);
        if ($request->hasFile('image') && $oldImage && !str_starts_with($oldImage, 'http')) {
            Storage::disk('public')->delete($oldImage);
        }
        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $imagePaths = collect([$category->image])
            ->merge($category->gallery ?: [])
            ->filter(fn ($path) => is_string($path) && $path !== '' && !str_starts_with($path, 'http'))
            ->unique()
            ->values()
            ->all();

        $category->delete();
        Storage::disk('public')->delete($imagePaths);

        return redirect()->back()->with('success', 'Category deleted successfully.');
    }

    private function validateCategory(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|max:4096',
            'remove_gallery' => 'nullable|json',
            'delivery_mode' => 'required|in:both,pickup,home_delivery',
            'business_type' => 'required|in:bakery,fast_food,restaurant,pharmacy,clinic,both',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);
    }

    private function storeGallery(Request $request): array
    {
        return collect($request->file('gallery', []))
            ->filter()
            ->map(fn ($file) => $file->store('categories/gallery', 'public'))
            ->values()
            ->all();
    }
}
