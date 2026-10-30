<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class LowStockController extends Controller
{
    public function index(): JsonResponse
    {
        $threshold = (int) config('inventory.low_stock_threshold', 10);

        $products = Product::query()
            ->where('stock_on_hand', '<', $threshold)
            ->orderBy('stock_on_hand')
            ->get();

        return response()->json([
            'threshold' => $threshold,
            'count' => $products->count(),
            'data' => $products,
        ]);
    }
}
