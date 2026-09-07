<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Coupon;
use App\Models\DeliveryArea;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function index()
    {
        $selectedBranchId = (int) session('branch_id');
        $branches = Branch::activeList();
        $deliveryAreas = DeliveryArea::where('is_active', true)->orderBy('zone_type')->orderBy('name')->get();
        $settings = \App\Models\Setting::getAllByGroup();
        return Inertia::render('Checkout/Index', [
            'branches' => $branches,
            'deliveryAreas' => $deliveryAreas,
            'selectedBranchId' => $selectedBranchId,
            'minOrder' => [
                'sadar' => (float) ($settings['min_order_sadar'] ?? 500),
                'outside' => (float) ($settings['min_order_outside'] ?? 1000),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'delivery_type' => 'required|in:pickup,home_delivery',
            'delivery_area_id' => [
                'nullable',
                'required_if:delivery_type,home_delivery',
                Rule::exists('delivery_areas', 'id')->where(fn ($query) => $query
                    ->where('is_active', true)
                    ->where('branch_id', $request->input('branch_id'))),
            ],
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_address' => 'nullable|required_if:delivery_type,home_delivery|string|max:500',
            'notes' => 'nullable|string|max:500',
            'payment_method' => 'nullable|in:cash_on_delivery',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'coupon_code' => 'nullable|string',
        ]);

        $branch = Branch::whereKey($validated['branch_id'])
            ->where('is_active', true)
            ->first();

        if (!$branch) {
            return redirect()->back()->withErrors(['branch_id' => 'Please select an active branch.'])->withInput();
        }

        // Prices and availability are always re-read from the selected branch on the server.

        // For pickup, branch_id is required
        if ($validated['delivery_type'] === 'pickup' && empty($validated['branch_id'])) {
            return redirect()->back()->withErrors(['branch_id' => 'Please select a branch for pickup.']);
        }

        $subtotal = 0;
        $itemsData = [];
        $products = collect();
        foreach ($validated['items'] as $item) {
            $product = Product::forBranch($branch->id)
                ->with('category')
                ->where('products.id', $item['product_id'])
                ->first();

            if (!$product) {
                return redirect()->back()->withErrors([
                    'items' => 'One or more products are not available at the selected branch.',
                ])->withInput();
            }

            $products->push($product);

            if ($validated['delivery_type'] === 'home_delivery' && !$product->allow_home_delivery) {
                return redirect()->back()->withErrors([
                    'items' => "{$product->name} is available for pickup only. Please choose Pickup for this order.",
                ])->withInput();
            }

            if ($validated['delivery_type'] === 'pickup' && !$product->allow_pickup) {
                return redirect()->back()->withErrors([
                    'items' => "{$product->name} is available for home delivery only. Please choose Home Delivery for this order.",
                ])->withInput();
            }

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

            if (!$area) {
                return redirect()->back()->withErrors([
                    'delivery_area_id' => 'This delivery area is not served by the selected branch.',
                ])->withInput();
            }

            if ($area->zone_type === 'outside_sadar' && !$this->outsideSadarAllowed($products)) {
                return redirect()->back()->withErrors([
                    'delivery_area_id' => 'Outside-Sadar delivery is available when your bag includes a cake with eligible bakery or sweets items.',
                ])->withInput();
            }

            $deliveryCharge = $area->delivery_charge;
        }

        // Check minimum order amount
        if ($validated['delivery_type'] === 'home_delivery') {
            $minOrder = 0;
            if (!empty($validated['delivery_area_id'])) {
                $area = $area ?: DeliveryArea::whereKey($validated['delivery_area_id'])
                    ->where('branch_id', $branch->id)
                    ->where('is_active', true)
                    ->first();
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

        // Email is optional. Keep the order address even when the customer does not provide one.
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

        // Update name if user existed with different name
        if ($user->name !== $validated['customer_name']) {
            $user->update(['name' => $validated['customer_name']]);
        }

        // Auto-login the user
        if (!Auth::check() && $user->is_active) {
            Auth::login($user);
        }

        $order = Order::create([
            'user_id' => $user->id,
            'branch_id' => $validated['branch_id'],
            'delivery_area_id' => $validated['delivery_area_id'] ?? null,
            'coupon_id' => $couponId,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_email' => $validated['customer_email'] ?? null,
            'customer_address' => $validated['customer_address'] ?? null,
            'delivery_type' => $validated['delivery_type'],
            'subtotal' => $subtotal,
            'delivery_charge' => $deliveryCharge,
            'discount' => $discount,
            'total' => $total,
            'payment_method' => 'cash_on_delivery',
            'advance_amount' => 0,
            'transaction_id' => null,
            'payment_verified' => false,
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($itemsData as $itemData) {
            $itemData['order_id'] = $order->id;
            OrderItem::create($itemData);
        }

        app(OrderNotificationService::class)->sendOrderCreated($order);

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

    private function outsideSadarAllowed($products): bool
    {
        $hasCake = $products->contains(fn (Product $product) => $this->isCustomCakeProduct($product));

        return $hasCake && $products->every(fn (Product $product) => $this->isCustomCakeProduct($product) || $this->isCakeCompanion($product));
    }

    private function isCustomCakeProduct(Product $product): bool
    {
        $category = strtolower((string) ($product->category?->name ?? ''));
        $name = strtolower((string) $product->name);

        return in_array($category, ['cake', 'order cake'], true)
            || str_contains($category, 'custom cake')
            || str_contains($category, 'cake')
            || str_contains($name, 'cake');
    }

    private function isCakeCompanion(Product $product): bool
    {
        $category = strtolower((string) ($product->category?->name ?? ''));

        return (bool) preg_match('/sweet|bakery|biscuit|cookie|toast|dessert/', $category);
    }
}
