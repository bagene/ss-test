<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Service\OrderService;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class OrderServiceTest extends TestCase
{
    private OrderService $service;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var OrderService $service */
        $service = $this->app->make(OrderService::class);
        $this->service = $service;
    }

    #[DataProvider('provideData')]
    public function testCreate(array $items, int $expectedTotal, int $mainCurrency): void
    {
        $order = $this->service->create([
            'reference' => 'order-1',
            'items' => $items,
            'currency' => $mainCurrency,
        ]);

        $this->assertNotNull($order);
        $this->assertEquals($expectedTotal, $order->total);
        $this->assertEquals($mainCurrency, $order->currency);
    }

    public static function provideData(): array
    {
        return [
            'singleProduct' => [
                [
                    [
                        'product_name' => 'Product 1',
                        'quantity' => 1,
                        'price' => 100,
                        'currency' => 45,
                    ],
                ],
                10000,
                45,
            ],
            'multipleProducts' => [
                [
                    [
                        'product_name' => 'Product 1',
                        'quantity' => 1,
                        'price' => 100,
                        'currency' => 45,
                    ],
                    [
                        'product_name' => 'Product 2',
                        'quantity' => 2,
                        'price' => 50,
                        'currency' => 45,
                    ],
                ],
                20000,
                45,
            ],
            'multipleProductsDifferentCurrencies' => [
                [
                    [
                        'product_name' => 'Product 1',
                        'quantity' => 1,
                        'price' => 100,
                        'currency' => 45,
                    ],
                    [
                        'product_name' => 'Product 2',
                        'quantity' => 2,
                        'price' => 50,
                        'currency' => 4,
                    ],
                ],
                19208,
                45,
            ],
            'withFixedDiscount' => [
                [
                    [
                        'product_name' => 'Product 1',
                        'quantity' => 1,
                        'price' => 100,
                        'currency' => 45,
                        'discount' => 10,
                    ],
                    [
                        'product_name' => 'Product 2',
                        'quantity' => 2,
                        'price' => 50,
                        'currency' => 45,
                    ],
                ],
                19000,
                45,
            ],
            'withPercentageDiscount' => [
                [
                    [
                        'product_name' => 'Product 1',
                        'quantity' => 1,
                        'price' => 200,
                        'currency' => 45,
                        'discount' => 10,
                        'discount_type' => 'percentage',
                    ],
                    [
                        'product_name' => 'Product 2',
                        'quantity' => 2,
                        'price' => 50,
                        'currency' => 45,
                    ],
                ],
                28000,
                45,
            ],
        ];
    }
}
