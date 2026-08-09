# 11 Module Specification: Admin Dashboard & System Control

## 1. Status & Overview
- **Status**: Completed (See [PROJECT_MEMORY.md](file:///c:/shop_online/ecommerce/PROJECT_MEMORY.md)).
- **Scope**: Metric KPI summary cards, order activity feeds, user account controls.

---

## 2. Architecture & Service Contract (`AdminDashboardService`)

```php
namespace App\Services;

use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;

final class AdminDashboardService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function getDashboardMetrics(): array
    {
        return [
            'total_revenue' => $this->orderRepository->getTotalRevenue(),
            'monthly_revenue' => $this->orderRepository->getMonthlyRevenue(),
            'total_orders' => $this->orderRepository->count(),
            'pending_orders' => $this->orderRepository->countByStatus('pending'),
            'low_stock_count' => $this->productRepository->countLowStock(),
            'total_customers' => $this->userRepository->countCustomers(),
        ];
    }
}
```

---

## 3. UI Template Structure (Bootstrap 5 Admin Panel)
- Side navigation sidebar (`.nav .flex-column`) + top navbar + main container.
- Summary KPI Cards formatted using Bootstrap `.card .border-0 .shadow-sm` with custom icon badges.
