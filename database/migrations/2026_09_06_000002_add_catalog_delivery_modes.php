<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('delivery_mode', 20)->default('both')->after('image');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('delivery_mode', 20)->default('inherit')->after('gallery');
        });

        DB::table('categories')
            ->whereIn('name', ['Cake', 'Order Cake'])
            ->update(['delivery_mode' => 'pickup']);

        DB::table('branches')
            ->where('name', 'Lake View Cafe & Restaurant (Main)')
            ->whereNull('address')
            ->update(['address' => 'Lake View Sweets & Bakery outlet']);
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('delivery_mode');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('delivery_mode');
        });
    }
};
