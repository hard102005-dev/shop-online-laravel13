# 05 UI Guidelines: Bootstrap 5 Design System

## 1. UI Framework Architecture
- The frontend UI is constructed using **Bootstrap 5.3+** CSS/JS integrated with Laravel Blade templates.
- SCSS/Sass assets compiled via Vite (`resources/sass/app.scss`).

---

## 2. Layout Structure & Responsive Grids
- Use standard Bootstrap 5 container grid systems: `.container`, `.container-fluid`, `.row`, `.col-12`, `.col-md-6`, `.col-lg-4`.
- All data tables must be wrapped inside `<div class="table-responsive">` to ensure mobile responsiveness.

```html
<!-- Responsive Table Example -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 text-dark fw-bold">Product Inventory</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Items -->
                </tbody>
            </table>
        </div>
    </div>
</div>
```

---

## 3. Component Standards
- **Buttons**: Use standard Bootstrap button variants (`.btn-primary`, `.btn-outline-secondary`, `.btn-danger`, `.btn-sm`).
- **Badges**: Use badge pills for order status:
  - `pending`: `<span class="badge bg-warning text-dark">Pending</span>`
  - `processing`: `<span class="badge bg-info text-white">Processing</span>`
  - `completed`: `<span class="badge bg-success">Completed</span>`
  - `cancelled`: `<span class="badge bg-danger">Cancelled</span>`
- **Feedback & Toast Notifications**: Display success/error flash alerts using Bootstrap alert dismissible components (`.alert .alert-success .alert-dismissible`).
