<?php

namespace App\Services;

use App\Models\PurchaseItem;

class PurchaseAmountService
{
    public function calculate(PurchaseItem $purchaseItem): float
    {
        $unitPrice = (float) ($purchaseItem->unit_price ?? 0);
        $quantity = (int) $purchaseItem->quantity;
        $shippingFee = (float) ($purchaseItem->shipping_fee ?? 0);
        $discount = (float) ($purchaseItem->discount ?? 0);

        return ($unitPrice * $quantity)
            + $shippingFee
            - $discount;
    }
    public function round(float $amount): int
    {
        $floor = floor($amount);
        $decimal = $amount - $floor;

        return $decimal >= 0.45
            ? $floor + 1
            : $floor;
    }
}