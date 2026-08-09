@extends('layouts.app')

@section('title', 'ShopOnline - Home')

@section('content')
<div class="row g-4 align-items-stretch">
    <div class="col-lg-8">
        <div class="card card-elevated hero-card border-0 rounded-4 p-4 p-md-5">
            <div class="row align-items-center g-4">
                <div class="col-md-7">
                    <p class="text-light-emphasis mb-2 fw-semibold">Fresh picks for every day</p>
                    <h1 class="display-6 fw-bold mb-3">Shop the latest essentials with confidence.</h1>
                    <p class="mb-4 text-light">Discover a curated collection of everyday favorites, trending products, and great deals in one place.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('products.index') }}" class="btn btn-light text-primary fw-semibold">
                            <i class="bi bi-bag me-2"></i>Browse Products
                        </a>
                        @guest
                            <a href="{{ route('login') }}" class="btn btn-outline-light">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-light">
                                <i class="bi bi-columns-gap me-2"></i>Go to Dashboard
                            </a>
                        @endguest
                    </div>
                </div>
                <div class="col-md-5 text-center">
                    <div class="bg-white bg-opacity-10 rounded-4 p-4">
                        <i class="bi bi-cart-check display-1"></i>
                        <p class="mb-0 mt-2 fw-semibold">Fast checkout</p>
                        <p class="small text-light-emphasis mb-0">Smooth shopping from browsing to delivery.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-elevated border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h2 class="h5 fw-bold mb-3">Why ShopOnline</h2>
                <ul class="list-unstyled mb-0">
                    <li class="d-flex gap-2 mb-3">
                        <i class="bi bi-check-circle-fill text-success mt-1"></i>
                        <span>Curated products across everyday categories.</span>
                    </li>
                    <li class="d-flex gap-2 mb-3">
                        <i class="bi bi-check-circle-fill text-success mt-1"></i>
                        <span>Reliable shopping experience for customers and admins.</span>
                    </li>
                    <li class="d-flex gap-2">
                        <i class="bi bi-check-circle-fill text-success mt-1"></i>
                        <span>Secure account area for orders and profile management.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-md-4">
        <div class="card card-elevated border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h3 class="h6 fw-bold">Browse Catalog</h3>
                <p class="text-muted mb-3">Explore our product collection and discover new favorites.</p>
                <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-sm">Go to Products</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-elevated border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h3 class="h6 fw-bold">Fast Checkout</h3>
                <p class="text-muted mb-3">Add items to cart and complete your order without friction.</p>
                <a href="{{ route('cart.index') }}" class="btn btn-outline-primary btn-sm">View Cart</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-elevated border-0 rounded-4 h-100">
            <div class="card-body p-4">
                <h3 class="h6 fw-bold">Stay Updated</h3>
                <p class="text-muted mb-3">Use your account area to manage profile and orders.</p>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm">Open Dashboard</a>
            </div>
        </div>
    </div>
</div>
@endsection
