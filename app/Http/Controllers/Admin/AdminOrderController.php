<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryMan;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['branch', 'deliveryArea', 'items', 'coupon', 'deliveryMan']);
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        $orders = $query->latest()->paginate(15);
        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'filters' => $request->only(['status']),
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['branch', 'deliveryArea', 'items.product', 'coupon', 'deliveryMan']);
        $deliveryMen = DeliveryMan::with('branch')->where('is_active', true)->get();
        return Inertia::render('Admin/Orders/Show', [
            'order' => $order,
            'deliveryMen' => $deliveryMen,
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,out_for_delivery,delivered,cancelled',
        ]);
        $order->update($validated);
        return redirect()->back()->with('success', 'Order status updated successfully.');
    }

    public function assignDeliveryMan(Request $request, Order $order)
    {
        $validated = $request->validate([
            'delivery_man_id' => 'nullable|exists:delivery_men,id',
        ]);
        $order->update($validated);
        return redirect()->back()->with('success', 'Delivery man assigned successfully.');
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:unpaid,paid',
        ]);
        $order->update($validated);
        return redirect()->back()->with('success', 'Payment status updated successfully.');
    }

    public function verifyPayment(Request $request, Order $order)
    {
        $order->update(['payment_verified' => true, 'payment_status' => 'paid']);
        return redirect()->back()->with('success', 'Payment verified successfully.');
    }
}

