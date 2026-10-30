<?php

namespace Tests\Feature;

use App\Jobs\SendOrderConfirmation;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CreateOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_create_an_order_and_stock_is_deducted(): void
    {
        Queue::fake();

        $product = Product::factory()->create([
            'price' => 50.00,
            'tax_percentage' => 18.00,
            'stock_on_hand' => 10,
        ]);

        $response = $this->postJson('/api/orders', [
            'customer' => [
                'name' => 'Arun Kumar',
                'email' => 'arun@example.com',
            ],
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.subtotal', '100.00')
            ->assertJsonPath('data.tax_amount', '18.00')
            ->assertJsonPath('data.grand_total', '118.00');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_on_hand' => 8,
        ]);

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_items', 1);

$orderId = $response->json('data.id');

    Queue::assertPushed(
        SendOrderConfirmation::class,
        fn ($job) => $job->orderId === $orderId
    );
    }

    public function test_insufficient_stock_does_not_create_an_order(): void
    {
        Queue::fake();

        $product = Product::factory()->create([
            'stock_on_hand' => 1,
        ]);

        $response = $this->postJson('/api/orders', [
            'customer' => [
                'name' => 'Priya Devi',
                'email' => 'priya@example.com',
            ],
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertUnprocessable();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_on_hand' => 1,
        ]);

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);

        Queue::assertNothingPushed();
    }
}
