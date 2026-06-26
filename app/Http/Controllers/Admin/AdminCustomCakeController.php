<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomCakeOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminCustomCakeController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomCakeOrder::with(['branch', 'deliveryArea']);
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        $orders = $query->latest()->paginate(15);
        return Inertia::render('Admin/CustomCakes/Index', [
            'orders' => $orders,
            'filters' => $request->only(['status']),
        ]);
    }

    public function show(CustomCakeOrder $customCakeOrder)
    {
        $customCakeOrder->load(['branch', 'deliveryArea', 'deliveryMan']);
        $deliveryMen = \App\Models\DeliveryMan::where('is_active', true)->orderBy('name')->get();
        return Inertia::render('Admin/CustomCakes/Show', [
            'order' => $customCakeOrder,
            'deliveryMen' => $deliveryMen,
        ]);
    }

    public function updateStatus(Request $request, CustomCakeOrder $customCakeOrder)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,delivered,cancelled',
            'estimated_price' => 'nullable|numeric|min:0',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $customCakeOrder->update($validated);
        if (isset($validated['estimated_price'])) {
            $customCakeOrder->total = $validated['estimated_price'] + ($customCakeOrder->delivery_charge ?? 0);
            $customCakeOrder->save();
        }
        return redirect()->back()->with('success', 'Custom cake order updated successfully.');
    }

    public function assignDeliveryMan(Request $request, CustomCakeOrder $customCakeOrder)
    {
        $validated = $request->validate([
            'delivery_man_id' => 'nullable|exists:delivery_men,id',
        ]);
        $customCakeOrder->update($validated);
        return redirect()->back()->with('success', 'Delivery man assigned successfully.');
    }

    public function updatePaymentStatus(Request $request, CustomCakeOrder $customCakeOrder)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:unpaid,paid',
        ]);
        $customCakeOrder->update($validated);
        return redirect()->back()->with('success', 'Payment status updated successfully.');
    }

    public function verifyPayment(Request $request, CustomCakeOrder $customCakeOrder)
    {
        $customCakeOrder->update(['payment_verified' => true, 'payment_status' => 'paid']);
        return redirect()->back()->with('success', 'Payment verified successfully.');
    }
}
