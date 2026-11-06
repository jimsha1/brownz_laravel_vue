<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DenominationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Denominations
        $this->createDenomination(1, 'One Rupee', '1');
        $this->createDenomination(2, 'Two Rupees', '2');
        $this->createDenomination(3, 'Five Rupees', '5');
        $this->createDenomination(4, 'Ten Rupees', '10');
        $this->createDenomination(5, 'Fifty Rupees', '50');
        $this->createDenomination(6, 'Hundred Rupees', '100');
        $this->createDenomination(7, 'Two Hundred Rupees', '200');
        $this->createDenomination(8, 'Five Hundred Rupees', '500');

        // is_denomination_table
        $this->createIsDenomination(1, 1);
    }

    private function createDenomination($denomination_id, $title, $denomination)
    {
        $denomination = DB::table('denomination')
                        ->updateOrInsert([
                            'denomination_id' => $denomination_id
                        ],[
                            'title' => $title,
                            'denomination' => $denomination,
                            'added_date' => date("Y/m/d H:i:s"),
                            'updated_date' => date("Y/m/d H:i:s")
                        ]);

    }

    private function createIsDenomination($id, $is_need)
    {
        $is_denomination = DB::table('is_denomination_table')
                        ->updateOrInsert([
                            'id' => $id
                        ],[
                            'is_need' => $is_need,
                            'added_date' => date("Y/m/d H:i:s"),
                            'updated_date' => date("Y/m/d H:i:s")
                        ]);

    }
}
