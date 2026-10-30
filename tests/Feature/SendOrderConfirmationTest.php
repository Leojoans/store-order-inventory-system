<?php

namespace Tests\Feature;

use App\Jobs\SendOrderConfirmation;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class SendOrderConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_logs_order_confirmation_details(): void
    {
        Log::spy();

        $customer = Customer::factory()->create([
            'name' => 'Arun Kumar',
            'email' => 'arun@example.com',
        ]);

        $order = $customer->orders()->create([
            'subtotal' => 100.00,
            'tax_amount' => 18.00,
            'grand_total' => 118.00,
        ]);

        $job = new SendOrderConfirmation($order->id);

        $job->handle();

        Log::shouldHaveReceived('info')
            ->once()
            ->with(
                'Order confirmation email simulated.',
                \Mockery::on(function ($context) use ($order) {
                    return $context['order_id'] === $order->id
                        && $context['customer_email'] === 'arun@example.com'
                        && $context['grand_total'] == 118.00;
                })
            );
    }

    public function test_job_handles_missing_order_without_sending_confirmation(): void
    {
        Log::spy();

        $job = new SendOrderConfirmation(999999);

        $job->handle();

        Log::shouldHaveReceived('warning')
            ->once()
            ->with(
                'Order confirmation skipped: order not found.',
                ['order_id' => 999999]
            );
    }
}
