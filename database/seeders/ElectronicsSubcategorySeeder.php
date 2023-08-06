<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ElectronicsSubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $electronicsCategory = Category::where('name', 'Electronics')->first();

        SubCategory::factory()
            ->count(5)
            ->create([
                'category_id' => $electronicsCategory->id,
            ]);
    }
}
