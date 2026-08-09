@extends('layouts.app')

@section('title', $product->name . ' - ShopOnline')

@section('content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
        @if ($product->category)
            <li class="breadcrumb-item"><a href="{{ route('products.index', ['category' => $product->category->id]) }}">{{ $product->category->name }}</a></li>
        @endif
        <li class="breadcrumb-item active">{{ $product->name }}</li>
    </ol>
</nav>

<div class="row g-5">
    <!-- Image Gallery Section -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="bg-light d-flex align-items-center justify-content-center p-5" style="min-height: 380px;">
                <i class="bi bi-box-seam display-1 text-secondary opacity-50"></i>
            </div>
        </div>
    </div>

    <!-- Product Details Section -->
    <div class="col-md-6">
        <div class="mb-3">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold">{{ $product->category->name ?? 'Uncategorized' }}</span>
            @if ($product->is_featured)
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold ms-1"><i class="bi bi-star-fill me-1"></i>Featured</span>
            @endif
        </div>

        <h1 class="h2 fw-bold text-dark mb-2">{{ $product->name }}</h1>
        <p class="text-muted mb-3">SKU: <code>{{ $product->sku }}</code></p>

        <div class="mb-4">
            @if ($product->sale_price)
                <span class="display-6 fw-bold text-success me-3">${{ number_format((float)$product->sale_price, 2) }}</span>
                <span class="fs-4 text-muted text-decoration-line-through">${{ number_format((float)$product->price, 2) }}</span>
                <span class="badge bg-danger ms-2">SAVE {{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%</span>
            @else
                <span class="display-6 fw-bold text-dark">${{ number_format((float)$product->price, 2) }}</span>
            @endif
        </div>

        @if ($product->short_description)
            <p class="lead fs-6 text-muted mb-4">{{ $product->short_description }}</p>
        @endif

        <div class="mb-4">
            <h6 class="fw-bold mb-2">Availability:</h6>
            @if ($product->stock <= 0)
                <span class="badge bg-danger p-2 fs-6"><i class="bi bi-x-circle me-1"></i>Out of Stock</span>
            @elseif ($product->stock <= $product->low_stock_threshold)
                <span class="badge bg-warning text-dark p-2 fs-6"><i class="bi bi-exclamation-triangle me-1"></i>Low Stock ({{ $product->stock }} left)</span>
            @else
                <span class="badge bg-success p-2 fs-6"><i class="bi bi-check-circle me-1"></i>In Stock ({{ $product->stock }} available)</span>
            @endif
        </div>

        <form action="{{ route('cart.add', $product) }}" method="POST" class="d-flex gap-3 mb-4">
            @csrf
            <div class="input-group style-qty" style="width: 130px;">
                <button class="btn btn-outline-secondary" type="button" onclick="this.parentNode.querySelector('input[type=number]').stepDown()"><i class="bi bi-dash"></i></button>
                <input type="number" name="quantity" class="form-control text-center" value="1" min="1" max="{{ $product->stock }}">
                <button class="btn btn-outline-secondary" type="button" onclick="this.parentNode.querySelector('input[type=number]').stepUp()"><i class="bi bi-plus"></i></button>
            </div>
            <button type="submit" class="btn btn-primary btn-lg flex-grow-1 {{ $product->stock <= 0 ? 'disabled' : '' }}">
                <i class="bi bi-cart-plus me-2"></i>Add to Cart
            </button>
        </form>

        @if ($product->description)
            <div class="border-top pt-4 mt-4">
                <h5 class="fw-bold mb-3">Product Overview</h5>
                <div class="text-secondary lh-lg">{!! nl2br(e($product->description)) !!}</div>
            </div>
        @endif
    </div>
</div>
@endsection
