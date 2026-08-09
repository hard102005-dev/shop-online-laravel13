@extends('layouts.admin')

@section('title', 'Order Details')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Order {{ $order->order_number }}</h1>
        <p class="text-muted mb-0">Placed {{ $order->created_at->format('M d, Y H:i') }}</p>
    </div>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">Back to Orders</a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="row g-3">
                    @csrf
                    @method('PATCH')
                    <div class="col-md-6">
                        <label class="form-label">Order Status</label>
                        <select name="status" class="form-select">
                            @foreach (App\Models\Order::STATUSES as $status)
                                <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Payment Status</label>
                        <select name="payment_status" class="form-select">
                            @foreach (App\Models\Order::PAYMENT_STATUSES as $status)
                                <option value="{{ $status }}" {{ $order->payment_status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body">
                <h2 class="h6 fw-bold mb-3">Customer Details</h2>
                <p class="mb-1"><strong>Name:</strong> {{ $order->customer_name }}</p>
                <p class="mb-1"><strong>Email:</strong> {{ $order->email }}</p>
                <p class="mb-1"><strong>Phone:</strong> {{ $order->phone }}</p>
                <p class="mb-1"><strong>Address:</strong> {{ $order->address }}</p>
                <p class="mb-1"><strong>City:</strong> {{ $order->city }}</p>
                <p class="mb-0"><strong>Postal Code:</strong> {{ $order->postal_code }}</p>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3 mt-4">
    <div class="card-body">
        <h2 class="h6 fw-bold mb-3">Order Items</h2>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
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
@endsection
