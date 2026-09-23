<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('branches')) {
            return;
        }

        if (!Schema::hasColumn('branches', 'name_bn')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->string('name_bn')->nullable()->after('name');
            });
        }

        $translations = [
            'Nalta (Opposite Ahsania Petrol Pump)' => 'নলতা (আহসানিয়া পেট্রোল পাম্পের বিপরীতে)',
            'AB Bank, Adjacent to Satkhira' => 'এবি ব্যাংক, সাতক্ষীরার পাশে',
            'Adjacent to Sadar Hospital' => 'সদর হাসপাতালের পাশে',
            'Adjacent to Land Office' => 'ভূমি অফিসের পাশে',
            'Adjacent to Govt Girls School' => 'সরকারি বালিকা বিদ্যালয়ের পাশে',
            'Old Satkhira Hatkhola Mor' => 'পুরাতন সাতক্ষীরা হাটখোলা মোড়',
            'Lake View Cafe & Restaurant (Main)' => 'লেক ভিউ ক্যাফে অ্যান্ড রেস্টুরেন্ট (প্রধান শাখা)',
        ];

        foreach ($translations as $name => $nameBn) {
            DB::table('branches')->where('name', $name)->whereNull('name_bn')->update(['name_bn' => $nameBn]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('branches') && Schema::hasColumn('branches', 'name_bn')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->dropColumn('name_bn');
            });
        }
    }
};
