@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0">Admin Dashboard</h1>
        <p class="text-muted mb-0">Overview of store inventory, orders, and system performance.</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-2">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-medium d-block mb-1">Total Orders</span>
                        <h3 class="fw-bold mb-0">{{ $stats['orders'] }}</h3>
                    </div>
                    <div class="bg-primary-subtle text-primary p-3 rounded-circle">
                        <i class="bi bi-receipt fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-2">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-medium d-block mb-1">Pending</span>
                        <h3 class="fw-bold mb-0">{{ $stats['pending_orders'] }}</h3>
                    </div>
                    <div class="bg-success-subtle text-success p-3 rounded-circle">
                        <i class="bi bi-clock-history fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-2">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-medium d-block mb-1">Processing</span>
                        <h3 class="fw-bold mb-0">{{ $stats['processing_orders'] }}</h3>
                    </div>
                    <div class="bg-warning-subtle text-warning p-3 rounded-circle">
                        <i class="bi bi-arrow-repeat fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-2">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-medium d-block mb-1">Completed</span>
                        <h3 class="fw-bold mb-0">{{ $stats['completed_orders'] }}</h3>
                    </div>
                    <div class="bg-info-subtle text-info p-3 rounded-circle">
                        <i class="bi bi-cart-check fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-2">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-medium d-block mb-1">Cancelled</span>
                        <h3 class="fw-bold mb-0">{{ $stats['cancelled_orders'] }}</h3>
                    </div>
                    <div class="bg-danger-subtle text-danger p-3 rounded-circle">
                        <i class="bi bi-x-circle fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-2">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-medium d-block mb-1">Total Sales</span>
                        <h3 class="fw-bold mb-0">${{ number_format((float) $stats['total_sales'], 2) }}</h3>
                    </div>
                    <div class="bg-warning-subtle text-warning p-3 rounded-circle">
                        <i class="bi bi-currency-dollar fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0">Recent Orders</h5>
            </div>
            <div class="card-body">
                @if ($recentOrders->isEmpty())
                    <p class="text-muted mb-0">No orders have been placed yet.</p>
                @else
                    <div class="list-group list-group-flush">
                        @foreach ($recentOrders as $order)
                            <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold">{{ $order->order_number }}</div>
                                    <div class="text-muted small">{{ $order->user->name ?? 'Guest' }} • {{ $order->created_at->format('M d, Y H:i') }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold">${{ number_format((float) $order->total, 2) }}</div>
                                    <div class="badge bg-primary-subtle text-primary">{{ ucfirst($order->status) }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0">Store Actions</h5>
            </div>
            <div class="card-body d-flex flex-column justify-content-between">
                <p class="text-muted">Manage your product catalog, categories, and review recent customer orders.</p>
                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-primary"><i class="bi bi-list me-1"></i>View Categories</a>
                    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Category</a>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-success"><i class="bi bi-box-seam me-1"></i>View Products</a>
                    <a href="{{ route('admin.products.create') }}" class="btn btn-success"><i class="bi bi-plus-lg me-1"></i>Add Product</a>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-dark"><i class="bi bi-receipt me-1"></i>Manage Orders</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
