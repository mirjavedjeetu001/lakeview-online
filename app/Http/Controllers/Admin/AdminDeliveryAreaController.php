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
        $areas = DeliveryArea::when($request->user()->adminBranchId(), fn ($builder, $id) => $builder->where(function ($query) use ($id) {
            $query->whereNull('branch_id')->orWhere('branch_id', $id);
        }))->orderBy('service_scope')->orderBy('district')->orderBy('name')->get();
        return Inertia::render('Admin/DeliveryAreas/Index', [
            'areas' => $areas,
            'branches' => Branch::when($request->user()->adminBranchId(), fn ($builder, $id) => $builder->whereKey($id))->orderBy('sort_order')->get(['id', 'name', 'is_active']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'name' => 'required|string|max:255',
            'district' => 'nullable|string|max:120',
            'upazila' => 'nullable|string|max:120',
            'service_scope' => 'required|in:sadar,sadar_rural,outside_sadar,national',
            'zone_type' => 'nullable|in:sadar,outside_sadar',
            'delivery_charge' => 'required|numeric|min:0',
            'courier_name' => 'nullable|string|max:120',
            'is_active' => 'boolean',
        ]);
        if (!$validated['branch_id'] && $request->user()->adminBranchId()) {
            $validated['branch_id'] = $request->user()->adminBranchId();
        }
        $validated['zone_type'] = in_array($validated['service_scope'], ['sadar', 'sadar_rural'], true) ? 'sadar' : 'outside_sadar';
        abort_unless($request->user()->canAccessBranch($validated['branch_id'] ? (int) $validated['branch_id'] : null), 403, 'Choose a branch within your access.');

        DeliveryArea::create($validated);
        return redirect()->back()->with('success', 'Delivery area created successfully.');
    }

    public function update(Request $request, DeliveryArea $deliveryArea)
    {
        abort_unless($request->user()->canAccessBranch($deliveryArea->branch_id ? (int) $deliveryArea->branch_id : null), 403, 'This delivery area is outside your branch access.');
        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'name' => 'required|string|max:255',
            'district' => 'nullable|string|max:120',
            'upazila' => 'nullable|string|max:120',
            'service_scope' => 'required|in:sadar,sadar_rural,outside_sadar,national',
            'zone_type' => 'nullable|in:sadar,outside_sadar',
            'delivery_charge' => 'required|numeric|min:0',
            'courier_name' => 'nullable|string|max:120',
            'is_active' => 'boolean',
        ]);
        if (!$validated['branch_id'] && $request->user()->adminBranchId()) {
            $validated['branch_id'] = $request->user()->adminBranchId();
        }
        $validated['zone_type'] = in_array($validated['service_scope'], ['sadar', 'sadar_rural'], true) ? 'sadar' : 'outside_sadar';
        abort_unless($request->user()->canAccessBranch($validated['branch_id'] ? (int) $validated['branch_id'] : null), 403, 'Choose a branch within your access.');

        $deliveryArea->update($validated);
        return redirect()->back()->with('success', 'Delivery area updated successfully.');
    }

    public function destroy(Request $request, DeliveryArea $deliveryArea)
    {
        abort_unless($request->user()->canAccessBranch($deliveryArea->branch_id ? (int) $deliveryArea->branch_id : null), 403, 'This delivery area is outside your branch access.');
        $deliveryArea->delete();
        return redirect()->back()->with('success', 'Delivery area deleted successfully.');
    }
}
