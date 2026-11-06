<?php

namespace Database\Seeders;

use App\Models\GlobalTax;
use App\Models\Shift;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Global Tax
        $this->createGlobalTax(1, 'GST 18%', 18);

        // Shifts
        // $this->createShifts(1, 'SHIFT:24 Hour', '00:00:00', '23:59:59');
        // $this->createShifts(2, 'SHIFT: 07AM-04PM', '07:00:00', '16:00:00');
        // $this->createShifts(3, 'SHIFT: 07AM-07PM', '07:00:00', '19:00:00');
        // $this->createShifts(4, 'SHIFT: 09AM-06PM', '09:00:00', '18:00:00');
        $this->createShifts(5, 'SHIFT: 10AM-09PM', '10:00:00', '21:00:00');
    }

    // Global Tax
    private function createGlobalTax($tax_id, $name, $tax)
    {
        $globalTax = GlobalTax::updateOrCreate([
            'tax_id' => $tax_id
        ], [
            'name' => $name,
            'tax' => $tax,
            'created_by' => 1
        ]);
    }

    // Shifts
    private function createShifts($shift_id, $title, $start_time, $end_time)
    {
        $shift = Shift::updateOrCreate([
            'shift_id' => $shift_id
        ], [
            'title' => $title,
            'start_time' => $start_time,
            'end_time' => $end_time,
        ]);
    }
}
