# 01 Project Overview: ShopOnline E-Commerce Platform

## System Overview
**ShopOnline** is a high-performance, responsive B2C e-commerce platform built on **Laravel 11+ / PHP 8.3+**, **Bootstrap 5 UI**, and a modular **Service-Repository** pattern. It provides a storefront for buyers and a control center for store admins.

---

## Technical Stack

| Component | Technology | Specification |
|---|---|---|
| **Backend Framework** | Laravel 11.x / 13.x | PHP 8.3+, Artisan CLI, Eloquent ORM |
| **Architecture Pattern** | Service-Repository Pattern | Thin Controllers, Business Services, DB Repositories |
| **Frontend Framework** | Bootstrap 5.3+ | Responsive Grid, Blade Components, Vanilla JS / Alpine.js |
| **Database Engine** | MySQL 8.0+ / SQLite | UTF8MB4, Foreign Key Constraints, Transactions |
| **Asset Bundler** | Vite | Sass/SCSS compilation, Bootstrap JS bundling |
| **Testing Engine** | PHPUnit 11+ / Pest | Unit, Feature, Database, and Integration tests |

---

## E-Commerce Domain Entities

```
+---------------+     1:N     +---------------+     1:N     +---------------+
|   Category    | ----------->|    Product    | ----------->| ProductImage  |
+---------------+             +---------------+             +---------------+
                                      |                             |
                                      | 1:N                         | 1:N
                                      v                             v
                              +---------------+             +---------------+
                              |   CartItem    |             |   OrderItem   |
                              +---------------+             +---------------+
                                      ^                             ^
                                      | N:1                         | N:1
+---------------+     1:N     +---------------+     1:N     +---------------+
|     User      | ----------->| ShoppingCart  |             |     Order     |
+---------------+             +---------------+             +---------------+
```

---

## Project Status & Progress Tracking
Track project milestones in [PROJECT_MEMORY.md](file:///c:/shop_online/ecommerce/PROJECT_MEMORY.md) and [TODO.md](file:///c:/shop_online/ecommerce/AI_DEVELOPMENT_KIT/TODO.md):
- **Completed**: Authentication, Admin Dashboard, Category CRUD.
- **In Progress / Remaining**: Product Catalog, Shopping Cart, Checkout Workflow, Order Management, Sales Reports.
