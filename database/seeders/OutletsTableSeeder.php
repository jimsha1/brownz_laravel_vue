<?php

namespace Database\Seeders;

use App\Models\Outlet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OutletsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $this->createPrimaryOutlet();
        });
    }

    private function createPrimaryOutlet()
    {
        $outlet = Outlet::updateOrCreate([
            'outlet_id' => 1
        ],[
            'name' => 'Head Office',
            'auto_prefix' => 'HO',

            // Contact
            'phone' => '',
            'email' => '',

            // Location
            'location' => '',
            'address' => '',
            'city' => '',

            // Postal address
            'building_name' => '',
            'post_office' => '',
            'postal_code' => '',
            'district' => '',
            'state' => '',
            'country' => '',

            // Settings
            'display_order' => 0,
            'is_active' => 1,

            // Activity
            'created_by' => 1,
            'updated_by' => 1,
        ]);

    }    
}
