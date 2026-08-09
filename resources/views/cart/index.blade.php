@extends('layouts.app')

@section('title', 'Your Cart - ShopOnline')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 fw-bold mb-1">Your Cart</h1>
                        <p class="text-muted mb-0">Review your selected items before checkout.</p>
                    </div>
                    @if (!empty($items))
                        <form action="{{ route('cart.clear') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm">Clear Cart</button>
                        </form>
                    @endif
                </div>
            </div>
            <div class="card-body">
                @if (empty($items))
                    <div class="text-center py-5">
                        <i class="bi bi-cart-x fs-1 text-muted"></i>
                        <h3 class="h5 fw-bold mt-3">Your cart is empty</h3>
                        <p class="text-muted">Browse our products and add something you love.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-primary mt-2">Continue Shopping</a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $productId => $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                                                    @if (!empty($item['image_path']))
                                                        <img src="{{ asset('storage/' . $item['image_path']) }}" alt="{{ $item['name'] }}" class="img-fluid rounded" style="max-height: 56px; object-fit: cover;">
                                                    @else
                                                        <i class="bi bi-box-seam text-muted"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <a href="{{ route('products.show', $item['slug']) }}" class="fw-semibold text-dark text-decoration-none">{{ $item['name'] }}</a>
                                                    <div class="text-muted small">In stock: {{ $item['stock'] }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('cart.update', $productId) }}" method="POST" class="d-flex justify-content-center align-items-center gap-2">
                                                @csrf
                                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ $item['stock'] }}" class="form-control form-control-sm" style="width: 70px;">
                                                <button type="submit" class="btn btn-outline-secondary btn-sm">Update</button>
                                            </form>
                                        </td>
                                        <td class="text-end">${{ number_format((float) $item['price'], 2) }}</td>
                                        <td class="text-end fw-semibold">${{ number_format((float) $item['price'] * (int) $item['quantity'], 2) }}</td>
                                        <td class="text-end">
                                            <form action="{{ route('cart.remove', $productId) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-link text-danger p-0">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if (!empty($items))
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 sticky-top" style="top: 20px;">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">Order Summary</h2>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Items</span>
                        <span>{{ $item_count }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span>${{ number_format((float) $subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Shipping</span>
                        <span>${{ number_format((float) $shipping_fee, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-3 fw-bold fs-5">
                        <span>Total</span>
                        <span>${{ number_format((float) $total, 2) }}</span>
                    </div>
                    <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100 mt-4">Proceed to Checkout</a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
