<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Colgate Toothpaste',
                'code' => 'PRD-0001',
                'price' => 50.00,
                'tax_percentage' => 18.00,
                'stock_on_hand' => 20,
            ],
            [
                'name' => 'Parle-G Biscuit',
                'code' => 'PRD-0002',
                'price' => 10.00,
                'tax_percentage' => 5.00,
                'stock_on_hand' => 30,
            ],
            [
                'name' => 'Milk 1L',
                'code' => 'PRD-0003',
                'price' => 30.00,
                'tax_percentage' => 5.00,
                'stock_on_hand' => 9,
            ],
            [
                'name' => 'Eggs (12)',
                'code' => 'PRD-0004',
                'price' => 72.00,
                'tax_percentage' => 0.00,
                'stock_on_hand' => 2,
            ],
            [
                'name' => 'Bath Soap',
                'code' => 'PRD-0005',
                'price' => 40.00,
                'tax_percentage' => 18.00,
                'stock_on_hand' => 15,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['code' => $product['code']],
                $product
            );
        }

        Product::factory()->count(5)->create();
    }
}
