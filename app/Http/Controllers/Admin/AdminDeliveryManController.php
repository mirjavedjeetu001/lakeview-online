<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\DeliveryMan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminDeliveryManController extends Controller
{
    public function index(Request $request)
    {
        $deliveryMen = DeliveryMan::with('branch')->when($request->user()->adminBranchId(), fn ($builder, $id) => $builder->where('branch_id', $id))->latest()->get();
        $branches = Branch::when($request->user()->adminBranchId(), fn ($builder, $id) => $builder->whereKey($id))->where('is_active', true)->orderBy('sort_order')->get();
        return Inertia::render('Admin/DeliveryMen/Index', [
            'deliveryMen' => $deliveryMen,
            'branches' => $branches,
        ]);
    }

    public function store(Request $request)
    {
        abort_if($request->user()->adminBranchId() && (int) $request->input('branch_id') !== (int) $request->user()->adminBranchId(), 403, 'Choose your assigned branch.');
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
        abort_unless($request->user()->canAccessBranch((int) $deliveryMan->branch_id), 403, 'This delivery man is outside your branch access.');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:delivery_men,phone,' . $deliveryMan->id,
            'branch_id' => 'nullable|exists:branches,id',
            'is_active' => 'boolean',
        ]);
        abort_if($request->user()->adminBranchId() && (int) $validated['branch_id'] !== (int) $request->user()->adminBranchId(), 403, 'Choose your assigned branch.');

        $deliveryMan->update($validated);
        return redirect()->back()->with('success', 'Delivery man updated successfully.');
    }

    public function destroy(Request $request, DeliveryMan $deliveryMan)
    {
        abort_unless($request->user()->canAccessBranch((int) $deliveryMan->branch_id), 403, 'This delivery man is outside your branch access.');
        $deliveryMan->delete();
        return redirect()->back()->with('success', 'Delivery man removed.');
    }
}
