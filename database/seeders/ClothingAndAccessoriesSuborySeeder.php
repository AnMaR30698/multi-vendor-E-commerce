<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClothingAndAccessoriesSuborySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clothingAndAccessories = Category::where('name', 'Clothing And Accessories')->first();

        SubCategory::factory()
            ->count(5)
            ->create([
                'category_id' => $clothingAndAccessories->id,
            ]);
    }
}
