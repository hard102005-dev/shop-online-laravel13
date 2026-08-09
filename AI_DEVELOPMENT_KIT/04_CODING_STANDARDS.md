# 04 Coding Standards & Clean Code Rules

## 1. PSR-12 & Strict Typing
- Every PHP file **must** begin with `declare(strict_types=1);` immediately after the `<?php` tag.
- Follow **PSR-12** formatting standards (enforced via Laravel Pint).
- Explicitly declare method parameter types and return types on all functions and class properties.

```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;

final class InventoryService
{
    public function isStockAvailable(Product $product, int $requestedQuantity): bool
    {
        return $product->stock >= $requestedQuantity;
    }
}
```

---

## 2. Naming Conventions

| Artifact | Convention | Example |
|---|---|---|
| **Classes / Interfaces** | PascalCase | `ProductService`, `CategoryRepositoryInterface` |
| **Methods / Functions** | camelCase | `calculateTax()`, `findActiveBySlug()` |
| **Variables / Properties** | camelCase | `$shippingFee`, `$cartItems` |
| **Database Tables** | Plural, snake_case | `products`, `order_items` |
| **Database Columns** | Snake_case | `unit_price`, `stock_quantity` |
| **Route Names** | Dot notation, lower-kebab | `admin.categories.index`, `cart.add` |

---

## 3. Validation Rule: Dedicated Form Requests
Inline `$request->validate([...])` inside Controller methods is prohibited. Create dedicated Form Request classes (`App\Http\Requests\`) for every POST, PUT, or PATCH operation.
