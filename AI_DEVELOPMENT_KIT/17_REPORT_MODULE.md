# 17 Module Specification: Reports & Analytics Engine

## 1. Status & Overview
- **Status**: Remaining (See [PROJECT_MEMORY.md](file:///c:/shop_online/ecommerce/PROJECT_MEMORY.md)).
- **Scope**: Revenue analytics charts, top products ranking, low stock alerts, streamed CSV/Excel data export.

---

## 2. Architecture & Service Contract (`ReportService`)

```php
namespace App\Services;

use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Carbon;

final class ReportService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly ProductRepositoryInterface $productRepository
    ) {}

    public function getRevenueReport(Carbon $startDate, Carbon $endDate): array
    {
        return $this->orderRepository->getRevenueBetween($startDate, $endDate);
    }

    public function getTopSellingProducts(int $limit = 10): array
    {
        return $this->productRepository->getTopSelling($limit);
    }
}
```

---

## 3. Streamed CSV Export Protocol
Export large datasets efficiently using Symfony StreamedResponse without exceeding PHP memory limits:

```php
use Symfony\Component\HttpFoundation\StreamedResponse;

public function exportOrdersCsv(): StreamedResponse
{
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="orders_export.csv"',
    ];

    return response()->stream(function () {
        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['Order Number', 'Customer', 'Total', 'Status', 'Date']);

        Order::with('user')->lazy()->each(function ($order) use ($handle) {
            fputcsv($handle, [
                $order->order_number,
                $order->user->name,
                $order->total_amount,
                $order->status,
                $order->created_at->toDateTimeString(),
            ]);
        });

        fclose($handle);
    }, 200, $headers);
}
```
