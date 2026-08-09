<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateAdmin(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    public function test_admin_can_view_category_list(): void
    {
        $this->authenticateAdmin();
        Category::factory(3)->create();

        $response = $this->get(route('admin.categories.index'));

        $response->assertStatus(200);
        $response->assertViewHas('categories');
    }

    public function test_admin_can_create_category(): void
    {
        $this->authenticateAdmin();

        $payload = [
            'name' => 'Audio & Sound',
            'description' => 'Headphones and speakers',
            'sort_order' => 1,
            'is_active' => 1,
        ];

        $response = $this->post(route('admin.categories.store'), $payload);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Audio & Sound',
            'slug' => 'audio-sound',
        ]);
    }

    public function test_admin_can_update_category(): void
    {
        $this->authenticateAdmin();
        $category = Category::factory()->create(['name' => 'Old Category']);

        $response = $this->put(route('admin.categories.update', $category), [
            'name' => 'New Updated Category',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'New Updated Category',
            'slug' => 'new-updated-category',
        ]);
    }

    public function test_admin_can_delete_category(): void
    {
        $this->authenticateAdmin();
        $category = Category::factory()->create();

        $response = $this->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
