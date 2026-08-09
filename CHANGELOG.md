2026-08-08

Added:
- Customer-facing storefront with product catalog browsing and product detail pages
- Shopping cart flow with add/update/remove/clear operations and cart summary handling
- Checkout flow that creates orders, stores order details, and reduces product stock
- Order history and order detail pages for authenticated users
- Admin dashboard order statistics and recent-order overview

Hardening:
- Hardened cart validation and session-cart consistency for invalid, inactive, deleted, or oversold products
- Strengthened checkout validation and stock re-checks before order creation
- Switched order pricing calculations to server-side totals driven by database product values
- Made order creation transaction-safe with atomic stock reduction and unique order numbers
- Verified admin order authorization, status/payment updates, and dashboard stats using real database values

Tests:
- Added end-to-end feature coverage for the complete purchase journey, checkout validation, product state-change failures, stock re-checks, price assertions, order history/detail access, admin authorization, status workflow, and transaction rollback

2026-08-07

Added:
- Product management with create/edit/delete, image upload, category assignment, active/inactive status, and soft delete/restore support
- Product admin list with search, pagination, eager loading, and category dropdown selection
- Product validation for SKU uniqueness, price numeric, stock integer, category existence, and image file types
- Product policy and admin-only form request authorization

2026-08-06

Added:
- Category management with create/edit/delete, image upload, admin-only access, and soft delete support
- Category validation via dedicated form requests and admin policy enforcement
- Admin category routes and dashboard integration for category management

2026-08-03

Added:
- Category CRUD
- Admin Dashboard
- Authentication foundation with login, registration, and logout
- Admin-only route protection and seeded admin account

Fixed:
- Route conflict
- Validation error
- Registration flow and admin middleware behavior