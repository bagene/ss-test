<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class OrderControllerTest extends TestCase
{
    private const URL = '/api/orders';
    private const POST_METHOD = 'POST';

    public function testCreate(): void
    {
        $response = $this->json(self::POST_METHOD, self::URL, [
            'reference' => 'order-1',
            'items' => [
                [
                    'product_name' => 'Product 1',
                    'quantity' => 1,
                    'price' => 100,
                    'currency' => 45,
                ],
            ],
            'currency' => 45,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'reference',
                'items' => [
                    '*' => [
                        'product_name',
                        'quantity',
                        'price',
                        'currency',
                    ],
                ],
                'total',
                'currency',
            ],
        ]);
    }

    #[DataProvider('provideInvalidData')]
    public function testCreateInvalidData(array $data, array $expectedErrors): void
    {
        $response = $this->json(self::POST_METHOD, self::URL, $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($expectedErrors);
    }

    public static function provideInvalidData(): array
    {
        return [
            'empty' => [
                [],
                [
                    'reference' => 'The reference field is required.',
                    'items' => 'The items field is required.',
                    'currency' => 'The currency field is required.',
                ],
            ],
            'invalid_data' => [
                [
                    'reference' => 1,
                    'items' => 'invalid',
                    'currency' => 'invalid',
                ],
                [
                    'reference' => 'The reference field must be a string.',
                    'items' => 'The items field must be an array.',
                    'currency' => 'The currency field must be an integer.',
                ],
            ],
            'invalid_items' => [
                [
                    'reference' => 'order-1',
                    'items' => [
                        [
                            'product_name' => 1,
                            'quantity' => 1,
                            'price' => 100,
                            'currency' => 'EUR',
                        ],
                    ],
                    'currency' => 45,
                ],
                [
                    'items.0.product_name' => 'The items.0.product_name field must be a string.',
                    'items.0.currency' => 'The items.0.currency field must be an integer.',
                ],
            ]
        ];
    }
}
