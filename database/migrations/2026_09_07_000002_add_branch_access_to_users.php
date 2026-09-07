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
            $table->foreignId('branch_id')->nullable()->after('role')->constrained()->nullOnDelete();
            $table->json('permissions')->nullable()->after('branch_id');
        });

        $allPermissions = json_encode([
            'dashboard', 'products', 'categories', 'orders', 'reports', 'stock',
            'custom_cakes', 'branches', 'delivery_areas', 'delivery_men',
            'coupons', 'users', 'settings',
        ]);

        DB::table('users')
            ->where('role', 'admin')
            ->whereNull('permissions')
            ->update(['permissions' => $allPermissions]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn(['branch_id', 'permissions']);
        });
    }
};
