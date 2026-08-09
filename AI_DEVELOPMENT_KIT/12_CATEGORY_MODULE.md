# 12 Module Specification: Category Management

## 1. Status & Overview
- **Status**: Completed (See [PROJECT_MEMORY.md](file:///c:/shop_online/ecommerce/PROJECT_MEMORY.md)).
- **Scope**: Hierarchical parent-child category tree, unique URL slug generation, image uploads, active state toggles.

---

## 2. Architecture: Service & Repository Layer

- **Interface**: `App\Repositories\Contracts\CategoryRepositoryInterface`
- **Implementation**: `App\Repositories\CategoryRepository`
- **Service**: `App\Services\CategoryService` handling slug slugification, image storage, and parent-child hierarchy checks.

---

## 3. Database Schema

```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->string('image_path')->nullable();
    $table->integer('sort_order')->default(0);
    $table->boolean('is_active')->default(true)->index();
    $table->timestamps();
});
```

---

## 4. Business Rules & Validations
- Category slug auto-generated via `Str::slug($name)` upon creation or update.
- Validation: `name` is required; `parent_id` must exist in `categories` table and cannot equal current category ID.
- Bootstrap 5 responsive table displaying category tree depth with nested bullet icons (`└─`).
