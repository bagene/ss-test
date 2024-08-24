<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Enums\Currency;
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
    public function testCreate(array $items, int $expectedTotal, string $mainCurrency): void
    {
        $order = $this->service->create([
            'reference' => 'order-1',
            'items' => $items,
            'currency' => $mainCurrency,
        ]);

        $this->assertNotNull($order);
        $this->assertEquals($expectedTotal, $order->total);
        $this->assertEquals(Currency::fromName($mainCurrency)->value, $order->currency);
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
                        'currency' => 'EUR',
                    ],
                ],
                10000,
                'EUR',
            ],
            'multipleProducts' => [
                [
                    [
                        'product_name' => 'Product 1',
                        'quantity' => 1,
                        'price' => 100,
                        'currency' => 'EUR',
                    ],
                    [
                        'product_name' => 'Product 2',
                        'quantity' => 2,
                        'price' => 50,
                        'currency' => 'EUR',
                    ],
                ],
                20000,
                'EUR',
            ],
            'multipleProductsDifferentCurrencies' => [
                [
                    [
                        'product_name' => 'Product 1',
                        'quantity' => 1,
                        'price' => 100,
                        'currency' => 'EUR',
                    ],
                    [
                        'product_name' => 'Product 2',
                        'quantity' => 2,
                        'price' => 50,
                        'currency' => 'AMD',
                    ],
                ],
                19208,
                'EUR',
            ],
            'withFixedDiscount' => [
                [
                    [
                        'product_name' => 'Product 1',
                        'quantity' => 1,
                        'price' => 100,
                        'currency' => 'EUR',
                        'discount' => 10,
                    ],
                    [
                        'product_name' => 'Product 2',
                        'quantity' => 2,
                        'price' => 50,
                        'currency' => 'EUR',
                    ],
                ],
                19000,
                'EUR',
            ],
            'withPercentageDiscount' => [
                [
                    [
                        'product_name' => 'Product 1',
                        'quantity' => 1,
                        'price' => 200,
                        'currency' => 'EUR',
                        'discount' => 10,
                        'discount_type' => 'percentage',
                    ],
                    [
                        'product_name' => 'Product 2',
                        'quantity' => 2,
                        'price' => 50,
                        'currency' => 'EUR',
                    ],
                ],
                28000,
                'EUR',
            ],
        ];
    }
}
