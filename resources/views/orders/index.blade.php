@extends('layouts.app')

@section('title', 'Order History - ShopOnline')

@section('content')
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white border-0 py-3">
        <h1 class="h3 fw-bold mb-1">Order History</h1>
        <p class="text-muted mb-0">View your recent purchases and order details.</p>
    </div>
    <div class="card-body">
        @if ($orders->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-receipt fs-1 text-muted"></i>
                <h3 class="h5 fw-bold mt-3">No orders yet</h3>
                <p class="text-muted">Your completed orders will appear here once you place an order.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary mt-2">Shop Now</a>
            </div>
        @else
            <div class="list-group">
                @foreach ($orders as $order)
                    <a href="{{ route('orders.show', $order) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold">{{ $order->order_number }}</div>
                            <div class="text-muted small">Placed {{ $order->created_at->format('M d, Y H:i') }}</div>
                        </div>
                        <div class="text-end">
                            <div class="fw-semibold">${{ number_format((float) $order->total, 2) }}</div>
                            <div class="badge bg-primary-subtle text-primary">{{ ucfirst($order->status) }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
