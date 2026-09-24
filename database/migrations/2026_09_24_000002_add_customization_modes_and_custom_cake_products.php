<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'customization_mode')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('customization_mode', 32)->default('ready_only')->after('national_delivery');
            });
        }

        if (Schema::hasTable('custom_cake_orders') && !Schema::hasColumn('custom_cake_orders', 'product_id')) {
            Schema::table('custom_cake_orders', function (Blueprint $table) {
                $table->foreignId('product_id')->nullable()->after('branch_id')->constrained('products')->nullOnDelete();
            });
        }

        if (Schema::hasTable('products') && Schema::hasTable('categories')) {
            $cakeCategoryIds = DB::table('categories')
                ->whereIn(DB::raw('LOWER(name)'), ['cake', 'order cake'])
                ->pluck('id');

            if ($cakeCategoryIds->isNotEmpty()) {
                DB::table('products')
                    ->whereIn('category_id', $cakeCategoryIds)
                    ->where('customization_mode', 'ready_only')
                    ->update(['customization_mode' => 'ready_and_customization']);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('custom_cake_orders') && Schema::hasColumn('custom_cake_orders', 'product_id')) {
            Schema::table('custom_cake_orders', function (Blueprint $table) {
                $table->dropForeign(['product_id']);
                $table->dropColumn('product_id');
            });
        }

        if (Schema::hasTable('products') && Schema::hasColumn('products', 'customization_mode')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('customization_mode');
            });
        }
    }
};
