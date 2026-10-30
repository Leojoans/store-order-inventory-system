<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::updateOrCreate(
            ['email' => 'arun@example.com'],
            ['name' => 'Arun Kumar']
        );

        Customer::updateOrCreate(
            ['email' => 'priya@example.com'],
            ['name' => 'Priya Devi']
        );

        Customer::factory()->count(8)->create();
    }
}
