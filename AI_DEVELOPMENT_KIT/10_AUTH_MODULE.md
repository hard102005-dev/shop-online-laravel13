# 10 Module Specification: Authentication & User Management

## 1. Status & Overview
- **Status**: Completed (See [PROJECT_MEMORY.md](file:///c:/shop_online/ecommerce/PROJECT_MEMORY.md)).
- **Scope**: Registration, Login, Logout, Password Reset, Role Management (`admin`, `manager`, `customer`).

---

## 2. Architecture & Service Design
- **Form Requests**: `LoginRequest`, `RegisterRequest`, `ResetPasswordRequest`.
- **Service Layer**: `AuthService` handling credential verification, token generation, and password hashing.
- **Repository**: `UserRepositoryInterface` defining user persistence methods.

---

## 3. Database Schema

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->string('phone_number')->nullable();
    $table->string('role')->default('customer')->index(); // 'admin', 'manager', 'customer'
    $table->boolean('is_active')->default(true);
    $table->rememberToken();
    $table->timestamps();
    $table->softDeletes();
});
```

---

## 4. UI Rendering (Bootstrap 5)
- Login & Register views render responsive Bootstrap card forms centered on page grid (`col-md-6 col-lg-4`).
