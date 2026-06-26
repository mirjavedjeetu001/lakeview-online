<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CustomCakeOrder;
use App\Models\DeliveryArea;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CustomCakeController extends Controller
{
    public function index()
    {
        $branches = Branch::where('is_active', true)->orderBy('sort_order')->get();
        $deliveryAreas = DeliveryArea::where('is_active', true)->orderBy('zone_type')->orderBy('name')->get();
        return Inertia::render('CustomCake/Index', [
            'branches' => $branches,
            'deliveryAreas' => $deliveryAreas,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'delivery_type' => 'required|in:pickup,home_delivery',
            'delivery_area_id' => 'nullable|exists:delivery_areas,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'nullable|string|max:500',
            'cake_type' => 'nullable|string|max:255',
            'cake_size' => 'nullable|string|max:255',
            'cake_flavor' => 'nullable|string|max:255',
            'message_on_cake' => 'nullable|string|max:255',
            'delivery_date' => 'required|date|after:today',
            'delivery_time' => 'nullable|string',
            'design_image' => 'nullable|image|max:5120',
            'notes' => 'nullable|string|max:1000',
        ]);

        // For home delivery, auto-assign branch from delivery area
        if ($validated['delivery_type'] === 'home_delivery' && !empty($validated['delivery_area_id'])) {
            $area = DeliveryArea::find($validated['delivery_area_id']);
            if ($area) {
                $validated['branch_id'] = $area->branch_id;
            }
        }

        if (empty($validated['branch_id'])) {
            return redirect()->back()->withErrors(['branch_id' => 'Please select a branch or delivery area.']);
        }

        $deliveryCharge = 0;
        if ($validated['delivery_type'] === 'home_delivery' && !empty($validated['delivery_area_id'])) {
            $area = DeliveryArea::find($validated['delivery_area_id']);
            if ($area) {
                $deliveryCharge = $area->delivery_charge;
            }
        }

        $imagePath = null;
        if ($request->hasFile('design_image')) {
            $imagePath = $request->file('design_image')->store('custom-cakes', 'public');
        }

        // Auto-create or find user by phone number
        $user = User::firstOrCreate(
            ['phone' => $validated['customer_phone']],
            [
                'name' => $validated['customer_name'],
                'email' => 'customer_' . Str::random(8) . '@lakeview.local',
                'password' => Hash::make(Str::random(16)),
                'role' => 'customer',
            ]
        );

        if ($user->name !== $validated['customer_name']) {
            $user->update(['name' => $validated['customer_name']]);
        }

        // Auto-login the user
        if (!Auth::check()) {
            Auth::login($user);
        }

        $order = CustomCakeOrder::create([
            'user_id' => $user->id,
            'branch_id' => $validated['branch_id'],
            'delivery_type' => $validated['delivery_type'],
            'delivery_area_id' => $validated['delivery_area_id'] ?? null,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
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
