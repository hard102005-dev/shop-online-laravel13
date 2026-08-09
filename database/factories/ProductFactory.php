<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
final class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);
        $price = fake()->randomFloat(2, 100, 5000);

        return [
            'category_id' => Category::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'sku' => 'SKU-' . strtoupper(Str::random(8)),
            'short_description' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'price' => $price,
            'sale_price' => fake()->boolean(40) ? round($price * 0.85, 2) : null,
            'stock' => fake()->numberBetween(0, 100),
            'low_stock_threshold' => 5,
            'is_featured' => fake()->boolean(20),
            'is_active' => true,
        ];
    }
}
