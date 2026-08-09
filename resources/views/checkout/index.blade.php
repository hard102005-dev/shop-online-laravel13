@extends('layouts.app')

@section('title', 'Checkout - ShopOnline')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <h1 class="h3 fw-bold mb-3">Checkout</h1>
                <p class="text-muted">Enter your details to complete your order.</p>

                <form action="{{ route('checkout.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $user->name ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="3" required>{{ old('address') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Postal Code</label>
                            <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-4">Place Order</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <h2 class="h5 fw-bold mb-3">Order Summary</h2>
                @foreach ($summary['items'] as $item)
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ $item['name'] }} x{{ $item['quantity'] }}</span>
                        <span>${{ number_format((float) $item['price'] * (int) $item['quantity'], 2) }}</span>
                    </div>
                @endforeach
                <div class="d-flex justify-content-between border-top pt-3 mt-3 fw-bold">
                    <span>Subtotal</span>
                    <span>${{ number_format((float) $summary['subtotal'], 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <span>Shipping</span>
                    <span>${{ number_format((float) $summary['shipping_fee'], 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mt-2 fs-5 fw-bold">
                    <span>Total</span>
                    <span>${{ number_format((float) $summary['total'], 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
