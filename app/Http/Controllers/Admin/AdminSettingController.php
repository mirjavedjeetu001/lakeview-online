<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CustomCakeOrder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AdminSettingController extends Controller
{
    public function index()
    {
        $this->ensureMailerSettings();
        $this->ensureDeliverySettings();
        // Advance payment is disabled for now. Keep its database values for a future
        // re-enable, but do not expose the controls in the active admin UI.
        $settings = Setting::whereNotIn('key', ['merchant_number', 'merchant_name', 'payment_instructions'])
            ->get()
            ->map(function ($setting) {
                if ($setting->key === 'mail_password') {
                    $setting->value = '';
                }
                return $setting;
            })
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
            if ($setting['key'] === 'mail_password') {
                if (filled($setting['value'])) {
                    Setting::set($setting['key'], Crypt::encryptString($setting['value']), 'mailer');
                }
                continue;
            }

            if ($setting['key'] === 'mail_order_recipients') {
                $recipients = json_decode($setting['value'] ?? '[]', true);
                validator(['recipients' => $recipients], [
                    'recipients' => 'array',
                    'recipients.*' => 'email|max:255',
                ])->validate();
                $setting['value'] = json_encode(array_values(array_unique(array_filter($recipients))));
            }

            Setting::set($setting['key'], $setting['value'], $setting['group']);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Settings updated successfully.');
    }

    public function testMail(Request $request)
    {
        $validated = $request->validate(['email' => 'required|email|max:255']);
        $settings = Setting::getMailerSettings();
        $this->configureMailer($settings);

        try {
            Mail::raw(
                "This is a test email from Lake View Sweets & Bakery. Your mail settings are working.",
                function ($message) use ($validated, $settings) {
                    $message->to($validated['email'])
                        ->subject('Lake View mail setup test');
                    if ($settings['reply_to']) {
                        $message->replyTo($settings['reply_to']);
                    }
                }
            );
        } catch (\Throwable $exception) {
            report($exception);
            return redirect()->back()->withErrors(['mail_test' => 'Mail test failed. Please check the SMTP host, port, encryption and credentials.']);
        }

        return redirect()->back()->with('success', 'Test email sent successfully.');
    }

    public function cleanup(Request $request)
    {
        abort_unless($request->user()?->role === 'super_admin', 403, 'Only the super admin can clean database data.');

        $request->validate([
            'products' => 'required|boolean',
            'orders' => 'required|boolean',
            'confirmation' => 'required|in:DELETE ALL DATA',
        ]);

        $cleanProducts = $request->boolean('products');
        $cleanOrders = $request->boolean('orders');

        if (!$cleanProducts && !$cleanOrders) {
            return redirect()->back()->withErrors(['cleanup' => 'Select Products, Orders, or both before cleaning the database.']);
        }

        $productImagePaths = [];
        $cakeImagePaths = [];
        $deletedProducts = 0;
        $deletedOrders = 0;
        $deletedCakeOrders = 0;

        DB::transaction(function () use ($cleanProducts, $cleanOrders, &$productImagePaths, &$cakeImagePaths, &$deletedProducts, &$deletedOrders, &$deletedCakeOrders) {
            if ($cleanOrders) {
                $deletedOrders = Order::query()->count();
                $deletedCakeOrders = CustomCakeOrder::query()->count();
                $cakeImagePaths = CustomCakeOrder::query()
                    ->pluck('design_image')
                    ->filter(fn ($path) => is_string($path) && $path !== '' && !str_starts_with($path, 'http'))
                    ->unique()
                    ->values()
                    ->all();

                OrderItem::query()->delete();
                Order::query()->delete();
                CustomCakeOrder::query()->delete();
                Coupon::query()->update(['used_count' => 0]);
            }

            if ($cleanProducts) {
                $deletedProducts = Product::query()->count();
                $productImagePaths = Product::query()
                    ->get(['image', 'gallery'])
                    ->flatMap(fn (Product $product) => collect([$product->image])->merge($product->gallery ?: []))
                    ->filter(fn ($path) => is_string($path) && $path !== '' && !str_starts_with($path, 'http'))
                    ->unique()
                    ->values()
                    ->all();

                DB::table('branch_product')->delete();
                Product::query()->delete();
            }
        });

        Storage::disk('public')->delete(array_values(array_unique(array_merge($productImagePaths, $cakeImagePaths))));

        $summary = [];
        if ($cleanProducts) {
            $summary[] = "{$deletedProducts} products";
        }
        if ($cleanOrders) {
            $summary[] = "{$deletedOrders} orders and {$deletedCakeOrders} custom cake orders";
        }

        return redirect()->back()->with('success', 'Database cleanup complete: ' . implode(', ', $summary) . '.');
    }

    private function ensureMailerSettings(): void
    {
        $defaults = [
            'mail_mailer' => ['smtp', 'mailer'],
            'mail_host' => ['lakeview-cafe.com', 'mailer'],
            'mail_port' => ['465', 'mailer'],
            'mail_scheme' => ['smtps', 'mailer'],
            'mail_username' => ['info@lakeview-cafe.com', 'mailer'],
            'mail_password' => ['', 'mailer'],
            'mail_from_address' => ['info@lakeview-cafe.com', 'mailer'],
            'mail_from_name' => ['Lake View Sweets & Bakery', 'mailer'],
            'mail_reply_to' => ['', 'mailer'],
            'mail_order_recipients' => [json_encode(['metasoftinfo@gmail.com']), 'mailer'],
            'mail_order_notifications' => ['1', 'mailer'],
            'mail_customer_notifications' => ['1', 'mailer'],
        ];

        foreach ($defaults as $key => [$value, $group]) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
        }

        Cache::forget('settings.all');
        Cache::forget('settings.group.delivery');
    }

    private function ensureDeliverySettings(): void
    {
        $globalMinimum = (string) (Setting::where('key', 'min_order_amount')->value('value') ?? '0');

        $defaults = [
            'min_order_amount' => [$globalMinimum, 'delivery'],
            'min_order_sadar' => [$globalMinimum, 'delivery'],
            'min_order_outside' => [$globalMinimum, 'delivery'],
            'min_order_pickup' => ['0', 'delivery'],
        ];

        foreach ($defaults as $key => [$value, $group]) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
        }
    }

    private function configureMailer(array $settings): void
    {
        config([
            'mail.default' => $settings['mailer'],
            'mail.from.address' => $settings['from_address'],
            'mail.from.name' => $settings['from_name'],
            'mail.mailers.smtp.host' => $settings['host'],
            'mail.mailers.smtp.port' => $settings['port'],
            'mail.mailers.smtp.scheme' => $settings['scheme'],
            'mail.mailers.smtp.username' => $settings['username'],
            'mail.mailers.smtp.password' => $settings['password'],
            'mail.mailers.smtp.timeout' => 10,
        ]);
    }
}
