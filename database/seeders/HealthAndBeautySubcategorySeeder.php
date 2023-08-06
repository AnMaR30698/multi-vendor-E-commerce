<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HealthAndBeautySubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $healthAndBeauty = Category::where('name', 'Health And Beauty')->first();

        SubCategory::factory()
            ->count(5)
            ->create([
                'category_id' => $healthAndBeauty->id,
            ]);
    }
}
