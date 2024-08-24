<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Service\OrderService;
use Illuminate\Http\JsonResponse;

final class OrderController extends Controller
{
    public function create(OrderRequest $request, OrderService $service): JsonResponse
    {
        /**
         * @var array{
         *     reference: string,
         *     items: array{
         *       product_name: string,
         *       quantity: int,
         *       price: float,
         *       currency: int,
         *       discount?: float
         *     }[],
         *     discount?: float,
         *     currency: int
         * } $data
         */
        $data = $request->validated();
        $order = $service->create($data);

        return response()->json([
            'message' => 'Order created',
            'data' => $order,
        ]);
    }
}
