<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\FoodOption;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\User;
use DB;
use Illuminate\Http\Request;

class TelephoneOrderController extends Controller
{
    // نمایش فرم سفارش تلفنی
    public function create()
    {
        $restaurants = Restaurant::all();
        return view('admin.orders.create_telephone', compact('restaurants'));
    }

    // بررسی شماره همراه کاربر
    public function checkUser(Request $request)
    {
        $mobile = $request->query('mobile');

        $user = User::where('mobile', $mobile)->with('addresses')->first();

        if($user) {
            // اگر کاربر آدرس دارد، اولین آدرس را برگردان
            $address = $user->addresses->where('is_main', true)->first()?->address ?? '';

            return response()->json([
                'exists' => true,
                'user' => [
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'address' => $address,
                ]
            ]);
        }

        return response()->json(['exists' => false]);
    }

    // دریافت غذاهای یک رستوران
    public function getRestaurantFoods($restaurant_id)
    {
        $foods = Food::with('options')
            ->where('restaurant_id', $restaurant_id)
            ->get()
            ->map(function($food) {
                return [
                    'id' => $food->id,
                    'name' => $food->name,
                    'options' => $food->options->map(function($option) {
                        return [
                            'id' => $option->id,
                            'name' => $option->name,
                            'price' => $option->price, // قیمت نهایی با تخفیف
                        ];
                    }),
                ];
            });

        return response()->json($foods);
    }

    // ذخیره سفارش تلفنی
    public function store(Request $request)
    {
        $request->validate([
            'mobile'        => 'required',
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'address'       => 'required|string|max:500',
            'restaurant_id' => 'required|exists:restaurants,id',
            'foods'         => 'required|json',
            'delivery_fee'  => 'nullable|numeric|min:0',
            'note'          => 'nullable|string|max:500',
        ]);

        $foods = json_decode($request->foods, true);
        if(empty($foods)) {
            return back()->withErrors(['foods' => 'هیچ غذایی انتخاب نشده است.']);
        }

        DB::transaction(function() use ($request, $foods) {

            // ایجاد یا بروزرسانی کاربر
            $user = User::updateOrCreate(
                ['mobile' => $request->mobile],
                [
                    'first_name' => $request->first_name,
                    'last_name'  => $request->last_name,
                ]
            );
            $add = $user->addresses()->updateOrCreate(
                ['user_id' => $user->id],
                ['address' => $request->address]
            );


            // ایجاد سفارش
            $order = Order::create([
                'user_id'       => $user->id,
                'address_id'       => $add->id,
                'restaurant_id' => $request->restaurant_id,
                'send_price'  => $request->delivery_fee ?? 0,
                'admin_note'          => $request->note,
                'time'        => 'now',
                'mobile'        => $request->mobile,
                'payment_method'        => 'cash',
                'sending_method'        => $request->sending_method,
                'total_amount'        => $request->total_amount,
                'status'        => 'processing',
                'payment_status'        => 'cash',
            ]);

            $total = 0;

            foreach($foods as $item) {
                $option = FoodOption::find($item['option_id']);
                if(!$option) continue;

                $quantity = $item['qty'];
                $price = $option->discount; // قیمت واقعی با تخفیف در نظر گرفته شده

                OrderItem::create([
                    'order_id'       => $order->id,
                    'food_option_id' => $option->id,
                    'price'          => $price,
                    'quantity'       => $quantity,
                ]);

                $total += $price * $quantity;
            }

            // جمع نهایی سفارش شامل هزینه ارسال
            $order->update(['total_amount' => $total + ($request->send_price ?? 0)]);
        });

        return redirect()->route('admin.orders.index')->with('success', 'سفارش تلفنی با موفقیت ثبت شد.');
    }
}
