<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->boolean('is_active')->default(true)->after('role');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('customer_email')->nullable()->after('customer_phone');
        });

        Schema::table('custom_cake_orders', function (Blueprint $table) {
            $table->string('customer_email')->nullable()->after('customer_phone');
        });

        // Remove the legacy placeholder addresses that were generated for customers.
        DB::table('users')
            ->where('email', 'like', 'customer_%@lakeview.local')
            ->update(['email' => null]);

        DB::table('branches')
            ->where('name', 'Lake View Cafe & Restaurant (Main)')
            ->update(['address' => 'Lake View Sweets & Bakery outlet']);

        foreach ([
            ['mail_mailer', 'smtp'],
            ['mail_host', 'lakeview-cafe.com'],
            ['mail_port', '465'],
            ['mail_scheme', 'smtps'],
            ['mail_username', 'info@lakeview-cafe.com'],
            ['mail_password', ''],
            ['mail_from_address', 'info@lakeview-cafe.com'],
            ['mail_from_name', 'Lake View Sweets & Bakery'],
            ['mail_reply_to', ''],
            ['mail_order_recipients', json_encode(['metasoftinfo@gmail.com'])],
            ['mail_order_notifications', '1'],
            ['mail_customer_notifications', '1'],
        ] as [$key, $value]) {
            DB::table('settings')->insertOrIgnore([
                'key' => $key,
                'value' => $value,
                'group' => 'mailer',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('custom_cake_orders', function (Blueprint $table) {
            $table->dropColumn('customer_email');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('customer_email');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
            $table->string('email')->nullable(false)->change();
        });
    }
};
