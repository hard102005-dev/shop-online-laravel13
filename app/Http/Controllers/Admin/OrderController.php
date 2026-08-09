<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Order::class);

        $orders = $this->orderService->getOrdersForAdmin($request->only(['search', 'status', 'payment_status']));

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        $order->load(['user', 'items.product']);

        return view('admin.orders.show', compact('order'));
    }

    public function update(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

        $this->orderService->updateOrder($order, $request->validated());

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order updated successfully.');
    }
}
