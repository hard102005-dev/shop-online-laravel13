<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService)
    {
    }

    public function index(Request $request): View
    {
        $orders = $this->orderService->getOrdersForUser($request->user());

        return view('orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order): View
    {
        $this->authorize('view', $order);
        $order = $this->orderService->getOrderForUser($order, $request->user());

        return view('orders.show', compact('order'));
    }
}
