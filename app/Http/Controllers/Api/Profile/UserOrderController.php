<?php

namespace App\Http\Controllers\Api\Profile;

use App\Models\Order;

class UserOrderController
{
    public  function  index()
    {
        $orders = Order::where('user_id',auth()->user()->id)->paginate();
        $orders->getCollection()->transform(function($order){
           return [
                'id' => $order->id,
                'restaurant_name' => $order->restaurant->name,
                'image' => $order->restaurant->image,
                'date' => $order->created_at,
                'payment_method' => $order->payment_method,
                'sending_method' => $order->sending_method,
                'payment_status' => $order->payment_status,
                'address' => $order->adress->address,
               'total_amount' => $order->total_amount,
           ];
        });
        return api_response($orders);

    }
    public function show($id)
    {
        $order = Order::find($id);
        $o = [
            'id' => $order->id,
            'restaurant_name' => $order->restaurant->name,
            'image' => $order->restaurant->image,
            'date' => $order->created_at,
            'price' => [
                'total_amount_with_out_discount' => $order->total_amount,
                'send_price' => $order->send_price,
                'total_price' => $order->total_price,
                'discount' => 0,
            ],
            'address' => $order->adress->address,
            'payment_method' => $order->payment_method,
            'sending_method' => $order->sending_method,
            'payment_status' => $order->pay_status_fa,
            'notes' => $order->notes,
            'items' => $order->items->map(function($item){
                return [

                    'id' => $item->id,
                    'name' => $item->option?->food?->name,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'dish_quantity' => $item->dish_quantity,
                ];
            }),
        ];
        return api_response($o);
    }

}

