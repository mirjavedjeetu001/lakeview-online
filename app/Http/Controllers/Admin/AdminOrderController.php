<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\DeliveryMan;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['branch', 'deliveryArea', 'items', 'coupon', 'deliveryMan']);
        $branchId = $request->user()->adminBranchId() ?: ($request->filled('branch_id') ? $request->integer('branch_id') : null);
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        $orders = $query->latest()->paginate(15);
        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'branches' => Branch::when($request->user()->adminBranchId(), fn ($builder, $id) => $builder->whereKey($id))
                ->orderBy('sort_order')->get(['id', 'name']),
            'filters' => ['status' => $request->input('status', ''), 'branch_id' => $branchId],
        ]);
    }

    public function show(Order $order)
    {
        abort_unless(request()->user()->canAccessBranch((int) $order->branch_id), 403, 'This order is outside your branch access.');
        $order->load(['branch', 'deliveryArea', 'items.product', 'coupon', 'deliveryMan']);
        $deliveryMen = DeliveryMan::with('branch')->where('is_active', true)
            ->when(request()->user()->adminBranchId(), fn ($builder, $branchId) => $builder->where(function ($query) use ($branchId) {
                $query->where('branch_id', $branchId)->orWhereNull('branch_id');
            }))->get();
        return Inertia::render('Admin/Orders/Show', [
            'order' => $order,
            'deliveryMen' => $deliveryMen,
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $this->ensureOrderAccess($request, $order);
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,out_for_delivery,delivered,cancelled',
        ]);
        $order->update($validated);
        return redirect()->back()->with('success', 'Order status updated successfully.');
    }

    public function assignDeliveryMan(Request $request, Order $order)
    {
        $this->ensureOrderAccess($request, $order);
        $validated = $request->validate([
            'delivery_man_id' => 'nullable|exists:delivery_men,id',
        ]);
        $order->update($validated);
        return redirect()->back()->with('success', 'Delivery man assigned successfully.');
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $this->ensureOrderAccess($request, $order);
        $validated = $request->validate([
            'payment_status' => 'required|in:unpaid,paid',
            'advance_amount' => 'nullable|numeric|min:0',
            'transaction_id' => 'nullable|string|max:255',
            'payment_verified' => 'nullable|boolean',
        ]);
        $paidAmount = array_key_exists('advance_amount', $validated)
            ? min((float) ($validated['advance_amount'] ?? 0), (float) $order->total)
            : (float) ($order->advance_amount ?? 0);
        $isPaid = $validated['payment_status'] === 'paid';
        $order->update([
            'payment_status' => $isPaid ? 'paid' : ($paidAmount >= (float) $order->total ? 'paid' : 'unpaid'),
            'advance_amount' => $isPaid ? (float) $order->total : $paidAmount,
            'transaction_id' => $validated['transaction_id'] ?? null,
            'payment_verified' => $request->boolean('payment_verified'),
        ]);
        return redirect()->back()->with('success', 'Payment details updated successfully.');
    }

    public function verifyPayment(Request $request, Order $order)
    {
        $this->ensureOrderAccess($request, $order);
        $order->update(['payment_verified' => true, 'payment_status' => 'paid']);
        return redirect()->back()->with('success', 'Payment verified successfully.');
    }

    public function updateDiscount(Request $request, Order $order)
    {
        $this->ensureOrderAccess($request, $order);
        $validated = $request->validate([
            'discount' => 'required|numeric|min:0',
        ]);

        $maximumDiscount = (float) $order->subtotal + (float) $order->delivery_charge;
        $discount = min((float) $validated['discount'], $maximumDiscount);

        $order->update([
            'discount' => $discount,
            'total' => $maximumDiscount - $discount,
        ]);

        return redirect()->back()->with('success', 'Order discount updated successfully.');
    }

    private function ensureOrderAccess(Request $request, Order $order): void
    {
        abort_unless($request->user()->canAccessBranch((int) $order->branch_id), 403, 'This order is outside your branch access.');
    }
}
