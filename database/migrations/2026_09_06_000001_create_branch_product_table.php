<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->boolean('is_available')->default(true);
            $table->unsignedInteger('stock')->nullable();
            $table->timestamps();

            $table->unique(['branch_id', 'product_id']);
            $table->index(['branch_id', 'is_available']);
        });

        // Existing production products should remain visible at every active outlet
        // after the new branch-aware catalog is introduced.
        $now = now();
        $branchIds = DB::table('branches')->where('is_active', true)->pluck('id');
        $products = DB::table('products')->select('id', 'is_available')->orderBy('id')->get();

        foreach ($branchIds as $branchId) {
            $rows = $products->map(fn ($product) => [
                'branch_id' => $branchId,
                'product_id' => $product->id,
                'is_available' => (bool) $product->is_available,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all();

            if ($rows) {
                DB::table('branch_product')->insertOrIgnore($rows);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_product');
    }
};
