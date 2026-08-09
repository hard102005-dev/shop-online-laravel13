@extends('layouts.app')

@section('title', 'Products Catalog - ShopOnline')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h1 class="h2 fw-bold text-dark mb-1">Product Catalog</h1>
        <p class="text-muted mb-0">Browse our latest collection of premium products.</p>
    </div>
    <!-- Search & Filter Form -->
    <div class="col-md-6 mt-3 mt-md-0">
        <form action="{{ route('products.index') }}" method="GET" class="d-flex gap-2">
            @if (request()->has('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search products..." value="{{ $search }}">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i></button>
            </div>
            @if ($search || $categoryId)
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary" title="Clear Filters"><i class="bi bi-x-circle"></i></a>
            @endif
        </form>
    </div>
</div>

<div class="row">
    <!-- Category Sidebar Filter -->
    <div class="col-lg-3 mb-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0"><i class="bi bi-funnel me-2"></i>Categories</h5>
            </div>
            <div class="list-group list-group-flush">
                <a href="{{ route('products.index', array_filter(['search' => $search])) }}" 
                   class="list-group-item list-group-item-action border-0 {{ !$categoryId ? 'active fw-bold' : '' }}">
                    All Categories
                </a>
                @foreach ($categories as $parent)
                    <div class="fw-bold text-uppercase fs-7 text-muted px-3 mt-3 mb-1">{{ $parent->name }}</div>
                    @foreach ($parent->children as $child)
                        <a href="{{ route('products.index', array_filter(['category' => $child->id, 'search' => $search])) }}" 
                           class="list-group-item list-group-item-action border-0 ps-4 {{ $categoryId == $child->id ? 'active fw-bold' : '' }}">
                            {{ $child->name }}
                        </a>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>

    <!-- Product Grid -->
    <div class="col-lg-9">
        <div class="row g-4">
            @forelse ($products as $product)
                <div class="col-12 col-sm-6 col-xl-4">
                    <div class="card h-100 border-0 shadow-sm rounded-3 product-card position-relative overflow-hidden">
                        @if ($product->is_featured)
                            <span class="position-absolute top-0 start-0 bg-warning text-dark px-3 py-1 rounded-end-pill fw-bold fs-7 shadow-sm mt-2">
                                <i class="bi bi-star-fill me-1"></i>Featured
                            </span>
                        @endif

                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="bi bi-box-seam fs-1 text-secondary opacity-50"></i>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <span class="text-muted fs-7 mb-1">{{ $product->category->name ?? 'Category' }}</span>
                            <h5 class="card-title fw-bold text-dark mb-2 fs-6">
                                <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none text-dark">
                                    {{ $product->name }}
                                </a>
                            </h5>
                            <p class="card-text text-muted fs-7 mb-3 flex-grow-1">
                                {{ Str::limit($product->short_description ?? $product->description, 70) }}
                            </p>

                            <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                <div>
                                    @if ($product->sale_price)
                                        <span class="fw-bold text-success fs-5">${{ number_format((float)$product->sale_price, 2) }}</span>
                                        <span class="text-muted text-decoration-line-through fs-7">${{ number_format((float)$product->price, 2) }}</span>
                                    @else
                                        <span class="fw-bold text-dark fs-5">${{ number_format((float)$product->price, 2) }}</span>
                                    @endif
                                </div>
                                <div class="d-flex gap-2">
                                    <form action="{{ route('cart.add', $product) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                            <i class="bi bi-cart-plus me-1"></i>Add
                                        </button>
                                    </form>
                                    <a href="{{ route('products.show', $product->slug) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="mb-3"><i class="bi bi-search fs-1 text-muted"></i></div>
                    <h4 class="fw-bold text-dark">No Products Found</h4>
                    <p class="text-muted">Try adjusting your category filter or search keywords.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary mt-2">Clear Filters</a>
                </div>
            @endforelse
        </div>

        @if ($products->hasPages())
            <div class="d-flex justify-content-center mt-5">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
