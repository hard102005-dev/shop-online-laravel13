<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class OrderService
{
    public function __construct(private readonly PriceCalculationService $priceCalculationService)
    {
    }

    public function createOrder(User $user, array $checkoutData, array $items): Order
    {
        return DB::transaction(function () use ($user, $checkoutData, $items): Order {
            $productIds = array_values(array_unique(array_filter(array_map(static fn (array $item): int => (int) ($item['product_id'] ?? 0), $items))));

            if ($productIds === []) {
                throw new \RuntimeException('Your cart is empty.');
            }

            $products = Product::query()
                ->withTrashed()
                ->whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($items as $item) {
                $productId = (int) ($item['product_id'] ?? 0);
                $quantity = max(0, (int) ($item['quantity'] ?? 0));

                if ($productId <= 0 || $quantity <= 0) {
                    throw new \RuntimeException('One or more products are invalid.');
                }

                $product = $products->get($productId);

                if ($product === null || $product->trashed() || ! $product->is_active) {
                    throw new \RuntimeException('One or more products are no longer available.');
                }

                if ($product->stock < $quantity) {
                    throw new \RuntimeException('One or more products do not have enough stock.');
                }
            }

            $lineItems = [];
            foreach ($items as $item) {
                $productId = (int) ($item['product_id'] ?? 0);
                $quantity = max(0, (int) ($item['quantity'] ?? 0));
                $product = $products->get($productId);

                if ($product === null) {
                    throw new \RuntimeException('One or more products could not be resolved.');
                }

                $lineItems[] = [
                    'quantity' => $quantity,
                    'unit_price' => (float) $product->effective_price,
                ];
            }

            $totals = $this->priceCalculationService->calculateOrderTotals($lineItems);

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => $this->generateOrderNumber(),
                'customer_name' => $checkoutData['customer_name'],
                'email' => $checkoutData['email'],
                'phone' => $checkoutData['phone'],
                'address' => $checkoutData['address'],
                'city' => $checkoutData['city'],
                'postal_code' => $checkoutData['postal_code'],
                'notes' => $checkoutData['notes'] ?? null,
                'subtotal' => round((float) $totals['subtotal'], 2),
                'shipping_fee' => round((float) $totals['shipping_fee'], 2),
                'total' => round((float) $totals['total'], 2),
            ]);

            foreach ($items as $item) {
                $productId = (int) ($item['product_id'] ?? 0);
                $quantity = max(0, (int) ($item['quantity'] ?? 0));
                $product = $products->get($productId);

                if ($product === null) {
                    throw new \RuntimeException('One or more products could not be resolved.');
                }

                $product->decrement('stock', $quantity);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'quantity' => $quantity,
                    'unit_price' => round((float) $product->effective_price, 2),
                    'total_price' => $this->priceCalculationService->calculateLineTotal((float) $product->effective_price, $quantity),
                ]);
            }

            return $order;
        });
    }

    public function getOrdersForUser(User $user): Collection
    {
        return Order::query()
            ->where('user_id', $user->id)
            ->with('items')
            ->latest()
            ->get();
    }

    public function getOrderForUser(Order $order, User $user): Order
    {
        return Order::query()
            ->where('id', $order->id)
            ->where('user_id', $user->id)
            ->with('items.product')
            ->firstOrFail();
    }

    public function getDashboardStats(): array
    {
        return [
            'orders' => Order::query()->count(),
            'pending_orders' => Order::query()->where('status', 'pending')->count(),
            'processing_orders' => Order::query()->where('status', 'processing')->count(),
            'completed_orders' => Order::query()->where('status', 'completed')->count(),
            'cancelled_orders' => Order::query()->where('status', 'cancelled')->count(),
            'total_sales' => Order::query()->where('status', 'completed')->sum('total'),
            'revenue' => Order::query()->where('status', 'completed')->sum('total'),
        ];
    }

    public function getRecentOrders(int $limit = 5): Collection
    {
        return Order::query()
            ->with('user')
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getOrdersForAdmin(array $filters = []): LengthAwarePaginator
    {
        $query = Order::query()->with('user');

        if (! empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $query->where(function ($query) use ($search): void {
                $query->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        return $query->latest()->paginate(15);
    }

    public function updateOrder(Order $order, array $data): Order
    {
        if (isset($data['status'])) {
            $status = (string) $data['status'];

            if (! in_array($status, Order::STATUSES, true)) {
                throw new \RuntimeException('The selected order status is invalid.');
            }

            if (! $order->canTransitionTo($status)) {
                throw new \RuntimeException('The selected order status transition is not allowed.');
            }
        }

        if (isset($data['payment_status'])) {
            $paymentStatus = (string) $data['payment_status'];

            if (! in_array($paymentStatus, Order::PAYMENT_STATUSES, true)) {
                throw new \RuntimeException('The selected payment status is invalid.');
            }
        }

        $order->fill($data);
        $order->save();

        return $order->fresh();
    }

    private function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');

        do {
            $number = sprintf('ORD-%s-%06d', $date, random_int(0, 999999));
        } while (Order::query()->where('order_number', $number)->exists());

        return $number;
    }
}
