<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\View\View;

final class DashboardController extends Controller
{
    public function __construct(private readonly OrderService $orderService)
    {
    }

    public function index(): View
    {
        $stats = $this->orderService->getDashboardStats();
        $recentOrders = $this->orderService->getRecentOrders();

        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }
}
