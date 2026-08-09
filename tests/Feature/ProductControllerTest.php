<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_view_product_catalog(): void
    {
        Product::factory(5)->create(['is_active' => true]);

        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertViewHas('products');
    }

    public function test_customer_can_filter_products_by_category(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'is_active' => true]);

        $response = $this->get(route('products.index', ['category' => $category->id]));

        $response->assertStatus(200);
        $response->assertSee($product->name);
    }

    public function test_customer_can_view_product_detail_page(): void
    {
        $product = Product::factory()->create(['is_active' => true]);

        $response = $this->get(route('products.show', $product->slug));

        $response->assertStatus(200);
        $response->assertSee($product->name);
    }
}
