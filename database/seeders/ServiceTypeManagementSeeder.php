<?php

namespace Database\Seeders;

use App\Models\ServiceTypeManagement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceTypeManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createServiceType(1, 'Salon');
    }

    private function createServiceType($id, $name)
    {
        $serviceType = ServiceTypeManagement::updateOrCreate([
            'service_type_management_id' => $id
        ], [
            'service_type_name' => $name
        ]);

    }
}
