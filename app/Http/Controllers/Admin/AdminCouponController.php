<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminCouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->paginate(15);
        return Inertia::render('Admin/Coupons/Index', [
            'coupons' => $coupons,
            'categories' => Category::where('is_active', true)->orderBy('sort_order')->get(['id', 'name']),
            'branches' => Branch::where('is_active', true)->orderBy('sort_order')->get(['id', 'name', 'business_type']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'description' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'branch_id' => 'nullable|exists:branches,id',
            'business_type' => 'nullable|in:bakery,fast_food,restaurant,pharmacy,clinic,both',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'boolean',
        ]);

        Coupon::create($validated);
        return redirect()->back()->with('success', 'Coupon created successfully.');
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'description' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'branch_id' => 'nullable|exists:branches,id',
            'business_type' => 'nullable|in:bakery,fast_food,restaurant,pharmacy,clinic,both',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'boolean',
        ]);

        $coupon->update($validated);
        return redirect()->back()->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->back()->with('success', 'Coupon deleted successfully.');
    }
}
