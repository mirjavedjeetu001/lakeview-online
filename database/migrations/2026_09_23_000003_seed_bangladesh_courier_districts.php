<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('delivery_areas') || !Schema::hasColumn('delivery_areas', 'service_scope')) {
            return;
        }

        $districts = [
            'Bagerhat', 'Bandarban', 'Barguna', 'Barishal', 'Bhola', 'Bogura', 'Brahmanbaria',
            'Chandpur', 'Chattogram', 'Chuadanga', 'Coxs Bazar', 'Cumilla', 'Dhaka', 'Dinajpur',
            'Faridpur', 'Feni', 'Gaibandha', 'Gazipur', 'Gopalganj', 'Habiganj', 'Jamalpur',
            'Jashore', 'Jhalokathi', 'Jhenaidah', 'Joypurhat', 'Khagrachhari', 'Khulna',
            'Kishoreganj', 'Kurigram', 'Kushtia', 'Lakshmipur', 'Lalmonirhat', 'Madaripur',
            'Magura', 'Manikganj', 'Meherpur', 'Moulvibazar', 'Munshiganj', 'Mymensingh',
            'Naogaon', 'Narail', 'Narayanganj', 'Narsingdi', 'Natore', 'Netrokona', 'Nilphamari',
            'Noakhali', 'Pabna', 'Panchagarh', 'Patuakhali', 'Pirojpur', 'Rajbari', 'Rajshahi',
            'Rangamati', 'Rangpur', 'Satkhira', 'Shariatpur', 'Sherpur', 'Sirajganj', 'Sunamganj',
            'Sylhet', 'Tangail', 'Thakurgaon',
        ];

        foreach ($districts as $district) {
            $exists = DB::table('delivery_areas')
                ->whereNull('branch_id')
                ->where('service_scope', 'national')
                ->where('district', $district)
                ->exists();

            if (!$exists) {
                DB::table('delivery_areas')->insert([
                    'branch_id' => null,
                    'name' => $district,
                    'district' => $district,
                    'upazila' => null,
                    'zone_type' => 'outside_sadar',
                    'service_scope' => 'national',
                    'delivery_charge' => 150,
                    'courier_name' => 'Pathao Courier',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('delivery_areas') && Schema::hasColumn('delivery_areas', 'service_scope')) {
            DB::table('delivery_areas')->whereNull('branch_id')->where('service_scope', 'national')->delete();
        }
    }
};
