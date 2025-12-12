<?php

namespace App\Http\Controllers\Api\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RestReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $restaurant = Restaurant::where('user_id', $user->id)->first();
        $from = $request->input('from');
        $to = $request->input('to');
        $send = $request->input('tab');

        $orders = Order::whereRelation('restaurant', 'user_id', $user->id)->where('payment_status' , 'paid')->orWhere('payment_status' , 'cash')->where('restaurant_id', $restaurant->id);

        if ($from) {
            $orders->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $orders->whereDate('created_at', '<=', $to);
        }
        if ($send) {
            $orders->where('sending_method', $send);
        }

        $orders = $orders->with('items.option.food')->get();

        $foods = [];

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $food = $item->option->food ?? null;
                if (!$food) continue;

                if (!isset($foods[$food->id])) {
                    $foods[$food->id] = [
                        'food_id' => $food->id,
                        'food_name' => $food->name,
                        'total_orders' => 0,
                        'total_price' => 0,
                    ];
                }

                $foods[$food->id]['total_orders'] += $item->quantity ?? 1;
                $foods[$food->id]['total_price'] += ($item->price ?? $item->option->price) * ($item->quantity ?? 1);
            }
        }

        return api_response([
            'foods'=>array_values($foods),
            'count' => count($orders),
            'price' => $orders->sum('total_price'),

        ], 'گزارش سفارش غذاها با موفقیت دریافت شد');
    }



}
