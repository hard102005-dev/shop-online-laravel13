<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

final class CommerceE2ETest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_purchase_flow_from_login_to_order_detail(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'is_active' => true, 'price' => 100]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->get(route('products.index'))->assertOk()->assertSee($product->name);
        $this->get(route('products.show', $product->slug))->assertOk()->assertSee($product->name);

        $this->post(route('cart.add', ['product' => $product->id]), ['quantity' => 2])
            ->assertRedirect(route('cart.index'));

        $cart = Session::get('cart', []);
        $this->assertSame(2, $cart[$product->id]['quantity']);

        $this->post(route('cart.update', ['productId' => $product->id]), ['quantity' => 3])
            ->assertRedirect(route('cart.index'));

        $this->assertSame(3, Session::get('cart')[$product->id]['quantity']);

        $this->get(route('checkout.index'))->assertOk();

        $response = $this->post(route('checkout.store'), [
            'customer_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'Metropolis',
            'postal_code' => '12345',
            'notes' => 'Please hurry',
        ]);

        $response->assertRedirect();

        $order = Order::query()->where('user_id', $user->id)->latest()->first();
        $this->assertNotNull($order);
        $this->assertSame(300.0, (float) $order->subtotal);
        $this->assertSame(0.0, (float) $order->shipping_fee);
        $this->assertSame(300.0, (float) $order->total);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'unit_price' => 100.0,
            'total_price' => 300.0,
            'product_name' => $product->name,
        ]);
        $this->assertSame(7, $product->fresh()->stock);
        $this->assertSame([], Session::get('cart', []));

        $this->get(route('orders.index'))->assertOk()->assertSee($order->order_number);
        $this->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee($product->name)
            ->assertSee('$300.00');
    }

    public function test_checkout_validation_rejects_invalid_customer_and_shipping_data(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 5, 'is_active' => true, 'price' => 100]);
        Session::put('cart', [
            $product->id => [
                'product_id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => (float) $product->effective_price,
                'image_path' => $product->image_path,
                'stock' => $product->stock,
                'quantity' => 1,
            ],
        ]);

        $invalidPayloads = [
            ['expectedErrors' => ['customer_name'], 'payload' => ['customer_name' => '', 'email' => 'jane@example.com', 'phone' => '1234567890', 'address' => '123 Main St', 'city' => 'Metropolis', 'postal_code' => '12345']],
            ['expectedErrors' => ['phone'], 'payload' => ['customer_name' => 'Jane Doe', 'email' => 'jane@example.com', 'phone' => '', 'address' => '123 Main St', 'city' => 'Metropolis', 'postal_code' => '12345']],
            ['expectedErrors' => ['address'], 'payload' => ['customer_name' => 'Jane Doe', 'email' => 'jane@example.com', 'phone' => '1234567890', 'address' => '', 'city' => 'Metropolis', 'postal_code' => '12345']],
            ['expectedErrors' => ['email'], 'payload' => ['customer_name' => 'Jane Doe', 'email' => '', 'phone' => '1234567890', 'address' => '123 Main St', 'city' => 'Metropolis', 'postal_code' => '12345']],
            ['expectedErrors' => ['city'], 'payload' => ['customer_name' => 'Jane Doe', 'email' => 'jane@example.com', 'phone' => '1234567890', 'address' => '123 Main St', 'city' => '', 'postal_code' => '12345']],
            ['expectedErrors' => ['postal_code'], 'payload' => ['customer_name' => 'Jane Doe', 'email' => 'jane@example.com', 'phone' => '1234567890', 'address' => '123 Main St', 'city' => 'Metropolis', 'postal_code' => '']],
        ];

        foreach ($invalidPayloads as $invalidCase) {
            $this->actingAs($user)->post(route('checkout.store'), $invalidCase['payload'])
                ->assertSessionHasErrors($invalidCase['expectedErrors']);
        }

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertSame(5, $product->fresh()->stock);
    }

    public function test_checkout_fails_safely_when_product_is_deleted_before_checkout(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 4, 'is_active' => true, 'price' => 100]);
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

        $this->actingAs($user)->post(route('checkout.store'), [
            'customer_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'Metropolis',
            'postal_code' => '12345',
        ])->assertSessionHasErrors('cart');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertSame(4, Product::query()->withTrashed()->find($product->id)->stock);
    }

    public function test_checkout_fails_safely_when_product_is_deactivated_before_checkout(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 4, 'is_active' => true, 'price' => 100]);
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

        $product->update(['is_active' => false]);

        $this->actingAs($user)->post(route('checkout.store'), [
            'customer_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'Metropolis',
            'postal_code' => '12345',
        ])->assertSessionHasErrors('cart');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertSame(4, $product->fresh()->stock);
    }

    public function test_checkout_fails_when_stock_has_changed_since_cart_was_created(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'is_active' => true, 'price' => 100]);
        Session::put('cart', [
            $product->id => [
                'product_id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => (float) $product->effective_price,
                'image_path' => $product->image_path,
                'stock' => $product->stock,
                'quantity' => 5,
            ],
        ]);

        $product->update(['stock' => 2]);

        $this->actingAs($user)->post(route('checkout.store'), [
            'customer_name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '1234567890',
            'address' => '123 Main St',
            'city' => 'Metropolis',
            'postal_code' => '12345',
        ])->assertSessionHasErrors('cart');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertSame(2, $product->fresh()->stock);
        $this->assertSame(5, Session::get('cart')[$product->id]['quantity']);
    }

    public function test_order_history_only_shows_the_authenticated_users_orders(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $ownerOrder = Order::create([
            'user_id' => $owner->id,
            'order_number' => 'ORD-20260808-000101',
            'customer_name' => 'Owner User',
            'email' => 'owner@example.com',
            'phone' => '1111111111',
            'address' => '1 Main St',
            'city' => 'City',
            'postal_code' => '10000',
            'subtotal' => 100,
            'shipping_fee' => 0,
            'total' => 100,
        ]);
        Order::create([
            'user_id' => $otherUser->id,
            'order_number' => 'ORD-20260808-000102',
            'customer_name' => 'Other User',
            'email' => 'other@example.com',
            'phone' => '2222222222',
            'address' => '2 Main St',
            'city' => 'City',
            'postal_code' => '10000',
            'subtotal' => 100,
            'shipping_fee' => 0,
            'total' => 100,
        ]);

        $this->actingAs($owner)->get(route('orders.index'))
            ->assertOk()
            ->assertSee($ownerOrder->order_number)
            ->assertDontSee('ORD-20260808-000102');
    }

    public function test_order_detail_is_visible_to_the_owner_but_not_to_other_users(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $product = Product::factory()->create(['price' => 120, 'is_active' => true]);
        $order = Order::create([
            'user_id' => $owner->id,
            'order_number' => 'ORD-20260808-000201',
            'customer_name' => 'Owner User',
            'email' => 'owner@example.com',
            'phone' => '1111111111',
            'address' => '1 Main St',
            'city' => 'City',
            'postal_code' => '10000',
            'subtotal' => 240,
            'shipping_fee' => 0,
            'total' => 240,
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'sku' => $product->sku,
            'quantity' => 2,
            'unit_price' => 120,
            'total_price' => 240,
        ]);

        $this->actingAs($owner)->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee($product->name)
            ->assertSee('$240.00');
        $this->actingAs($otherUser)->get(route('orders.show', $order))->assertForbidden();
    }

    public function test_admin_can_manage_orders_but_normal_users_cannot(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-20260808-000301',
            'customer_name' => 'Normal User',
            'email' => 'user@example.com',
            'phone' => '1234567890',
            'address' => '1 Main St',
            'city' => 'City',
            'postal_code' => '10000',
            'subtotal' => 100,
            'shipping_fee' => 0,
            'total' => 100,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $this->actingAs($admin)->get(route('admin.orders.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.orders.show', $order))->assertOk();
        $this->actingAs($admin)->patch(route('admin.orders.update', $order), ['status' => 'processing', 'payment_status' => 'paid'])->assertRedirect();

        $this->actingAs($user)->get(route('admin.orders.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.orders.show', $order))->assertForbidden();
        $this->actingAs($user)->patch(route('admin.orders.update', $order), ['status' => 'processing', 'payment_status' => 'paid'])->assertForbidden();
    }

    public function test_order_status_updates_follow_the_defined_workflow_and_reject_invalid_transitions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = Order::create([
            'user_id' => $admin->id,
            'order_number' => 'ORD-20260808-000401',
            'customer_name' => 'Admin User',
            'email' => 'admin@example.com',
            'phone' => '1234567890',
            'address' => '1 Main St',
            'city' => 'City',
            'postal_code' => '10000',
            'subtotal' => 100,
            'shipping_fee' => 0,
            'total' => 100,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $this->actingAs($admin)->patch(route('admin.orders.update', $order), ['status' => 'processing', 'payment_status' => 'paid'])
            ->assertRedirect();
        $this->assertSame('processing', $order->fresh()->status);
        $this->assertSame('paid', $order->fresh()->payment_status);

        $this->actingAs($admin)->patch(route('admin.orders.update', $order), ['status' => 'confirmed', 'payment_status' => 'paid'])
            ->assertSessionHasErrors('status');

        $this->actingAs($admin)->patch(route('admin.orders.update', $order), ['status' => 'not-a-status', 'payment_status' => 'paid'])
            ->assertSessionHasErrors('status');
    }

    public function test_order_creation_exception_rolls_back_the_transaction(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'is_active' => true, 'price' => 100]);
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

        OrderItem::flushEventListeners();
        OrderItem::creating(function (): void {
            throw new \RuntimeException('Simulated order item failure');
        });

        try {
            $this->actingAs($user)->post(route('checkout.store'), [
                'customer_name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'phone' => '1234567890',
                'address' => '123 Main St',
                'city' => 'Metropolis',
                'postal_code' => '12345',
            ])->assertSessionHas('error');
        } finally {
            OrderItem::flushEventListeners();
        }

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
        $this->assertSame(10, $product->fresh()->stock);
        $this->assertSame(2, Session::get('cart')[$product->id]['quantity']);
    }
}
