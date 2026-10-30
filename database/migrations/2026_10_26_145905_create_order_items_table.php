<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            $table->unsignedInteger('quantity');

            // Snapshot of product details at purchase time.
            $table->decimal('unit_price', 12, 2);
            $table->decimal('tax_percentage', 5, 2)->default(0);

            $table->decimal('line_subtotal', 12, 2);
            $table->decimal('line_tax', 12, 2)->default(0);
            $table->decimal('line_total', 12, 2);

            $table->timestamps();

            $table->index(['product_id', 'order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};