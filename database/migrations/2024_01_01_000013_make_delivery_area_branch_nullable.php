<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_areas', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->change();
        });

        // Make existing delivery areas global (branch_id = null)
        \App\Models\DeliveryArea::query()->update(['branch_id' => null]);
    }

    public function down(): void
    {
        Schema::table('delivery_areas', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable(false)->change();
        });
    }
};
