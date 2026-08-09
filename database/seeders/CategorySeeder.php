<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Electronics' => ['Smartphones', 'Laptops', 'Audio & Headphones', 'Wearables'],
            'Fashion' => ["Men's Clothing", "Women's Clothing", 'Shoes', 'Accessories'],
            'Home & Living' => ['Furniture', 'Kitchenware', 'Decor', 'Lighting'],
        ];

        $sortOrder = 1;
        foreach ($categories as $parentName => $subCategories) {
            $parent = Category::firstOrCreate(
                ['slug' => Str::slug($parentName)],
                [
                    'name' => $parentName,
                    'description' => "All {$parentName} products",
                    'sort_order' => $sortOrder++,
                    'is_active' => true,
                ]
            );

            foreach ($subCategories as $subName) {
                Category::firstOrCreate(
                    ['slug' => Str::slug($subName)],
                    [
                        'parent_id' => $parent->id,
                        'name' => $subName,
                        'description' => "Shop for {$subName}",
                        'sort_order' => $sortOrder++,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
