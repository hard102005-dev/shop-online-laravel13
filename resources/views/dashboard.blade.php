@extends('layouts.app')

@section('title', 'Dashboard - ShopOnline')

@section('content')
@php($cartQuantity = collect(session('cart', []))->sum('quantity'))

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card card-elevated border-0 rounded-4">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                    <div>
                        <p class="text-primary fw-semibold mb-2">Welcome back</p>
                        <h1 class="h3 fw-bold mb-2">{{ auth()->user()->name }}</h1>
                        <p class="text-muted mb-0">Manage your shopping activity, review your orders, and continue discovering great products.</p>
                    </div>
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 align-self-start">
                        <i class="bi bi-shield-check me-1"></i>Customer account
                    </span>
                </div>

                <div class="row g-3 mt-3">
                    <div class="col-md-6">
                        <a href="{{ route('products.index') }}" class="btn btn-primary w-100 py-3 rounded-3">
                            <i class="bi bi-bag me-2"></i>Continue Shopping
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('cart.index') }}" class="btn btn-outline-primary w-100 py-3 rounded-3">
                            <i class="bi bi-cart3 me-2"></i>View Cart ({{ $cartQuantity }})
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary w-100 py-3 rounded-3">
                            <i class="bi bi-box-seam me-2"></i>My Orders
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-dark w-100 py-3 rounded-3">
                            <i class="bi bi-person-gear me-2"></i>Account Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-elevated border-0 rounded-4">
            <div class="card-body p-4">
                <h2 class="h5 fw-bold mb-3">Account overview</h2>
                <div class="d-flex flex-column gap-3">
                    <div class="border rounded-3 p-3 bg-light-subtle">
                        <div class="text-muted small">Name</div>
                        <div class="fw-semibold">{{ auth()->user()->name }}</div>
                    </div>
                    <div class="border rounded-3 p-3 bg-light-subtle">
                        <div class="text-muted small">Email</div>
                        <div class="fw-semibold">{{ auth()->user()->email }}</div>
                    </div>
                    <div class="border rounded-3 p-3 bg-light-subtle">
                        <div class="text-muted small">Cart items</div>
                        <div class="fw-semibold">{{ $cartQuantity }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
