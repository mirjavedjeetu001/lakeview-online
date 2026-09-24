<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The original schema used enum columns with COD as the only option.
        // Keep the existing data, but allow configurable bKash/Rocket payments
        // and a pending/partial verification state for online orders.
        foreach (['orders', 'custom_cake_orders'] as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            if (Schema::hasColumn($table, 'payment_method')) {
                DB::statement("ALTER TABLE `{$table}` MODIFY `payment_method` VARCHAR(40) NOT NULL DEFAULT 'cash_on_delivery'");
            }

            if (Schema::hasColumn($table, 'payment_status')) {
                DB::statement("ALTER TABLE `{$table}` MODIFY `payment_status` VARCHAR(40) NOT NULL DEFAULT 'unpaid'");
            }
        }

        if (Schema::hasTable('settings')) {
            $now = now();
            $defaults = [
                ['key' => 'hero_desktop_image', 'value' => '', 'group' => 'homepage'],
                ['key' => 'hero_mobile_image', 'value' => '', 'group' => 'homepage'],
                ['key' => 'bkash_enabled', 'value' => '0', 'group' => 'payment'],
                ['key' => 'bkash_number', 'value' => '', 'group' => 'payment'],
                ['key' => 'bkash_app_key', 'value' => '', 'group' => 'payment'],
                ['key' => 'bkash_app_secret', 'value' => '', 'group' => 'payment'],
                ['key' => 'bkash_username', 'value' => '', 'group' => 'payment'],
                ['key' => 'bkash_password', 'value' => '', 'group' => 'payment'],
                ['key' => 'rocket_enabled', 'value' => '0', 'group' => 'payment'],
                ['key' => 'rocket_number', 'value' => '', 'group' => 'payment'],
                ['key' => 'rocket_merchant_id', 'value' => '', 'group' => 'payment'],
                ['key' => 'rocket_password', 'value' => '', 'group' => 'payment'],
                ['key' => 'online_payment_note', 'value' => 'Send the exact payable amount and keep your transaction ID ready.', 'group' => 'payment'],
            ];

            foreach ($defaults as $default) {
                DB::table('settings')->insertOrIgnore($default + ['created_at' => $now, 'updated_at' => $now]);
            }
        }
    }

    public function down(): void
    {
        // Do not narrow the columns again: online orders may already exist.
    }
};
