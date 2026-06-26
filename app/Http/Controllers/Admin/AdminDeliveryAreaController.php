<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\DeliveryArea;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminDeliveryAreaController extends Controller
{
    public function index()
    {
        $areas = DeliveryArea::orderBy('zone_type')->orderBy('name')->get();
        return Inertia::render('Admin/DeliveryAreas/Index', [
            'areas' => $areas,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'zone_type' => 'required|in:sadar,outside_sadar',
            'delivery_charge' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        DeliveryArea::create($validated);
        return redirect()->back()->with('success', 'Delivery area created successfully.');
    }

    public function update(Request $request, DeliveryArea $deliveryArea)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'zone_type' => 'required|in:sadar,outside_sadar',
            'delivery_charge' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $deliveryArea->update($validated);
        return redirect()->back()->with('success', 'Delivery area updated successfully.');
    }

    public function destroy(DeliveryArea $deliveryArea)
    {
        $deliveryArea->delete();
        return redirect()->back()->with('success', 'Delivery area deleted successfully.');
    }
}
