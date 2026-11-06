<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call(PermissionMasterSeeder::class);
        //$this->call(UserTableSeeder::class);
        $this->call(AutoIncrementTableSeeder::class);
        $this->call(BusinessConfigTableSeeder::class);
        //$this->call(OutletsTableSeeder::class);
        //$this->call(ServiceTypeManagementSeeder::class);
        //$this->call(DenominationSeeder::class);
        //$this->call(MasterDataSeeder::class);

        // inventory
        $this->call(AccountSeeder::class);
        $this->call(UnitOfMeasurementSeeder::class);
    }
}
