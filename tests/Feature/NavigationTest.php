<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class NavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_nav_contains_expected_public_links(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('href="' . route('home') . '"', false);
        $response->assertSee('href="' . route('products.index') . '"', false);
        $response->assertSee('href="' . route('login') . '"', false);
        $response->assertSee('href="' . route('register') . '"', false);
    }

    public function test_authenticated_customer_nav_contains_customer_links(): void
    {
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('href="' . route('orders.index') . '">My Orders', false);
        $response->assertSee('href="' . route('dashboard') . '">Dashboard', false);
        $response->assertSee('href="' . route('profile.edit') . '">Account Settings', false);
        $response->assertSee('action="' . route('logout') . '"', false);
    }
}
