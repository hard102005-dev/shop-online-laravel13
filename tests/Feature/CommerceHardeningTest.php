<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

final class CommerceHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_cart_page(): void
    {
        $response = $this->get(route('cart.index'));

        $response->assertOk();
        $response->assertSee('Your Cart');
    }

    public function test_user_can_add_active_product_to_cart(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 5, 'is_active' => true]);

        $response = $this->actingAs($user)->post(route('cart.add', $product), ['quantity' => 2]);

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('success', 'Product added to cart.');
        $this->assertSame(2, Session::get('cart')[$product->id]['quantity']);
    }

    public function test_user_cannot_add_inactive_product(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 5, 'is_active' => false]);

        $response = $this->actingAs($user)->post(route('cart.add', $product), ['quantity' => 1]);

        $response->assertSessionHasErrors('product_id');
        $response->assertSessionHasErrors(['product_id' => 'This product is currently unavailable.']);
        $this->assertFalse(Session::has('cart'));
    }

    public function test_user_cannot_add_deleted_product(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 5]);
        $product->delete();

        $response = $this->actingAs($user)->post(route('cart.add', $product), ['quantity' => 1]);

        $response->assertSessionHasErrors('product_id');
        $response->assertSessionHasErrors(['product_id' => 'This product is no longer available.']);
    }

    public function test_quantity_must_be_greater_than_zero(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 5, 'is_active' => true]);

        $response = $this->actingAs($user)->post(route('cart.add', $product), ['quantity' => 0]);

        $response->assertSessionHasErrors('quantity');
    }

    public function test_quantity_cannot_exceed_stock(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 2, 'is_active' => true]);

        $response = $this->actingAs($user)->post(route('cart.add', $product), ['quantity' => 3]);

        $response->assertSessionHasErrors('quantity');
    }

    public function test_user_can_update_and_remove_cart_items(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 5, 'is_active' => true]);

        $this->actingAs($user)->post(route('cart.add', $product), ['quantity' => 2]);
        $this->actingAs($user)->post(route('cart.update', $product->id), ['quantity' => 4]);

        $this->assertSame(4, Session::get('cart')[$product->id]['quantity']);

        $this->actingAs($user)->post(route('cart.remove', $product->id));

        $this->assertSame([], Session::get('cart', []));
    }

    public function test_cart_summary_removes_unavailable_products_and_recalculates_totals(): void
    {
        $product = Product::factory()->create(['stock' => 5, 'is_active' => true, 'price' => 19.99]);
        Session::put('cart', [
            $product->id => [
                'product_id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => (float) $product->effective_price,
                'image_path' => $product->image_path,
                'stock' => $product->stock,
                'quantity' => 2,
            ],
        ]);

        $product->delete();

        $summary = app(CartService::class)->getSummary();

        $this->assertSame([], $summary['items']);
        $this->assertSame(0, $summary['item_count']);
        $this->assertSame(0.0, $summary['subtotal']);
    }

    public function test_update_quantity_above_stock_throws_runtime_exception(): void
    {
        $product = Product::factory()->create(['stock' => 2, 'is_active' => true, 'price' => 15]);

        app(CartService::class)->addItem($product, 2);

        $this->expectException(\RuntimeException::class);
        app(CartService::class)->updateItem($product->id, 3);
    }

    public function test_guest_cannot_checkout(): void
    {
        $response = $this->get(route('checkout.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_empty_cart_cannot_checkout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('checkout.index'));

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error', 'Your cart is empty.');
    }

    public function test_insufficient_stock_prevents_checkout(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 2, 'is_active' => true]);
        Session::put('cart', [
            $product->id => [
                'product_id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => (float) $product->effective_price,
                'image_path' => $product->image_path,
                'stock' => $product->stock,
                'quantity' => 3,
            ],
        ]);

        $response = $this->actingAs($user)->post(route('checkout.store'), [
            'customer_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'Metropolis',
            'postal_code' => '12345',
            'notes' => 'Please hurry',
        ]);

        $response->assertSessionHasErrors('cart');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_successful_checkout_creates_order_and_reduces_stock(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 5, 'is_active' => true, 'price' => 50]);
        Session::put('cart', [
            $product->id => [
                'product_id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => (float) $product->effective_price,
                'image_path' => $product->image_path,
                'stock' => $product->stock,
                'quantity' => 2,
            ],
        ]);

        $response = $this->actingAs($user)->post(route('checkout.store'), [
            'customer_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'Metropolis',
            'postal_code' => '12345',
            'notes' => 'Please hurry',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', ['user_id' => $user->id, 'customer_name' => 'Jane Doe']);
        $this->assertDatabaseHas('order_items', ['product_id' => $product->id, 'quantity' => 2]);
        $this->assertSame(3, $product->fresh()->stock);
        $this->assertSame([], Session::get('cart', []));
    }

    public function test_failed_checkout_does_not_create_partial_order_data(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 1, 'is_active' => true]);
        Session::put('cart', [
            $product->id => [
                'product_id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => (float) $product->effective_price,
                'image_path' => $product->image_path,
                'stock' => $product->stock,
                'quantity' => 2,
            ],
        ]);

        $response = $this->actingAs($user)->post(route('checkout.store'), [
            'customer_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'Metropolis',
            'postal_code' => '12345',
            'notes' => 'Please hurry',
        ]);

        $response->assertSessionHasErrors('cart');
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertSame(1, $product->fresh()->stock);
    }

    public function test_user_can_view_own_order_but_not_another_users_order(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $order = Order::create([
            'user_id' => $owner->id,
            'order_number' => 'ORD-20260808-000001',
            'customer_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'Metropolis',
            'postal_code' => '12345',
            'subtotal' => 100,
            'shipping_fee' => 0,
            'total' => 100,
        ]);

        $this->actingAs($owner)->get(route('orders.show', $order))->assertOk();
        $this->actingAs($otherUser)->get(route('orders.show', $order))->assertForbidden();
    }

    public function test_admin_can_manage_orders_and_normal_user_cannot_access_admin_orders(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $this->actingAs($admin)->get(route('admin.orders.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.orders.index'))->assertForbidden();
    }

    public function test_admin_can_update_order_status_and_invalid_status_is_rejected(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = Order::create([
            'user_id' => $admin->id,
            'order_number' => 'ORD-20260808-000002',
            'customer_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'Metropolis',
            'postal_code' => '12345',
            'subtotal' => 100,
            'shipping_fee' => 0,
            'total' => 100,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.orders.update', $order), [
            'status' => 'processing',
            'payment_status' => 'paid',
        ]);

        $response->assertRedirect(route('admin.orders.show', $order));
        $this->assertSame('processing', $order->fresh()->status);
        $this->assertSame('paid', $order->fresh()->payment_status);

        $invalidResponse = $this->actingAs($admin)->patch(route('admin.orders.update', $order), [
            'status' => 'pending',
            'payment_status' => 'paid',
        ]);

        $invalidResponse->assertSessionHasErrors('status');
    }

    public function test_admin_dashboard_shows_stats_and_recent_orders(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-20260808-000003',
            'customer_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'Metropolis',
            'postal_code' => '12345',
            'subtotal' => 100,
            'shipping_fee' => 0,
            'total' => 100,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);
        Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-20260808-000004',
            'customer_name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'Metropolis',
            'postal_code' => '12345',
            'subtotal' => 200,
            'shipping_fee' => 0,
            'total' => 200,
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Total Orders');
        $response->assertSee('Total Sales');
        $response->assertSee('ORD-20260808-000004');
    }

    public function test_order_numbers_follow_the_required_format_and_are_unique(): void
    {
        $user = User::factory()->create();
        $orderOne = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-20260808-000005',
            'customer_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'Metropolis',
            'postal_code' => '12345',
            'subtotal' => 100,
            'shipping_fee' => 0,
            'total' => 100,
        ]);
        $orderTwo = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-20260808-000006',
            'customer_name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'Metropolis',
            'postal_code' => '12345',
            'subtotal' => 100,
            'shipping_fee' => 0,
            'total' => 100,
        ]);

        $this->assertMatchesRegularExpression('/^ORD-\d{8}-\d{6}$/', $orderOne->order_number);
        $this->assertMatchesRegularExpression('/^ORD-\d{8}-\d{6}$/', $orderTwo->order_number);
        $this->assertNotSame($orderOne->order_number, $orderTwo->order_number);
    }
}
