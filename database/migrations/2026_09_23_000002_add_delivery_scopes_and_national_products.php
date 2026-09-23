<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('delivery_areas') && !Schema::hasColumn('delivery_areas', 'service_scope')) {
            Schema::table('delivery_areas', function (Blueprint $table) {
                $table->string('service_scope', 30)->default('sadar')->after('zone_type');
                $table->string('district')->nullable()->after('name');
                $table->string('upazila')->nullable()->after('district');
                $table->string('courier_name')->nullable()->after('delivery_charge');
            });

            DB::table('delivery_areas')->where('zone_type', 'outside_sadar')->update(['service_scope' => 'outside_sadar']);
            DB::table('delivery_areas')
                ->where('zone_type', 'sadar')
                ->where(function ($query) {
                    $query->where('name', 'like', '%rural%')->orWhere('name', 'like', '%গ্রাম%');
                })
                ->update(['service_scope' => 'sadar_rural']);
        }

        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'national_delivery')) {
            Schema::table('products', function (Blueprint $table) {
                $table->boolean('national_delivery')->default(false)->after('delivery_mode');
            });

            DB::table('products')
                ->whereIn('category_id', function ($query) {
                    $query->select('id')->from('categories')->whereIn('name', ['Cake', 'Order Cake']);
                })
                ->update(['national_delivery' => true]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'national_delivery')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('national_delivery');
            });
        }

        if (Schema::hasTable('delivery_areas') && Schema::hasColumn('delivery_areas', 'service_scope')) {
            Schema::table('delivery_areas', function (Blueprint $table) {
                $table->dropColumn(['service_scope', 'district', 'upazila', 'courier_name']);
            });
        }
    }
};
