# 03 Database Rules & Schema Standards

## 1. Column Precision Standards

### Financial & Currency Fields
- **ALWAYS** use `decimal(12, 2)` or integer cents (`bigInteger`) for prices, subtotals, taxes, discounts, and total amounts.
- **NEVER** use `float` or `double` for currency to prevent precision rounding errors.

```php
// Standard Financial Columns
$table->decimal('price', 12, 2);
$table->decimal('sale_price', 12, 2)->nullable();
$table->decimal('subtotal', 12, 2);
$table->decimal('tax_amount', 12, 2)->default(0.00);
$table->decimal('discount_amount', 12, 2)->default(0.00);
$table->decimal('total_amount', 12, 2);
```

---

## 2. Foreign Keys & Cascading Strategy
- Use explicit foreign keys with constraint rules: `$table->foreignId('category_id')->constrained()->cascadeOnDelete();` or `nullOnDelete()`.
- Explicitly index foreign key columns and frequently queried flags (`is_active`, `status`, `sku`, `slug`).

---

## 3. Transaction Safety
Wrap multi-table operations inside `DB::transaction()` within the Service Layer:

```php
use Illuminate\Support\Facades\DB;

public function placeOrder(array $orderData): Order
{
    return DB::transaction(function () use ($orderData) {
        $order = $this->orderRepository->create($orderData);
        $this->inventoryService->decrementStock($orderData['items']);
        $this->cartService->clearCart($orderData['user_id']);

        return $order;
    });
}
```

---

## 4. Seeder & Factory Rules
- Model Factories must be created for every model in `database/factories/`.
- Seeders must use Model Factories rather than raw DB insertions.
