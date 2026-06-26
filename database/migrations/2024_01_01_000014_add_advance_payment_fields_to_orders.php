<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('advance_amount', 10, 2)->default(0)->after('total');
            $table->string('transaction_id')->nullable()->after('advance_amount');
            $table->boolean('payment_verified')->default(false)->after('transaction_id');
        });

        Schema::table('custom_cake_orders', function (Blueprint $table) {
            $table->decimal('advance_amount', 10, 2)->default(0)->after('total');
            $table->string('transaction_id')->nullable()->after('advance_amount');
            $table->boolean('payment_verified')->default(false)->after('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['advance_amount', 'transaction_id', 'payment_verified']);
        });

        Schema::table('custom_cake_orders', function (Blueprint $table) {
            $table->dropColumn(['advance_amount', 'transaction_id', 'payment_verified']);
        });
    }
};
