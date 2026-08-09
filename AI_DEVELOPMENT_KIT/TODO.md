# Project Task Tracking (TODO)

## Completed Tasks
- [x] Initial Laravel 11/13 project workspace setup.
- [x] Configure AI Development Kit specification suite.
- [x] Module 10: Authentication System (User Registration, Login, Logout, Roles).
- [x] Module 11: Admin Dashboard & KPI Summary Engine.
- [x] Module 12: Category Management & Parent-Child Tree.

---

## Active & Upcoming Tasks

### Module 13: Product Catalog & Stock Control
- [ ] Create `products` & `product_images` migrations and Eloquent models.
- [ ] Create `ProductRepository` and `ProductService`.
- [ ] Create `StoreProductRequest` and `UpdateProductRequest`.
- [ ] Build Admin Product CRUD Blade views (Bootstrap 5).
- [ ] Build Customer Storefront catalog & search grid.
- [ ] Write Feature tests (`ProductCatalogTest`).

### Module 14: Shopping Cart & Coupons
- [ ] Create `carts` and `cart_items` migrations and models.
- [ ] Implement `CartService` (Add, Update, Remove, Guest-to-User Merge).
- [ ] Build Offcanvas Cart Drawer & Cart page view (Bootstrap 5).
- [ ] Implement Coupon validation logic.

### Module 15: Checkout & Order Processing
- [ ] Create `orders` and `order_items` migrations.
- [ ] Implement `CheckoutService` with `DB::transaction()` stock locking.
- [ ] Implement multi-step Checkout view (Bootstrap Accordion).
- [ ] Add PDF Invoice generation.

### Module 16: Profile & Address Book
- [ ] Create `user_addresses` migration and model.
- [ ] Implement Address Book management UI.
- [ ] Build Customer Order History detail view.

### Module 17: Reports & Analytics
- [ ] Implement `ReportService` for daily/monthly revenue.
- [ ] Implement streamed CSV export for orders and inventory.
