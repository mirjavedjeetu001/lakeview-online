<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\DeliveryMan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminDeliveryManController extends Controller
{
    public function index()
    {
        $deliveryMen = DeliveryMan::with('branch')->latest()->get();
        $branches = Branch::where('is_active', true)->orderBy('sort_order')->get();
        return Inertia::render('Admin/DeliveryMen/Index', [
            'deliveryMen' => $deliveryMen,
            'branches' => $branches,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:delivery_men,phone',
            'branch_id' => 'nullable|exists:branches,id',
            'is_active' => 'boolean',
        ]);

        DeliveryMan::create($validated);
        return redirect()->back()->with('success', 'Delivery man added successfully.');
    }

    public function update(Request $request, DeliveryMan $deliveryMan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:delivery_men,phone,' . $deliveryMan->id,
            'branch_id' => 'nullable|exists:branches,id',
            'is_active' => 'boolean',
        ]);

        $deliveryMan->update($validated);
        return redirect()->back()->with('success', 'Delivery man updated successfully.');
    }

    public function destroy(DeliveryMan $deliveryMan)
    {
        $deliveryMan->delete();
        return redirect()->back()->with('success', 'Delivery man removed.');
    }
}
