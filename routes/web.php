<?php

use App\Http\Controllers\StoreBillingController;
use Illuminate\Support\Facades\Route;

// Homepage redirects to the billing screen.
Route::redirect('/', '/billing');

// Store billing page.
Route::get('/billing', [
    StoreBillingController::class,
    'index',
])->name('billing.index');
