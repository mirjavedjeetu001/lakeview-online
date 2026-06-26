<?php

use App\Http\Controllers\Admin\AdminBranchController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\AdminCustomCakeController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDeliveryAreaController;
use App\Http\Controllers\Admin\AdminDeliveryManController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomCakeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Customer routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/custom-cake', [CustomCakeController::class, 'index'])->name('custom-cake.index');
Route::post('/custom-cake', [CustomCakeController::class, 'store'])->name('custom-cake.store');
Route::get('/custom-cake/success/{order}', [CustomCakeController::class, 'success'])->name('custom-cake.success');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/track-order', [CheckoutController::class, 'trackOrder'])->name('checkout.track');
Route::post('/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.apply-coupon');

// Auth routes
Route::get('/login', function () {
    return inertia('Auth/Login');
})->name('login');

Route::get('/register', function () {
    return inertia('Auth/Register');
})->name('register');

Route::post('/register', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20|unique:users,phone',
        'password' => 'required|string|min:6',
    ]);

    $user = \App\Models\User::create([
        'name' => $validated['name'],
        'phone' => $validated['phone'],
        'email' => 'customer_' . \Illuminate\Support\Str::random(8) . '@lakeview.local',
        'password' => $validated['password'],
        'role' => 'customer',
    ]);

    auth()->login($user);
    $request->session()->regenerate();

    return redirect()->intended('/');
})->name('register.post');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'login' => 'required|string',
        'password' => 'required',
    ]);

    $loginField = $validated['login'];
    $user = null;

    if (filter_var($loginField, FILTER_VALIDATE_EMAIL)) {
        $user = \App\Models\User::where('email', $loginField)->first();
    } else {
        $user = \App\Models\User::where('phone', $loginField)->first();
    }

    if ($user && \Illuminate\Support\Facades\Hash::check($validated['password'], $user->password)) {
        auth()->login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        if ($user->isAdmin()) {
            return redirect()->intended('/admin/dashboard');
        }
        return redirect()->intended('/');
    }

    return back()->withErrors([
        'login' => 'Invalid credentials. Please check your phone/email and password.',
    ])->onlyInput('login');
})->name('login.post');

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    auth()->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Profile routes (auth required)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
    Route::post('/profile', [AccountController::class, 'updateProfile'])->name('profile.update');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', AdminCategoryController::class)->except(['create', 'edit', 'show']);
    Route::resource('products', AdminProductController::class)->except(['create', 'edit', 'show']);
    Route::resource('branches', AdminBranchController::class)->except(['create', 'edit', 'show']);
    Route::resource('delivery-areas', AdminDeliveryAreaController::class)->except(['create', 'edit', 'show']);
    Route::resource('delivery-men', AdminDeliveryManController::class)->except(['create', 'edit', 'show']);
    Route::resource('coupons', AdminCouponController::class)->except(['create', 'edit', 'show']);

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::patch('/orders/{order}/delivery-man', [AdminOrderController::class, 'assignDeliveryMan'])->name('orders.delivery-man');
    Route::patch('/orders/{order}/payment', [AdminOrderController::class, 'updatePaymentStatus'])->name('orders.payment');
    Route::patch('/orders/{order}/verify-payment', [AdminOrderController::class, 'verifyPayment'])->name('orders.verify-payment');

    Route::get('/custom-cakes', [AdminCustomCakeController::class, 'index'])->name('custom-cakes.index');
    Route::get('/custom-cakes/{customCakeOrder}', [AdminCustomCakeController::class, 'show'])->name('custom-cakes.show');
    Route::patch('/custom-cakes/{customCakeOrder}/status', [AdminCustomCakeController::class, 'updateStatus'])->name('custom-cakes.status');
    Route::patch('/custom-cakes/{customCakeOrder}/delivery-man', [AdminCustomCakeController::class, 'assignDeliveryMan'])->name('custom-cakes.delivery-man');
    Route::patch('/custom-cakes/{customCakeOrder}/payment', [AdminCustomCakeController::class, 'updatePaymentStatus'])->name('custom-cakes.payment');
    Route::patch('/custom-cakes/{customCakeOrder}/verify-payment', [AdminCustomCakeController::class, 'verifyPayment'])->name('custom-cakes.verify-payment');

    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
});
