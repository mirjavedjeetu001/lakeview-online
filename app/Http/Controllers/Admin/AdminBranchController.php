<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AdminBranchController extends Controller
{
    public function index()
    {
        $branches = Branch::withCount(['deliveryAreas', 'orders'])->orderBy('sort_order')->get();
        return Inertia::render('Admin/Branches/Index', ['branches' => $branches]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phones' => 'nullable|array',
            'phones.*' => 'string|max:20',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('branches', 'public');
        }
        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(5);

        Branch::create($validated);
        return redirect()->back()->with('success', 'Branch created successfully.');
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phones' => 'nullable|array',
            'phones.*' => 'string|max:20',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('branches', 'public');
        }

        $branch->update($validated);
        return redirect()->back()->with('success', 'Branch updated successfully.');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->back()->with('success', 'Branch deleted successfully.');
    }
}
