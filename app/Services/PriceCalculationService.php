<?php

declare(strict_types=1);

namespace App\Services;

final class PriceCalculationService
{
    public function calculateTotals(array $items): array
    {
        $itemCount = 0;
        $subtotal = 0.0;

        foreach ($items as $item) {
            $quantity = max(0, (int) ($item['quantity'] ?? 0));
            $price = (float) ($item['price'] ?? $item['unit_price'] ?? 0);

            $itemCount += $quantity;
            $subtotal += $this->calculateLineTotal($price, $quantity);
        }

        return $this->buildTotals($subtotal, $itemCount);
    }

    public function calculateOrderTotals(array $items): array
    {
        $itemCount = 0;
        $subtotal = 0.0;

        foreach ($items as $item) {
            $quantity = max(0, (int) ($item['quantity'] ?? 0));
            $unitPrice = (float) ($item['unit_price'] ?? $item['price'] ?? 0);

            $itemCount += $quantity;
            $subtotal += $this->calculateLineTotal($unitPrice, $quantity);
        }

        return $this->buildTotals($subtotal, $itemCount);
    }

    public function calculateLineTotal(float $unitPrice, int $quantity): float
    {
        return round($unitPrice * $quantity, 2);
    }

    public function calculateShippingFee(float $subtotal): float
    {
        return $subtotal >= 100 ? 0.0 : 5.0;
    }

    private function buildTotals(float $subtotal, int $itemCount): array
    {
        $shippingFee = $this->calculateShippingFee($subtotal);

        return [
            'item_count' => $itemCount,
            'subtotal' => round($subtotal, 2),
            'shipping_fee' => round($shippingFee, 2),
            'total' => round($subtotal + $shippingFee, 2),
        ];
    }
}
