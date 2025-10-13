<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FinalOrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'restaurant_discount' => 'nullable|numeric',
            'discount_code' => 'nullable|string',
            'phone' => 'nullable|string',
            'mobile' => 'required|string',
            'notes' => 'nullable|string',
            'time' => 'required|date',
            'payment_method' => 'required|string',
            'sending_method' => 'required|string',
            'restaurant_id' => 'required|exists:restaurants,id',
            'address_id' => 'required|exists:addresses,id',
            'items' => 'required|array|min:1',
            'items.*.food_id' => 'required|exists:food,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);


    }
}
