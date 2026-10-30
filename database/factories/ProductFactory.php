<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Colgate Toothpaste',
                'Parle-G Biscuit',
                'Milk 1L',
                'Eggs (12)',
                'Bath Soap',
            ]),
            'code' => fake()->unique()->bothify('PRD-####??'),
            'price' => fake()->randomFloat(2, 10, 500),
            'tax_percentage' => fake()->randomElement([0, 5, 12, 18]),
            'stock_on_hand' => fake()->numberBetween(0, 100),
        ];
    }
}
