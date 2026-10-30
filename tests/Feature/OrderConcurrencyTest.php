<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class OrderConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_second_order_cannot_purchase_already_reserved_stock(): void
    {
        $product = Product::factory()->create([
            'stock_on_hand' => 1,
            'price' => 100,
            'tax_percentage' => 18,
        ]);

        $service = app(OrderService::class);

        $firstOrder = $service->create([
            'customer' => [
                'name' => 'Customer One',
                'email' => 'one@example.com',
            ],
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $this->assertNotNull($firstOrder->id);

        try {
            $service->create([
                'customer' => [
                    'name' => 'Customer Two',
                    'email' => 'two@example.com',
                ],
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 1,
                    ],
                ],
            ]);

            $this->fail('The second order should not succeed.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('items', $exception->errors());
        }

        $this->assertSame(
            0,
            (int) $product->fresh()->stock_on_hand
        );

        $this->assertDatabaseCount('orders', 1);
    }
}
