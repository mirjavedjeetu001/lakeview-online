<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\DeliveryArea;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminDeliveryAreaController extends Controller
{
    public function index(Request $request)
    {
        $areas = DeliveryArea::when($request->user()->adminBranchId(), fn ($builder, $id) => $builder->where('branch_id', $id))->orderBy('zone_type')->orderBy('name')->get();
        return Inertia::render('Admin/DeliveryAreas/Index', [
            'areas' => $areas,
            'branches' => Branch::when($request->user()->adminBranchId(), fn ($builder, $id) => $builder->whereKey($id))->orderBy('sort_order')->get(['id', 'name', 'is_active']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'zone_type' => 'required|in:sadar,outside_sadar',
            'delivery_charge' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);
        abort_unless($request->user()->canAccessBranch((int) $validated['branch_id']), 403, 'Choose a branch within your access.');

        DeliveryArea::create($validated);
        return redirect()->back()->with('success', 'Delivery area created successfully.');
    }

    public function update(Request $request, DeliveryArea $deliveryArea)
    {
        abort_unless($request->user()->canAccessBranch((int) $deliveryArea->branch_id), 403, 'This delivery area is outside your branch access.');
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'zone_type' => 'required|in:sadar,outside_sadar',
            'delivery_charge' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);
        abort_unless($request->user()->canAccessBranch((int) $validated['branch_id']), 403, 'Choose a branch within your access.');

        $deliveryArea->update($validated);
        return redirect()->back()->with('success', 'Delivery area updated successfully.');
    }

    public function destroy(Request $request, DeliveryArea $deliveryArea)
    {
        abort_unless($request->user()->canAccessBranch((int) $deliveryArea->branch_id), 403, 'This delivery area is outside your branch access.');
        $deliveryArea->delete();
        return redirect()->back()->with('success', 'Delivery area deleted successfully.');
    }
}
