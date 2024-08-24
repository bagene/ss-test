<?php

declare(strict_types=1);

namespace App\Service;

use App\Enums\Currency;
use App\Models\Order;
use App\Money\Money;

final class OrderService
{
    /**
     * @param array{
     *     reference: string,
     *     items: array{
     *       product_name: string,
     *       quantity: int,
     *       price: float,
     *       currency: int,
     *       discount?: float,
     *       discount_type?: string
     *     }[],
     *     discount?: float,
     *     discount_type?: string,
     *     currency: int
     * } $data
     */
    public function create(array $data): Order
    {
        $mainCurrency = Currency::from($data['currency']);
        $items = $data['items'];

        $total = $this->calculateItemsTotal($items, $mainCurrency)
            ->applyDiscount($data['discount'] ?? 0, $data['discount_type'] ?? Money::FIXED_DISCOUNT);

        $order = new Order();

        $order->status = Order::STATUS_PENDING;
        $order->reference = $data['reference'];
        $order->total = $total->getAmount();
        $order->currency = $mainCurrency->value;
        $order->items = $items;
        $order->save();

        return $order;
    }

    /**
     * @param array{
     *        product_name: string,
     *        quantity: int,
     *        price: float,
     *        currency: int,
     *        discount?: float,
     *        discount_type?: string
     *      }[] $items
     */
    private function calculateItemsTotal(array $items, Currency $mainCurrency): Money
    {
        $total = Money::fromInt(0, $mainCurrency);
        foreach ($items as $item) {
            $itemTotal = Money::fromFloat($item['price'], Currency::from($item['currency']))
                ->convertTo($mainCurrency)
                ->multiply($item['quantity'])
                ->applyDiscount($item['discount'] ?? 0, $item['discount_type'] ?? Money::FIXED_DISCOUNT);

            $total = $total->add($itemTotal);
        }

        return $total;
    }
}
