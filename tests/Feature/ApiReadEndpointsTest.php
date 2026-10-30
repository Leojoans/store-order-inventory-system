<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiReadEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_fetch_order_history_by_email(): void
    {
        $customer = Customer::factory()->create([
            'name' => 'Arun Kumar',
            'email' => 'arun@example.com',
        ]);

        $product = Product::factory()->create([
            'price' => 50.00,
            'tax_percentage' => 18.00,
            'stock_on_hand' => 8,
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'subtotal' => 100.00,
            'tax_amount' => 18.00,
            'grand_total' => 118.00,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 50.00,
            'tax_percentage' => 18.00,
            'line_subtotal' => 100.00,
            'line_tax' => 18.00,
            'line_total' => 118.00,
        ]);

        $response = $this->getJson(
            '/api/customers/orders?email=arun@example.com'
        );

        $response->assertOk()
            ->assertJsonPath('customer.email', 'arun@example.com')
            ->assertJsonPath('data.0.id', $order->id)
            ->assertJsonPath('data.0.grand_total', '118.00')
            ->assertJsonPath(
                'data.0.items.0.product.name',
                $product->name
            );
    }

    public function test_unknown_customer_receives_404(): void
    {
        $response = $this->getJson(
            '/api/customers/orders?email=unknown@example.com'
        );

        $response->assertNotFound()
            ->assertJsonPath('message', 'Customer not found.')
            ->assertJsonPath('data', []);
    }

    public function test_low_stock_endpoint_uses_configured_threshold(): void
    {
        config(['inventory.low_stock_threshold' => 10]);

        $lowStock = Product::factory()->create([
            'stock_on_hand' => 2,
        ]);

        Product::factory()->create([
            'stock_on_hand' => 10,
        ]);

        Product::factory()->create([
            'stock_on_hand' => 25,
        ]);

        $response = $this->getJson('/api/products/low-stock');

        $response->assertOk()
            ->assertJsonPath('threshold', 10)
            ->assertJsonPath('count', 1)
            ->assertJsonPath('data.0.id', $lowStock->id);
    }
}
