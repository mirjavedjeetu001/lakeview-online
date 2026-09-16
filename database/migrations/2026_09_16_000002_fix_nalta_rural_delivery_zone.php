<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('branches') || !Schema::hasTable('delivery_areas')) {
            return;
        }

        $nalta = DB::table('branches')
            ->where('name', 'like', 'Nalta%')
            ->first(['id']);

        if (!$nalta) {
            return;
        }

        $normalizedName = 'satkhira sadar rural';
        $naltaArea = DB::table('delivery_areas')
            ->where('branch_id', $nalta->id)
            ->whereRaw("LOWER(REPLACE(REPLACE(name, '(', ''), ')', '')) = ?", [$normalizedName])
            ->first(['id']);

        if ($naltaArea) {
            DB::table('delivery_areas')->where('id', $naltaArea->id)->update([
                'zone_type' => 'sadar',
                'is_active' => true,
                'updated_at' => now(),
            ]);
            return;
        }

        $globalRural = DB::table('delivery_areas')
            ->whereNull('branch_id')
            ->whereRaw("LOWER(REPLACE(REPLACE(name, '(', ''), ')', '')) = ?", [$normalizedName])
            ->first(['name', 'delivery_charge', 'is_active']);

        DB::table('delivery_areas')->insert([
            'branch_id' => $nalta->id,
            'name' => $globalRural->name ?? 'Satkhira Sadar (Rural)',
            'zone_type' => 'sadar',
            'delivery_charge' => $globalRural->delivery_charge ?? 200,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Keep admin-managed delivery-area records intact on rollback.
    }
};
