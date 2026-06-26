<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class AccountController extends Controller
{
    public function profile()
    {
        $user = auth()->user();
        $orders = \App\Models\Order::where('user_id', $user->id)
            ->with(['branch', 'items'])
            ->latest()
            ->limit(10)
            ->get();

        return Inertia::render('Profile', [
            'user' => $user,
            'orders' => $orders,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
        ]);

        $user = auth()->user();
        $user->update($validated);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
