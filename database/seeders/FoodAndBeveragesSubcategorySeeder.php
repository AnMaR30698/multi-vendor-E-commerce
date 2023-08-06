<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FoodAndBeveragesSubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $foodAndBeverages = Category::where('name', 'Food And Beverages')->first();

        SubCategory::factory()
            ->count(5)
            ->create([
                'category_id' => $foodAndBeverages->id,
            ]);
    }
}
