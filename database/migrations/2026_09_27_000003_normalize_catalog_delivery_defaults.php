<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('categories') || !Schema::hasColumn('categories', 'delivery_mode')) {
            return;
        }

        // Cakes are available for pickup and delivery by default. Other
        // existing categories use home delivery unless an admin overrides it.
        DB::table('categories')
            ->whereRaw('LOWER(name) LIKE ?', ['%cake%'])
            ->update(['delivery_mode' => 'both']);

        DB::table('categories')
            ->whereRaw('LOWER(name) NOT LIKE ?', ['%cake%'])
            ->where('delivery_mode', 'both')
            ->update(['delivery_mode' => 'home_delivery']);
    }

    public function down(): void
    {
        // Delivery mode values are admin-configurable, so no safe rollback
        // exists for the normalized data.
    }
};
