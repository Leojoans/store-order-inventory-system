
<?php

use App\Http\Controllers\Api\CustomerOrderHistoryController;
use App\Http\Controllers\Api\LowStockController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\StoreBillingController;
use Illuminate\Support\Facades\Route;

Route::post('/orders', [OrderController::class, 'store']);

Route::get(
    '/customers/orders',
    [CustomerOrderHistoryController::class, 'index']
);

Route::get(
    '/products/low-stock',
    [LowStockController::class, 'index']
);
Route::get('/billing', [StoreBillingController::class, 'index']);