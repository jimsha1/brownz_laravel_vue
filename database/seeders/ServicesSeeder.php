<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Service Category
        $this->createServiceCategory(1, NULL, 'men');
        $this->createServiceCategory(2, 1, 'Bridal Hair & Makeup');
        $this->createServiceCategory(3, NULL, 'women');
        $this->createServiceCategory(4, 3, 'EYELASH SYSTEM');
        $this->createServiceCategory(5, NULL, 'Face');
        $this->createServiceCategory(6, 5, 'Facial System');

        // Services

    }

    // Service Category
    private function createServiceCategory($category_id, $parent_id, $name)
    {
        $serviceCategory= ServiceCategory::updateOrCreate([
            'category_id' => $category_id
        ], [
            'parent_id' => $parent_id,
            'name' => $name,
            'show_on_website' => 1,
            'created_by' => 1,
            'updated_by' => 1
        ]);

    }
}
