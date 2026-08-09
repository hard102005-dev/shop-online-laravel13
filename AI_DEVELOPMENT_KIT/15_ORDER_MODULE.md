# 15 Module Specification: Checkout & Order Processing

## 1. Status & Overview
- **Status**: Remaining (See [PROJECT_MEMORY.md](file:///c:/shop_online/ecommerce/PROJECT_MEMORY.md)).
- **Scope**: Multi-step checkout, shipping address selection, order state machine, stock locking transactions, PDF invoices.

---

## 2. Architecture: Service & Repository Layer

- **Contracts**:
  - `App\Repositories\Contracts\OrderRepositoryInterface`
  - `App\Services\Contracts\CheckoutServiceInterface`
- **Workflow**:
  1. Validate stock for all cart items.
  2. Create Order & OrderItem records within `DB::transaction()`.
  3. Decrement product stock quantities.
  4. Clear user cart.
  5. Fire `OrderCreated` event (triggers customer email notification).

---

## 3. Database Schema

```php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->string('order_number')->unique();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('status')->default('pending')->index(); // pending, processing, shipped, completed, cancelled
    $table->string('payment_status')->default('unpaid')->index(); // unpaid, paid, failed
    $table->string('payment_method'); // stripe, promptpay, bank_transfer, cod
    
    // Address Snapshots
    $table->string('shipping_name');
    $table->string('shipping_phone');
    $table->text('shipping_address');
    
    // Monetary Breakdown
    $table->decimal('subtotal', 12, 2);
    $table->decimal('discount_amount', 12, 2)->default(0.00);
    $table->decimal('tax_amount', 12, 2);
    $table->decimal('shipping_fee', 12, 2);
    $table->decimal('total_amount', 12, 2);
    
    $table->timestamps();
    $table->softDeletes();
});

Schema::create('order_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->cascadeOnDelete();
    $table->foreignId('product_id')->nullable()->nullOnDelete();
    $table->string('product_name');
    $table->string('product_sku');
    $table->decimal('unit_price', 12, 2);
    $table->integer('quantity');
    $table->decimal('subtotal', 12, 2);
    $table->timestamps();
});
```

---

## 4. UI Requirements (Bootstrap 5 Checkout & Order Details)
- Checkout form using Bootstrap card accordion: Section 1 Shipping Address -> Section 2 Payment Method -> Section 3 Order Review.
- Admin order detail view with status dropdown switcher and printable invoice view.
