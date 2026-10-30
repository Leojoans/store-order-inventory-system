<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class StoreBillingController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'code',
                'price',
                'tax_percentage',
                'stock_on_hand',
            ]);

        $threshold = (int) config(
            'inventory.low_stock_threshold',
            10
        );

        $lowStockProducts = Product::query()
            ->where('stock_on_hand', '<', $threshold)
            ->orderBy('stock_on_hand')
            ->get([
                'id',
                'name',
                'stock_on_hand',
            ]);

        return view('store-billing', compact(
            'products',
            'lowStockProducts',
            'threshold'
        ));
    }
}
