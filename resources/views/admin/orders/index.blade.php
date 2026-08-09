@extends('layouts.admin')

@section('title', 'Admin Orders')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Order Management</h1>
        <p class="text-muted mb-0">Monitor and update customer orders.</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Order number, customer, email">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    @foreach (App\Models\Order::STATUSES as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Payment</label>
                <select name="payment_status" class="form-select">
                    <option value="">All</option>
                    @foreach (App\Models\Order::PAYMENT_STATUSES as $status)
                        <option value="{{ $status }}" {{ request('payment_status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $order->order_number }}</div>
                                <div class="text-muted small">{{ $order->email }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $order->customer_name }}</div>
                                <div class="text-muted small">{{ $order->user->name ?? 'Guest' }}</div>
                            </td>
                            <td><span class="badge bg-primary-subtle text-primary">{{ ucfirst($order->status) }}</span></td>
                            <td><span class="badge bg-secondary-subtle text-secondary">{{ ucfirst($order->payment_status) }}</span></td>
                            <td>${{ number_format((float) $order->total, 2) }}</td>
                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No orders matched the current filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $orders->appends(request()->query())->links() }}
</div>
@endsection
