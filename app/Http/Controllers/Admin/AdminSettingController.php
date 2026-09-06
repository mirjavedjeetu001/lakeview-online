<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminSettingController extends Controller
{
    public function index()
    {
        // Advance payment is disabled for now. Keep its database values for a future
        // re-enable, but do not expose the controls in the active admin UI.
        $settings = Setting::whereNotIn('key', ['merchant_number', 'merchant_name', 'payment_instructions'])
            ->get()
            ->groupBy('group')
            ->map(fn ($items) => $items->values());
        return Inertia::render('Admin/Settings/Index', ['settings' => $settings]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable|string',
            'settings.*.group' => 'required|string',
        ]);

        foreach ($validated['settings'] as $setting) {
            Setting::set($setting['key'], $setting['value'], $setting['group']);
        }

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
