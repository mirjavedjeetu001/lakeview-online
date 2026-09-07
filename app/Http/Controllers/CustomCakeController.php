<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CustomCakeOrder;
use App\Models\DeliveryArea;
use App\Models\User;
use App\Services\OrderNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CustomCakeController extends Controller
{
    public function index()
    {
        $branches = Branch::activeList();
        $deliveryAreas = DeliveryArea::where('is_active', true)->orderBy('zone_type')->orderBy('name')->get();
        return Inertia::render('CustomCake/Index', [
            'branches' => $branches,
            'deliveryAreas' => $deliveryAreas,
            'selectedBranchId' => (int) session('branch_id'),
            'deliveryMode' => 'both',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'delivery_type' => 'required|in:pickup,home_delivery',
            'delivery_area_id' => ['nullable', 'required_if:delivery_type,home_delivery', Rule::exists('delivery_areas', 'id')->where(fn ($query) => $query->where('is_active', true)->where('branch_id', $request->input('branch_id')))],
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_address' => 'nullable|required_if:delivery_type,home_delivery|string|max:500',
            'cake_type' => 'nullable|string|max:255',
            'cake_size' => 'nullable|string|max:255',
            'cake_flavor' => 'nullable|string|max:255',
            'message_on_cake' => 'nullable|string|max:255',
            'delivery_date' => 'required|date|after:today',
            'delivery_time' => 'nullable|string',
            'design_image' => 'nullable|image|max:2048',
            'notes' => 'nullable|string|max:1000',
        ]);

        $branch = Branch::whereKey($validated['branch_id'])
            ->where('is_active', true)
            ->first();

        if (!$branch) {
            return redirect()->back()->withErrors(['branch_id' => 'Please select an active branch.'])->withInput();
        }

        if ($validated['delivery_type'] === 'pickup') {
            $validated['delivery_area_id'] = null;
        }

        $deliveryCharge = 0;
        $area = null;
        if ($validated['delivery_type'] === 'home_delivery' && !empty($validated['delivery_area_id'])) {
            $area = DeliveryArea::whereKey($validated['delivery_area_id'])
                ->where('branch_id', $branch->id)
                ->where('is_active', true)
                ->first();
            $deliveryCharge = (float) ($area?->delivery_charge ?? 0);
        }

        $imagePath = null;
        if ($request->hasFile('design_image')) {
            $imagePath = $request->file('design_image')->store('custom-cakes', 'public');
        }

        $user = User::where('phone', $validated['customer_phone'])->first();
        if (!$user && !empty($validated['customer_email'])) {
            $user = User::where('email', $validated['customer_email'])->first();
        }
        if (!$user) {
            $user = User::create([
                'name' => $validated['customer_name'],
                'phone' => $validated['customer_phone'],
                'email' => $validated['customer_email'] ?? null,
                'password' => Hash::make(str()->random(16)),
                'role' => 'customer',
                'is_active' => true,
            ]);
        } elseif ($validated['customer_email'] && !$user->email) {
            $user->update(['email' => $validated['customer_email']]);
        }

        if ($user->name !== $validated['customer_name']) {
            $user->update(['name' => $validated['customer_name']]);
        }

        // Auto-login the user
        if (!Auth::check() && $user->is_active) {
            Auth::login($user);
        }

        $order = CustomCakeOrder::create([
            'user_id' => $user->id,
            'branch_id' => $validated['branch_id'],
            'delivery_type' => $validated['delivery_type'],
            'delivery_area_id' => $validated['delivery_area_id'] ?? null,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_email' => $validated['customer_email'] ?? null,
            'customer_address' => $validated['customer_address'] ?? null,
            'cake_type' => $validated['cake_type'] ?? null,
            'cake_size' => $validated['cake_size'] ?? null,
            'cake_flavor' => $validated['cake_flavor'] ?? null,
            'message_on_cake' => $validated['message_on_cake'] ?? null,
            'delivery_date' => $validated['delivery_date'],
            'delivery_time' => $validated['delivery_time'] ?? null,
            'design_image' => $imagePath,
            'estimated_price' => 0,
            'delivery_charge' => $deliveryCharge,
            'total' => $deliveryCharge,
            'payment_method' => 'cash_on_delivery',
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        app(OrderNotificationService::class)->sendCustomCakeCreated($order);

        return redirect()->route('custom-cake.success', ['order' => $order->id]);
    }

    public function success($orderId)
    {
        $order = CustomCakeOrder::with(['branch', 'deliveryArea'])->findOrFail($orderId);
        return Inertia::render('CustomCake/Success', [
            'order' => $order,
        ]);
    }
}
