<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function store(
        StoreOrderRequest $request,
        OrderService $orderService
    ): JsonResponse {
        $order = $orderService->create(
            $request->validated()
        );

        return response()->json([
            'message' => 'Order created successfully.',
            'data' => $order,
        ], 201);
    }
}
