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

        $ruralName = 'Satkhira Sadar (Rural)';
        $normalizedName = 'satkhira sadar rural';
        $alreadyAvailable = DB::table('delivery_areas')
            ->whereRaw("LOWER(REPLACE(REPLACE(name, '(', ''), ')', '')) = ?", [$normalizedName])
            ->where(function ($query) use ($nalta) {
                $query->whereNull('branch_id')->orWhere('branch_id', $nalta->id);
            })
            ->exists();

        if ($alreadyAvailable) {
            return;
        }

        $fallbackCharge = DB::table('delivery_areas')
            ->where('zone_type', 'outside_sadar')
            ->where('is_active', true)
            ->orderByDesc('delivery_charge')
            ->value('delivery_charge');

        DB::table('delivery_areas')->insert([
            'branch_id' => $nalta->id,
            'name' => $ruralName,
            'zone_type' => 'outside_sadar',
            'delivery_charge' => $fallbackCharge ?? 200,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Delivery-area edits are admin-managed data; do not remove them on rollback.
    }
};
