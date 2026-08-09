@extends('layouts.admin')

@section('title', 'Product Inventory')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-column flex-md-row gap-3">
    <div>
        <h1 class="h3 fw-bold mb-0">Products</h1>
        <p class="text-muted mb-0">Manage catalog items, pricing, SKUs, and stock inventory levels.</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <form action="{{ route('admin.products.index') }}" method="GET" class="d-flex gap-2 align-items-center">
            <input type="hidden" name="trashed" value="{{ $showTrashed ? 1 : 0 }}">
            <input type="search" name="search" value="{{ $search ?? '' }}" class="form-control form-control-sm" placeholder="Search products...">
            <button class="btn btn-outline-secondary btn-sm" type="submit"><i class="bi bi-search"></i></button>
        </form>
        <a href="{{ route('admin.products.index', array_merge(request()->except('page'), ['trashed' => $showTrashed ? 0 : 1])) }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-box-arrow-in-up-right me-1"></i>{{ $showTrashed ? 'Show Active' : 'Show Trashed' }}
        </a>
        <a href="{{ route('admin.products.create') }}" class="btn btn-success shadow-sm btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Add Product
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Image</th>
                        <th>SKU</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td class="ps-4">
                                @if ($product->image_path)
                                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="rounded-3" style="width: 60px; height: 40px; object-fit: cover;" loading="lazy">
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td><code>{{ $product->sku }}</code></td>
                            <td class="fw-bold">
                                {{ $product->name }}
                                @if ($product->is_featured)
                                    <span class="badge bg-warning text-dark ms-1"><i class="bi bi-star-fill me-1"></i>Featured</span>
                                @endif
                                @if ($product->trashed())
                                    <span class="badge bg-danger ms-1">Deleted</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">{{ $product->category->name ?? 'Uncategorized' }}</span>
                            </td>
                            <td>
                                @if ($product->sale_price)
                                    <span class="fw-bold text-success">${{ number_format((float)$product->sale_price, 2) }}</span>
                                    <span class="text-muted text-decoration-line-through fs-7">${{ number_format((float)$product->price, 2) }}</span>
                                @else
                                    <span class="fw-bold">${{ number_format((float)$product->price, 2) }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($product->stock <= 0)
                                    <span class="badge bg-danger">Out of Stock</span>
                                @elseif ($product->stock <= $product->low_stock_threshold)
                                    <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i>{{ $product->stock }} left</span>
                                @else
                                    <span class="badge bg-success-subtle text-success">{{ $product->stock }} in stock</span>
                                @endif
                            </td>
                            <td>
                                @if ($product->is_active)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('products.show', $product->slug) }}" class="btn btn-sm btn-outline-info me-1" target="_blank">
                                    <i class="bi bi-eye me-1"></i>View
                                </a>
                                @if ($product->trashed())
                                    <form action="{{ route('admin.products.restore', $product->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>Restore
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-pencil me-1"></i>Edit
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash me-1"></i>Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No products found. Click 'Add Product' to create one.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if ($products->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $products->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
