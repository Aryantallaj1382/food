<?php

namespace App\Http\Controllers\Api\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;

class RestOrderController extends Controller
{


    public function index_order(Request $request)
    {
//        $user = auth()->user();
        $user = User::find(6);

        $user_name = $request->input('user_name');
        $mobile= $request->input('mobile');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        $status= $request->input('status');
        $payment_method = $request->input('payment_method');


        // کوئری پایه
        $query = Order::with('user','restaurant');

        // فیلتر کاربر
        if ($user_name) {
            $query->whereHas('user', function($q) use ($user_name) {
                $q->where('first_name', 'like', '%' . $user_name . '%')
                ->orWhere('last_name', 'like', '%' . $user_name . '%');
            });
        }
        if ($mobile) {
            $query->where('mobile', $mobile);

        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($payment_method) {
            $query->where('payment_method', $payment_method);
        }

        if ($from_date && $to_date) {
            $query->whereBetween('created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
        }

        $orders = $query->whereRelation('user' , 'id' , $user->id)->where('payment_status' , 'paid')->latest()->paginate(15);


        $orders->getCollection()->transform(function($order){
            return [
                'id' => $order->id,
                'first_name' => $order->user?->first_name,
                'last_name' => $order->user?->last_name,
//                'restaurant' => $order->restaurant->name,
                'created' => $order->created_at?->format('d-m-Y'),
                'payment_method' => $order->payment_method,
                'total_amount' => $order->total_amount,

                'sending_method' => $order->sending_method,
                'status' => $order->status,

            ];
        });

        return api_response($orders, 'داده ها با موفقیت ارسال شدند');
    }

    public function show_order($id){

        $order=Order::with('user','restaurant','food','options','items')->find($id);

        $items = OrderItem::where('order_id', $order->id)->get();


        if (!$order) {
            return response()->json([
                'message' => 'سفارش مورد نظر یافت نشد'
            ], 404);
        }


        $data = [
            'id' => $id,
            'price'=>$order->total_amount,
            'first_name' => $order->user?->first_name,
            'last_name' => $order->user?->last_name,
            'created' => $order->created_at?->format('d-m-Y'),
            'mobile'=>$order->mobile,
            'address'=>$order->adress?->address,
            'notes'=>$order->notes,


            'items'=>$items->map(function($item){
                return [
                    'id' => $item->id,

                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'name' => $item->options->food->name,
                    'options_name' => $item->options->name,
                    'dish_quantity'=>$item->dish_quantity,

                ];
            }),

        ];
        return api_response($data,"اطلاعات با موفقیت ارسال شد");




    }



}
