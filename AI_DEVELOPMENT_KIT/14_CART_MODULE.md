# 14 Module Specification: Shopping Cart & Coupon Engine

## 1. Status & Overview
- **Status**: Remaining (See [PROJECT_MEMORY.md](file:///c:/shop_online/ecommerce/PROJECT_MEMORY.md)).
- **Scope**: Hybrid session/DB shopping cart, guest cart migration upon login, coupon code discounts, stock level validations.

---

## 2. Architecture & Service Contracts (`CartService`)

```php
namespace App\Services;

use App\Models\Cart;
use App\Repositories\Contracts\CartRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;

final class CartService
{
    public function __construct(
        private readonly CartRepositoryInterface $cartRepository,
        private readonly ProductRepositoryInterface $productRepository
    ) {}

    public function addToCart(int|string $cartIdentifier, int $productId, int $quantity): Cart
    {
        // 1. Verify product stock availability
        // 2. Add or update cart item
        // 3. Recalculate cart subtotal
    }
}
```

---

## 3. Database Schema

```php
Schema::create('carts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
    $table->string('session_id')->nullable()->index();
    $table->string('coupon_code')->nullable();
    $table->decimal('discount_amount', 12, 2)->default(0.00);
    $table->timestamps();
});

Schema::create('cart_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
    $table->foreignId('product_id')->constrained()->cascadeOnDelete();
    $table->integer('quantity');
    $table->decimal('unit_price', 12, 2);
    $table->timestamps();
    
    $table->unique(['cart_id', 'product_id']);
});
```

---

## 4. UI Requirements (Bootstrap 5 Cart Drawer / Table)
- Offcanvas cart drawer + dedicated `/cart` table view with quantity increment/decrement buttons and real-time total summary box.
