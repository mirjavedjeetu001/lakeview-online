<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Coupon;
use App\Models\DeliveryArea;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function index()
    {
        $branches = Branch::with('deliveryAreas')->where('is_active', true)->orderBy('sort_order')->get();
        $deliveryAreas = DeliveryArea::where('is_active', true)->orderBy('zone_type')->orderBy('name')->get();
        $settings = \App\Models\Setting::getAllByGroup();
        return Inertia::render('Checkout/Index', [
            'branches' => $branches,
            'deliveryAreas' => $deliveryAreas,
            'minOrder' => [
                'sadar' => (float) ($settings['min_order_sadar'] ?? 500),
                'outside' => (float) ($settings['min_order_outside'] ?? 1000),
            ],
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
            'notes' => 'nullable|string|max:500',
            'payment_method' => 'nullable|in:cash_on_delivery,advance_payment',
            'advance_amount' => 'nullable|numeric|min:0',
            'transaction_id' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'coupon_code' => 'nullable|string',
        ]);

        // For home delivery, branch_id comes from customer selection
        // Delivery areas are now global (not per-branch), so charge is based on zone type

        // For pickup, branch_id is required
        if ($validated['delivery_type'] === 'pickup' && empty($validated['branch_id'])) {
            return redirect()->back()->withErrors(['branch_id' => 'Please select a branch for pickup.']);
        }

        $subtotal = 0;
        $itemsData = [];
        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            $price = $product->effective_price;
            $itemTotal = $price * $item['quantity'];
            $subtotal += $itemTotal;
            $itemsData[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'price' => $price,
                'quantity' => $item['quantity'],
                'total' => $itemTotal,
            ];
        }

        $deliveryCharge = 0;
        if ($validated['delivery_type'] === 'home_delivery' && !empty($validated['delivery_area_id'])) {
            $area = DeliveryArea::find($validated['delivery_area_id']);
            if ($area) {
                $deliveryCharge = $area->delivery_charge;
            }
        }

        // Check minimum order amount
        if ($validated['delivery_type'] === 'home_delivery') {
            $minOrder = 0;
            if (!empty($validated['delivery_area_id'])) {
                $area = DeliveryArea::find($validated['delivery_area_id']);
                if ($area) {
                    $minKey = $area->zone_type === 'sadar' ? 'min_order_sadar' : 'min_order_outside';
                    $minOrder = (float) (\App\Models\Setting::where('key', $minKey)->first()?->value ?? 0);
                }
            }
            if ($minOrder > 0 && $subtotal < $minOrder) {
                return redirect()->back()->withErrors([
                    'delivery_area_id' => "Minimum order amount for this area is ৳{$minOrder}. Your cart total is ৳{$subtotal}.",
                ])->withInput();
            }
        }

        $discount = 0;
        $couponId = null;
        if (!empty($validated['coupon_code'])) {
            $coupon = Coupon::where('code', $validated['coupon_code'])->first();
            if ($coupon && $coupon->isValid($subtotal)) {
                $discount = $coupon->calculateDiscount($subtotal);
                $couponId = $coupon->id;
                $coupon->increment('used_count');
            }
        }

        $total = $subtotal + $deliveryCharge - $discount;

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

        // Update name if user existed with different name
        if ($user->name !== $validated['customer_name']) {
            $user->update(['name' => $validated['customer_name']]);
        }

        // Auto-login the user
        if (!Auth::check()) {
            Auth::login($user);
        }

        $order = Order::create([
            'user_id' => $user->id,
            'branch_id' => $validated['branch_id'],
            'delivery_area_id' => $validated['delivery_area_id'] ?? null,
            'coupon_id' => $couponId,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_address' => $validated['customer_address'] ?? null,
            'delivery_type' => $validated['delivery_type'],
            'subtotal' => $subtotal,
            'delivery_charge' => $deliveryCharge,
            'discount' => $discount,
            'total' => $total,
            'payment_method' => $validated['payment_method'] ?? 'cash_on_delivery',
            'advance_amount' => $validated['advance_amount'] ?? 0,
            'transaction_id' => $validated['transaction_id'] ?? null,
            'payment_verified' => false,
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($itemsData as $itemData) {
            $itemData['order_id'] = $order->id;
            OrderItem::create($itemData);
        }

        return redirect()->route('checkout.success', ['order' => $order->id]);
    }

    public function success($orderId)
    {
        $order = Order::with(['items', 'branch', 'deliveryArea', 'coupon'])->findOrFail($orderId);
        return Inertia::render('Checkout/Success', [
            'order' => $order,
        ]);
    }

    public function trackOrder(Request $request)
    {
        $order = null;
        if ($request->has('order_number') && $request->order_number) {
            $order = Order::with(['items', 'branch', 'deliveryArea'])
                ->where('order_number', $request->order_number)
                ->first();
        }
        return Inertia::render('Checkout/TrackOrder', [
            'order' => $order,
        ]);
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', $request->code)->first();
        if (!$coupon || !$coupon->isValid($request->subtotal)) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired coupon.'], 422);
        }

        $discount = $coupon->calculateDiscount($request->subtotal);
        return response()->json([
            'success' => true,
            'discount' => $discount,
            'message' => 'Coupon applied successfully!',
        ]);
    }
}
