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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'delivery_mode' => 'required|in:both,pickup,home_delivery',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }
        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(5);

        Category::create($validated);
        return redirect()->back()->with('success', 'Category created successfully.');
    }

    public function update(Request $request, Category $category)
    {
        $oldImage = $category->image;
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'delivery_mode' => 'required|in:both,pickup,home_delivery',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($validated);
        if ($request->hasFile('image') && $oldImage && !str_starts_with($oldImage, 'http')) {
            Storage::disk('public')->delete($oldImage);
        }
        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $image = $category->image;
        $category->delete();
        if ($image && !str_starts_with($image, 'http')) {
            Storage::disk('public')->delete($image);
        }
        return redirect()->back()->with('success', 'Category deleted successfully.');
    }
}
