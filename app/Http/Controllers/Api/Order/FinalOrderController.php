<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\DiscountCode;
use App\Models\FoodOption;
use App\Models\Order;
use App\Models\Restaurant;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinalOrderController extends Controller
{


    public function store(Request $request)
    {
        $request->validate([
            'restaurant_discount' => 'nullable',
            'discount_code' => 'nullable|string',
            'phone' => 'nullable|string',
            'mobile' => 'required|string',
            'gateway' => 'nullable|string',
            'notes' => 'nullable|string',
            'time' => 'required|date',
            'is_wallet' => 'required',
            'payment_method' => 'required|string',
            'sending_method' => 'required|string',
            'restaurantId' => 'required|exists:restaurants,id',
            'address_id' => 'required|exists:addresses,id',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:food,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);
        $rest = Restaurant::find($request->restaurantId);
        $user = auth()->user();
        $address= Address::find($request->address_id);
        $order = Order::create([
            'address_id' => $request->address_id,
            'restaurant_id' => $request->restaurantId,
            'user_id' => $user->id,
            'payment_status' => 'pending',
            'payment_method' => $request->payment_method,
            'gateway' => $request->gateway,
            'discount_code' => $request->discount_code,
            'restaurant_discount' => $request->restaurant_discount,
            'phone' => $request->phone,
            'mobile' => $request->mobile,
            'time' => $request->time,
            'send_price' => $request->send_price,
            'sending_method' => $request->sending_method,
            'notes' => $request->notes,
        ]);

        $foodIds = collect($request->items)->pluck('id')->toArray();
        $foods = FoodOption::whereIn('id', $foodIds)->get(['id', 'price']);
        $totalWithoutDiscount = 0;
        foreach ($request->items as $item) {
            $food = $foods->firstWhere('id', $item['id']);
            if (!$food) continue;

            $lineTotal = $food->price_discount * $item['quantity'];
            $totalWithoutDiscount += $lineTotal;

            $order->items()->create([
                'food_option_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $food->price,
                'dish_quantity' => $item['dish_quantity'] ?? null,
            ]);
        }
        $discount = 0;
        if ($request->sending_method == 'pike')
        {
            $distance = distanceKm($rest->latitude, $rest->longitude, $address->latitude, $address->longitude);
            $send_price = $rest->send_price * $distance;
            $totalWithoutDiscount += $send_price;

        }
        // اعمال تخفیف رستوران
        $total = $totalWithoutDiscount;
        if ($request->restaurant_discount == true)
        {
            $rest_discount = ($rest->discount * $totalWithoutDiscount)/100;
            $total = $totalWithoutDiscount - $rest_discount;
        }
//        elseif ($request->discount_code == true)
//        {
//            //
//        }


        if ($request->is_wallet == true)
        {
            $balance = $user->wallet->balance;
            $new_total = $balance - $total;
            if ($new_total < 0)
            {
                //
            }
            else{
                //
            }

        }
        if ($request->is_wallet == false)
        {
            //
        }


        return api_response([
            'message' => 'سفارش با موفقیت ثبت شد.',
            'order_id' => $order->id,

        ]);
    }

    public function send_price(Request $request)
    {
        $address= Address::find($request->address_id);
        $rest= Restaurant::find($request->restaurant_id);
        $distance = distanceKm($rest->latitude, $rest->longitude, $address->latitude, $address->longitude);
        $send_price = $rest->send_price * $distance;
        return api_response((int)$send_price);
    }
    public function check_discount(Request $request)
    {
        $code = $request->input('code');
        $restaurant = $request->input('rest');
        $price = $request->input('price');
        $user = auth()->user();
        $discount = DiscountCode::where('name', $code)->first();
        if (!$discount)
        {
            return api_response([],'کد تخفیف اشتباه است', 400);
        }
        if ($discount->valid_until < Carbon::now())
        {
            return api_response([],'کد منقضی شده', 400);
        }
        if($discount->restaurant_id != null && $discount->restaurant_id != $restaurant)
        {
            return api_response([],'این کد برای این مجموعه قابل استفاده نیست', 400 );
        }
        if ($discount->max_discount < $price)
        {
            return api_response([],'حداکثر قیمت برای این کد بزرگ تر از حد مجاز است' , 400 );
        }
        if ($discount->one_time_use == 1)
        {
            $order = Order::where('user_id' , $user->id)->where('payment_status' , 'paid')->where('discount_code' , $discount->name)->exists();
            if ($order)
            {
                return api_response([], 'از این کد تخفیف قبلا استفاده کردید' ,400);
            }
        }
        return api_response([
            'code' =>$discount->name,
            'percentage' => (int)$discount->percentage,
        ] , 'تخفیف با موفقیت اعمال شد');

    }
}
