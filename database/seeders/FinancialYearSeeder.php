<?php

namespace Database\Seeders;

use App\Service\FinancialYearService;
use Illuminate\Database\Seeder;

class FinancialYearSeeder extends Seeder
{
    public function run(): void
    {
        app(FinancialYearService::class)->initializeCurrentFinancialYear();
    }
}
