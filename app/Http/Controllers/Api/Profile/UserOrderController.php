<?php

namespace App\Http\Controllers\Api\Profile;

use App\Models\Order;
use Morilog\Jalali\Jalalian;

class UserOrderController
{
    public  function  index()
    {
        $orders = Order::where('user_id',auth()->user()->id)->where('payment_status','paid')->orWhere('payment_status','cash')->latest()->paginate(10);
        $orders->getCollection()->transform(function($order){
           return [
                'id' => $order->id,
                'restaurant_name' => $order->restaurant->name,
                'image' => $order->restaurant->image,
                 'date' => Jalalian::fromCarbon(
                   $order->created_at->copy()->timezone('Asia/Tehran')
                 )->format('Y/m/d , H:i'),
                'payment_method' => $order->payment_method,
                'payment_status_en' => $order->payment_status,
                'sending_method' => $order->sending_method,
                'payment_status' => $order->status_user_fa,
                'address' => $order->adress->address,
                'total_amount' =>(int) $order->total_amount,
           ];
        });
        return api_response($orders);

    }
    public function show($id)
    {
        $order = Order::find($id);
        if(!$order){
            return api_response([],'order not found',404);
        }
        $o = [
            'id' => $order->id,
            'restaurant_name' => $order->restaurant->name,
            'image' => $order->restaurant->image,
            'date' => $order->created_at,
            'price' => [
                'total_amount_with_out_discount' => (int)$order->total_amount,
                    'send_price' => (int)$order->send_price,
                'total_price' => (int)$order->total_price,
                'discount' => 0,
            ],
            'address' => $order->adress->address,
            'payment_method' => $order->payment_method,
            'sending_method' => $order->sending_method,
            'payment_status_en' => $order->payment_status,
            'payment_status' => $order->status_user_fa,
            'notes' => $order->notes,
            'items' => $order->items->map(function($item){
                return [

                    'id' => $item->id,
                    'name' => $item->option?->food?->name . '  '. $item->option?->name,
                    'price' => (int)$item->price,
                    'quantity' => (int)$item->quantity,
                    'dish_quantity' =>(int)$item->dish_quantity,
                ];
            }),
        ];
        return api_response($o);
    }

}

