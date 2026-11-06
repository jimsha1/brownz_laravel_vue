<?php

namespace Database\Seeders;

use App\Models\BusinessConfig;
use Illuminate\Database\Seeder;

class BusinessConfigTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedRow('business_title_english', 1, 'required');

        $this->seedRow('phone', 2, 'required');
        $this->seedRow('email', 3, 'required');

        $this->seedRow('address_english', 4, 'required', 'textarea');

        $this->seedRow('location_english', 5);

        $this->seedRow('city_english', 6, 'required');

        $this->seedRow('opening_hours', 7, 'required');
        $this->seedRow('telephone_ordering_time', 8, 'required');
        $this->seedRow('upi_id', 9, 'required');
        $this->seedRow('upi_name', 10, 'required');
        $this->seedRow('paid_leave_limit', 11, 'required');
        $this->seedRow('salary_days_in_a_month', 12, 'required');
        $this->seedRow('gst_number', 13, 'required');
        $this->seedRow('is_send_whatsapp_invoice', 14, 'required');

    }

    private function seedRow($key, $display_order, $validation = '', $input_type = 'text')
    {
        $config = BusinessConfig::firstOrNew(['config_key' => $key]);

        // MySQL requires config_value on insert; never overwrite existing values on re-seed
        if (! $config->exists) {
            $config->config_value = '';
        }

        $config->validation = $validation;
        $config->input_type = $input_type;
        $config->display_order = $display_order;
        $config->updated_by = 1;
        $config->save();
    }
}
