<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_cake_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('branch_id')->constrained()->restrictOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('customer_address')->nullable();
            $table->enum('delivery_type', ['pickup', 'home_delivery'])->default('pickup');
            $table->foreignId('delivery_area_id')->nullable()->constrained()->nullOnDelete();
            $table->string('cake_type')->nullable();
            $table->string('cake_size')->nullable();
            $table->string('cake_flavor')->nullable();
            $table->text('message_on_cake')->nullable();
            $table->date('delivery_date');
            $table->time('delivery_time')->nullable();
            $table->string('design_image')->nullable();
            $table->decimal('estimated_price', 10, 2)->default(0);
            $table->decimal('delivery_charge', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('payment_method', ['cash_on_delivery'])->default('cash_on_delivery');
            $table->enum('status', ['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_cake_orders');
    }
};
