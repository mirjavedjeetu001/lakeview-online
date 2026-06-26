<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('delivery_man_id')->nullable()->after('branch_id')->constrained('delivery_men')->nullOnDelete();
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid')->after('status');
        });

        Schema::table('custom_cake_orders', function (Blueprint $table) {
            $table->foreignId('delivery_man_id')->nullable()->after('branch_id')->constrained('delivery_men')->nullOnDelete();
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['delivery_man_id']);
            $table->dropColumn(['delivery_man_id', 'payment_status']);
        });

        Schema::table('custom_cake_orders', function (Blueprint $table) {
            $table->dropForeign(['delivery_man_id']);
            $table->dropColumn(['delivery_man_id', 'payment_status']);
        });
    }
};
