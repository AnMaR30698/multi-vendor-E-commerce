<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
                    'Electronics',
                    'Food And Beverages',
                    'Markets & Daily Needs',
                    'Clothing And Accessories',
                    'Health And Beauty',
                    'HouseHold items'
        ];

        foreach ($categories as $categoryName) {
            Category::create(['name' => $categoryName,
                              'image'=>'']);
        }
    }
}
