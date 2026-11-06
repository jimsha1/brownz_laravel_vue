<?php

namespace Database\Seeders;

use App\Models\UnitOfMeasurement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitOfMeasurementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createUnitOfMeasurement(1, 'PCS', 'Pieces');
        $this->createUnitOfMeasurement(2, 'PACK', 'Pack');
        $this->createUnitOfMeasurement(3, 'ML', 'Millilitre');
        $this->createUnitOfMeasurement(4, 'GM', 'Gram');
        $this->createUnitOfMeasurement(5, 'LTR', 'Litre');
        $this->createUnitOfMeasurement(6, 'KG', 'Kilogram');
        $this->createUnitOfMeasurement(7, 'BOX', 'Box');
        $this->createUnitOfMeasurement(8, 'SET', 'Set');
    }

    private function createUnitOfMeasurement($uom_id, $uom_name, $description)
    {
        $globalTax = UnitOfMeasurement::updateOrCreate([
            'uom_id' => $uom_id
        ], [
            'outlet_id' => 1,
            'uom_name' => $uom_name,
            'description' => $description,
            'created_by' => 1
        ]);
    }
}
