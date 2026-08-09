@extends('layouts.app')

@section('title', 'Order Details - ShopOnline')

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h1 class="h3 fw-bold mb-1">Order {{ $order->order_number }}</h1>
                        <p class="text-muted mb-0">Placed {{ $order->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <span class="badge bg-primary-subtle text-primary">{{ ucfirst($order->status) }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <h2 class="h6 fw-bold">Shipping Details</h2>
                        <p class="mb-1">{{ $order->customer_name }}</p>
                        <p class="mb-1">{{ $order->email }}</p>
                        <p class="mb-1">{{ $order->phone }}</p>
                        <p class="mb-0">{{ $order->address }}, {{ $order->city }} {{ $order->postal_code }}</p>
                    </div>
                    <div class="col-md-6">
                        <h2 class="h6 fw-bold">Payment</h2>
                        <p class="mb-1">Status: {{ ucfirst($order->payment_status) }}</p>
                        <p class="mb-0">Notes: {{ $order->notes ?: 'None' }}</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>{{ $item->product_name }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">${{ number_format((float) $item->unit_price, 2) }}</td>
                                    <td class="text-end">${{ number_format((float) $item->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <h2 class="h5 fw-bold mb-3">Order Summary</h2>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span>${{ number_format((float) $order->subtotal, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Shipping</span>
                    <span>${{ number_format((float) $order->shipping_fee, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between border-top pt-3 fw-bold fs-5">
                    <span>Total</span>
                    <span>${{ number_format((float) $order->total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
