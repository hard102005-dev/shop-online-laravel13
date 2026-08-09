# 13 Module Specification: Product Catalog & Inventory

## 1. Status & Overview
- **Status**: Remaining / In Progress (See [PROJECT_MEMORY.md](file:///c:/shop_online/ecommerce/PROJECT_MEMORY.md)).
- **Scope**: Product Catalog management, SKU tracking, Regular vs Sale prices, Stock inventory alerts, multi-image galleries, Bootstrap product cards.

---

## 2. Architecture & Service Contracts

- **Contracts**:
  - `App\Repositories\Contracts\ProductRepositoryInterface`
  - `App\Services\Contracts\ProductServiceInterface`
- **Service Responsibility**: Handling product creation, multi-image upload processing, inventory adjustment, price validation.

---

## 3. Database Schema

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->foreignId('category_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->string('slug')->unique();
    $table->string('sku')->unique()->index();
    $table->text('short_description')->nullable();
    $table->longText('description')->nullable();
    $table->decimal('price', 12, 2);
    $table->decimal('sale_price', 12, 2)->nullable();
    $table->integer('stock')->default(0);
    $table->integer('low_stock_threshold')->default(5);
    $table->boolean('is_featured')->default(false);
    $table->boolean('is_active')->default(true)->index();
    $table->timestamps();
    $table->softDeletes();
});

Schema::create('product_images', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained()->cascadeOnDelete();
    $table->string('image_path');
    $table->integer('sort_order')->default(0);
    $table->boolean('is_primary')->default(false);
    $table->timestamps();
});
```

---

## 4. UI Requirements (Bootstrap 5 Storefront & Admin)
- Storefront: Product grid using Bootstrap `.col-6 .col-md-4 .col-lg-3` cards with badge ribbons (`Sale!`, `Out of Stock`).
- Admin: Product management table with image thumbnail preview, stock quick-edit modal, and category dropdown filter.
