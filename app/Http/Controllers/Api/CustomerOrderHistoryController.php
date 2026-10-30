<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CustomerOrderHistoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $customer = Customer::where(
            'email',
            $validated['email']
        )->first();

        if (! $customer) {
            return response()->json([
                'message' => 'Customer not found.',
                'data' => [],
            ], 404);
        }

        $orders = $customer->orders()
            ->with('items.product')
            ->latest()
            ->get();

        return response()->json([
            'customer' => $customer,
            'data' => $orders,
        ]);
    }
}
