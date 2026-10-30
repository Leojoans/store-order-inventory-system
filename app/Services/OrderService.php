<?php

namespace App\Services;

use App\Jobs\SendOrderConfirmation;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function create(array $data): Order
    {
        $order = DB::transaction(function () use ($data) {
            $requestedItems = collect($data['items']);

            $productIds = $requestedItems
                ->pluck('product_id')
                ->map(fn ($id) => (int) $id)
                ->sort()
                ->values();

            // Lock product rows in a consistent order.
            $products = Product::query()
                ->whereIn('id', $productIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // Recheck products after obtaining the locks.
            if ($products->count() !== $productIds->count()) {
                throw ValidationException::withMessages([
                    'items' => ['One or more products no longer exist.'],
                ]);
            }

            // Check every quantity before writing any order data.
            foreach ($requestedItems as $item) {
                $product = $products->get(
                    (int) $item['product_id']
                );

                if ((int) $item['quantity'] > $product->stock_on_hand) {
                    throw ValidationException::withMessages([
                        'items' => [
                            "Insufficient stock for {$product->name}. "
                            . "Available: {$product->stock_on_hand}.",
                        ],
                    ]);
                }
            }

            // Convert monetary amounts to integer cents for calculation.
            $subtotalCents = 0;
            $taxCents = 0;
            $lines = [];

            foreach ($requestedItems as $item) {
                $product = $products->get(
                    (int) $item['product_id']
                );

                $quantity = (int) $item['quantity'];

                $unitPriceCents = (int) round(
                    (float) $product->price * 100
                );

                $lineSubtotalCents = $unitPriceCents * $quantity;

                // 18.00% becomes 1800 basis points.
                $taxBasisPoints = (int) round(
                    (float) $product->tax_percentage * 100
                );

                // Round tax to the nearest cent, half up.
                $lineTaxCents = intdiv(
                    ($lineSubtotalCents * $taxBasisPoints) + 5000,
                    10000
                );

                $subtotalCents += $lineSubtotalCents;
                $taxCents += $lineTaxCents;

                $lines[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $unitPriceCents,
                    'tax_percentage' => $product->tax_percentage,
                    'line_subtotal' => $lineSubtotalCents,
                    'line_tax' => $lineTaxCents,
                    'line_total' => $lineSubtotalCents + $lineTaxCents,
                ];
            }

            $customer = Customer::firstOrCreate(
                ['email' => $data['customer']['email']],
                ['name' => $data['customer']['name']]
            );

            $order = $customer->orders()->create([
                'subtotal' => $this->money($subtotalCents),
                'tax_amount' => $this->money($taxCents),
                'grand_total' => $this->money(
                    $subtotalCents + $taxCents
                ),
            ]);

            foreach ($lines as $line) {
                $order->items()->create([
                    'product_id' => $line['product']->id,
                    'quantity' => $line['quantity'],
                    'unit_price' => $this->money(
                        $line['unit_price']
                    ),
                    'tax_percentage' => $line['tax_percentage'],
                    'line_subtotal' => $this->money(
                        $line['line_subtotal']
                    ),
                    'line_tax' => $this->money(
                        $line['line_tax']
                    ),
                    'line_total' => $this->money(
                        $line['line_total']
                    ),
                ]);

                $line['product']->decrement(
                    'stock_on_hand',
                    $line['quantity']
                );
            }

            return $order;
        }, 3);

        // The transaction has committed successfully before dispatch.
        SendOrderConfirmation::dispatch($order->id)->afterCommit();

        return $order->load([
            'customer',
            'items.product',
        ]);
    }

    private function money(int $cents): string
    {
        return number_format($cents / 100, 2, '.', '');
    }
}
