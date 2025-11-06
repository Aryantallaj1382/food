<?php

namespace App\Http\Controllers\Api\Order;

use App\Helpers\ParsianPayment;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\DiscountCode;
use App\Models\FoodOption;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Restaurant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use IPPanel\Client;
use Morilog\Jalali\Jalalian;

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
            'time' => 'nullable',
            'is_wallet' => 'required',
            'payment_method' => 'required|string',
            'sending_method' => 'required|string',
            'restaurantId' => 'required|exists:restaurants,id',
            'address_id' => 'required|exists:addresses,id',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:foods,id',
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
            'time' => $request->time ?? 'now',
            'send_price' => $request->send_price,
            'sending_method' => $request->sending_method,
            'notes' => $request->notes,
        ]);
        $foodIds = collect($request->items)->pluck('id')->toArray();
        $foods = FoodOption::whereIn('id', $foodIds)->get();
        $total = 0;

        foreach ($request->items as $item) {
            $food = $foods->firstWhere('id', $item['id']);
            if (!$food) continue;

            $lineTotal = $food->price_order * $item['quantity'];
            $dishCount = 0;
            if ($food->dish && $food->dish_price) {
                $dishCount = floor($item['quantity'] / $food->dish);
                $dishCount = max(1, $dishCount);
                $lineTotal += $dishCount * $food->dish_price;
            }
            $total += $lineTotal;


            $order->items()->create([
                'food_option_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $food->price,
                'dish_quantity' => $dishCount,
            ]);
        }
        $discount = 0;
        if ($request->sending_method == 'pike')
        {
            $distance = distanceKm($rest->latitude, $rest->longitude, $address->latitude, $address->longitude);
            $send_price = $rest->send_price * $distance;
            $total += $send_price;
        }

        if ($request->restaurant_discount === true)
        {
            $rest_discount = ($rest->discount_percentage * $total)/100;
            $total = $total - $rest_discount;
        }
        elseif (!empty($request->discount_code))
        {
            $discount  =  DiscountCode::where('name', $request->discount_code)->first();
            if (!$discount)
            {
                return api_response([],'کد تخفیف اشتباه است', 400);
            }
            if ($discount->valid_until < Carbon::now())
            {
                return api_response([],'کد منقضی شده', 400);
            }
            if($discount->restaurant_id != null && $discount->restaurant_id != $request->restaurantId)
            {
                return api_response([],'این کد برای این مجموعه قابل استفاده نیست', 400 );
            }
            if ($discount->max_discount < $total)
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

            $total = $total - ($total * $discount->percentage/100);
            return $total;
        }



        if ($request->is_wallet == true)
        {
            $balance = $user->wallet->balance;
            if ($total < $balance)
            {
                $new_balance =  $balance - $total;
                $user->wallet->balance = $new_balance;
                $user->wallet->save();
                $order->update(['payment_status' => 'paid', 'status' => 'processing' , 'total_amount' => $total]);
                Payment::create([
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'amount' => $total,
                    'payment_method' => 'wallet',
                    'gateway' => $request->gateway,
                ]);
            }
            else{
                $new_balance = $total - $balance;
                $user->wallet->balance = 0;
                $user->wallet->save();
                Payment::create([
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'amount' => $total,
                    'payment_method' => 'both',
                    'gateway' => $request->gateway,
                ]);
                $x = ui_code($order->id , $user->id);
                $a = new ParsianPayment();
                $b = $a->pay($x , (int)$total , 'https://api.testghazaresan.ir?order_id=' . $order->id. '&uni=' . $x );
                return api_response($b,'سفارش با موفقیت ثبت شد.');
            }


        }
        if ($request->is_wallet == false)
        {
            $x = ui_code($order->id , $user->id);
            $a = new ParsianPayment();
            $b = $a->pay($x , (int)$total , 'https://api.testghazaresan.ir?order_id=' . $order->id. '&uni=' . $x );
            return api_response($b,'سفارش با موفقیت ثبت شد.');
        }
//        $data = ['name' => $order->user->name,
//            'date' => Jalalian::now()->format('Y/m/d H:i'),
//            'restaurant' => $order->restaurant->name,
//        ];
//        sms('9by4knaewe6rvmo' ,'09902866182' , $data );

        return api_response([
            'order_id' => $order->id,

        ],'سفارش با موفقیت ثبت شد.');
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
    public function test()
    {
        $x = ui_code(1 , 5);
//        $a = new ParsianPayment();
//        $b = $a->pay(15 , 10000 , 'https://api.testghazaresan.ir/' );
        return api_response($x);

    }
}
