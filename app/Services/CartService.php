<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Session;

final class CartService
{
    private const SESSION_KEY = 'cart';

    public function __construct(private readonly PriceCalculationService $priceCalculationService)
    {
    }

    public function getCart(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    public function getSummary(): array
    {
        $normalizedItems = [];
        $cart = $this->getCart();

        if ($cart !== []) {
            $productIds = array_values(array_unique(array_map(static fn (string|int $productId): int => (int) $productId, array_keys($cart))));
            $products = Product::query()
                ->withTrashed()
                ->whereIn('id', $productIds)
                ->get()
                ->keyBy('id');

            foreach ($cart as $productId => $item) {
                $quantity = max(0, (int) ($item['quantity'] ?? 0));

                if ($quantity <= 0) {
                    continue;
                }

                $product = $products->get((int) $productId);

                if ($product === null || $product->trashed() || ! $product->is_active) {
                    continue;
                }

                $availableQuantity = min($quantity, max(0, $product->stock));

                if ($availableQuantity <= 0) {
                    continue;
                }

                $normalizedItems[$product->id] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => (float) $product->effective_price,
                    'image_path' => $product->image_path,
                    'stock' => $product->stock,
                    'quantity' => $availableQuantity,
                ];
            }
        }

        Session::put(self::SESSION_KEY, $normalizedItems);

        $totals = $this->priceCalculationService->calculateTotals($normalizedItems);

        return array_merge($totals, [
            'items' => $normalizedItems,
        ]);
    }

    public function addItem(?Product $product, int $quantity = 1): array
    {
        $cart = $this->getCart();

        if ($product === null) {
            throw new \RuntimeException('The selected product could not be found.');
        }

        if ($quantity < 1) {
            throw new \RuntimeException('Quantity must be at least 1.');
        }

        if ($product->trashed()) {
            throw new \RuntimeException('This product is no longer available.');
        }

        if (! $product->is_active) {
            throw new \RuntimeException('This product is currently unavailable.');
        }

        $existingQuantity = (int) ($cart[$product->id]['quantity'] ?? 0);
        $newQuantity = $existingQuantity + $quantity;

        if ($newQuantity > $product->stock) {
            throw new \RuntimeException('The requested quantity exceeds the available stock.');
        }

        $cart[$product->id] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => (float) $product->effective_price,
            'image_path' => $product->image_path,
            'stock' => $product->stock,
            'quantity' => $newQuantity,
        ];

        Session::put(self::SESSION_KEY, $cart);

        return $this->getSummary();
    }

    public function updateItem(int $productId, int $quantity): array
    {
        $cart = $this->getCart();

        if (! isset($cart[$productId])) {
            return $this->getSummary();
        }

        if ($quantity < 1) {
            unset($cart[$productId]);
            Session::put(self::SESSION_KEY, $cart);

            return $this->getSummary();
        }

        $product = Product::query()->withTrashed()->find($productId);

        if ($product === null || $product->trashed() || ! $product->is_active) {
            unset($cart[$productId]);
            Session::put(self::SESSION_KEY, $cart);

            return $this->getSummary();
        }

        if ($quantity > $product->stock) {
            throw new \RuntimeException('The requested quantity exceeds the available stock.');
        }

        $cart[$productId]['quantity'] = $quantity;
        $cart[$productId]['price'] = (float) $product->effective_price;
        $cart[$productId]['stock'] = $product->stock;

        Session::put(self::SESSION_KEY, $cart);

        return $this->getSummary();
    }

    public function removeItem(int $productId): array
    {
        $cart = $this->getCart();
        unset($cart[$productId]);
        Session::put(self::SESSION_KEY, $cart);

        return $this->getSummary();
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public function count(): int
    {
        return array_sum(array_column($this->getCart(), 'quantity'));
    }
}
