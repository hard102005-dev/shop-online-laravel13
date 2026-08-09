<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateAdmin(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    public function test_admin_can_view_product_list(): void
    {
        $this->authenticateAdmin();
        Product::factory(3)->create();

        $response = $this->get(route('admin.products.index'));

        $response->assertStatus(200);
        $response->assertViewHas('products');
    }

    public function test_admin_can_create_product(): void
    {
        $this->authenticateAdmin();

        $category = Category::factory()->create();

        $payload = [
            'category_id' => $category->id,
            'name' => 'Flagship Smartphone',
            'sku' => 'PHONE-001',
            'price' => 999.99,
            'sale_price' => 899.99,
            'stock' => 50,
            'is_active' => 1,
        ];

        $response = $this->post(route('admin.products.store'), $payload);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', [
            'name' => 'Flagship Smartphone',
            'sku' => 'PHONE-001',
            'price' => 999.99,
        ]);
    }

    public function test_admin_can_update_product(): void
    {
        $this->authenticateAdmin();

        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 500.00,
        ]);

        $response = $this->put(route('admin.products.update', $product), [
            'category_id' => $category->id,
            'name' => 'Updated Product Name',
            'sku' => $product->sku,
            'price' => 450.00,
            'stock' => 20,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product Name',
            'price' => 450.00,
        ]);
    }

    public function test_admin_can_delete_product(): void
    {
        $this->authenticateAdmin();

        $product = Product::factory()->create();

        $response = $this->delete(route('admin.products.destroy', $product));

        $response->assertRedirect(route('admin.products.index'));
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_admin_can_restore_soft_deleted_product(): void
    {
        $this->authenticateAdmin();

        $product = Product::factory()->create();
        $product->delete();

        $response = $this->post(route('admin.products.restore', $product->id));

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', ['id' => $product->id, 'deleted_at' => null]);
    }

    public function test_admin_can_upload_product_image(): void
    {
        Storage::fake('public');
        $this->authenticateAdmin();

        $category = Category::factory()->create();
        $image = UploadedFile::fake()->image('product.jpg');

        $response = $this->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Camera Lens',
            'sku' => 'LENS-001',
            'price' => 199.99,
            'stock' => 20,
            'is_active' => 1,
            'image' => $image,
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', ['sku' => 'LENS-001']);
        $product = Product::where('sku', 'LENS-001')->first();
        $this->assertNotNull($product?->image_path);
        Storage::disk('public')->assertExists($product->image_path);
    }
}
