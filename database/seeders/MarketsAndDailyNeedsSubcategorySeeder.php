<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MarketsAndDailyNeedsSubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $marketingAndDailyNeeds = Category::where('name', 'Markets & Daily Needs')->first();

        SubCategory::factory()
            ->count(5)
            ->create([
                'category_id' => $marketingAndDailyNeeds->id,
            ]);
    }
}
