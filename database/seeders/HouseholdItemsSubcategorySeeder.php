<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HouseholdItemsSubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $houseHoldItems = Category::where('name', 'HouseHold items')->first();

        SubCategory::factory()
            ->count(5)
            ->create([
                'category_id' => $houseHoldItems->id,
            ]);
    }
}
