# 16 Module Specification: Customer Profile & Address Book

## 1. Status & Overview
- **Status**: Planned (See [PROJECT_MEMORY.md](file:///c:/shop_online/ecommerce/PROJECT_MEMORY.md)).
- **Scope**: Customer profile update, password security, multiple address book management, order history view.

---

## 2. Architecture & Database Schema (`user_addresses`)

```php
Schema::create('user_addresses', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('recipient_name');
    $table->string('phone_number');
    $table->string('address_line_1');
    $table->string('address_line_2')->nullable();
    $table->string('city');
    $table->string('state_province');
    $table->string('postal_code');
    $table->string('country_code', 2)->default('TH');
    $table->boolean('is_default')->default(false);
    $table->timestamps();
});
```

---

## 3. UI Requirements (Bootstrap 5 Customer Portal)
- Customer dashboard tabbed layout (`.nav-tabs` / `.list-group`): Profile Info, Change Password, Address Book, Order History.
- Modal dialog for adding/editing shipping addresses.
