<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('branches') && !Schema::hasColumn('branches', 'business_type')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->string('business_type', 32)->default('both')->after('image');
            });
        }

        if (Schema::hasTable('categories') && !Schema::hasColumn('categories', 'business_type')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->string('business_type', 32)->default('both')->after('image');
            });
        }

        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'cake_sizes')) {
            Schema::table('products', function (Blueprint $table) {
                $table->json('cake_sizes')->nullable()->after('customization_mode');
            });
        }

        if (Schema::hasTable('coupons')) {
            Schema::table('coupons', function (Blueprint $table) {
                if (!Schema::hasColumn('coupons', 'category_id')) {
                    $table->foreignId('category_id')->nullable()->after('code')->constrained('categories')->nullOnDelete();
                }
                if (!Schema::hasColumn('coupons', 'branch_id')) {
                    $table->foreignId('branch_id')->nullable()->after('category_id')->constrained('branches')->nullOnDelete();
                }
                if (!Schema::hasColumn('coupons', 'business_type')) {
                    $table->string('business_type', 32)->nullable()->after('branch_id');
                }
                if (!Schema::hasColumn('coupons', 'description')) {
                    $table->string('description')->nullable()->after('business_type');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('coupons')) {
            Schema::table('coupons', function (Blueprint $table) {
                foreach (['category_id', 'branch_id'] as $column) {
                    if (Schema::hasColumn('coupons', $column)) {
                        $table->dropForeign([$column]);
                        $table->dropColumn($column);
                    }
                }
                foreach (['business_type', 'description'] as $column) {
                    if (Schema::hasColumn('coupons', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('products') && Schema::hasColumn('products', 'cake_sizes')) {
            Schema::table('products', fn (Blueprint $table) => $table->dropColumn('cake_sizes'));
        }
        if (Schema::hasTable('categories') && Schema::hasColumn('categories', 'business_type')) {
            Schema::table('categories', fn (Blueprint $table) => $table->dropColumn('business_type'));
        }
        if (Schema::hasTable('branches') && Schema::hasColumn('branches', 'business_type')) {
            Schema::table('branches', fn (Blueprint $table) => $table->dropColumn('business_type'));
        }
    }
};
