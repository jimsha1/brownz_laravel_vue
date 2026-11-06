<?php

namespace Database\Seeders;

use App\Models\AutoIncrementMaster;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AutoIncrementTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createAutoIncrementMaster(1, 'AI_OUTLET_CODE', 'Outlet Code', 'OC', 1);
        $this->createAutoIncrementMaster(2, 'AI_ITEM_CODE', 'Item Code', 'PD', 1);
        $this->createAutoIncrementMaster(3, 'AI_SALON_BOOKING_CODE', 'Salon Booking Code', 'BS', 1);
        $this->createAutoIncrementMaster(4, 'AI_BEAUTICIAN_CODE', 'Beautician Code', 'BT', 1);
        $this->createAutoIncrementMaster(5, 'AI_SALES_ORDER_NUMBER', 'Sales Order Number', 'SL', 1);
        $this->createAutoIncrementMaster(6, 'AI_EMPLOYEE_CODE', 'Employee Code', 'EMP', 1);
        $this->createAutoIncrementMaster(7, 'AI_PURCHASE_REF_NO', 'Purchase Ref.No', 'PR', 1);
        $this->createAutoIncrementMaster(8, 'AI_WEBSITE_BOOKING_CODE', 'Website Booking Code', 'BW', 1);               
    }

    private function createAutoIncrementMaster($id, $code, $name, $prefix, $master_prefix, $max_incr = 0)
    {
        $auto_increment_master = AutoIncrementMaster::updateOrCreate([
            'auto_increment_id' => $id
        ], [
            'auto_code' => $code,
            'auto_name' => $name,
            'auto_prefix' => $prefix,
            'use_master_prefix' => $master_prefix,
            //'auto_increment_general_value' => 1,
            'max_increment_value' => $max_incr
        ]);

    }
}
