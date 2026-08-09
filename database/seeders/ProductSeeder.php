<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

final class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $childCategories = Category::whereNotNull('parent_id')->get();

        foreach ($childCategories as $category) {
            Product::factory(5)->create([
                'category_id' => $category->id,
            ]);
        }
    }
}
