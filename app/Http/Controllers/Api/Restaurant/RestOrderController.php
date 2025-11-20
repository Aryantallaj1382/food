<?php

namespace App\Http\Controllers\Api\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Morilog\Jalali\CalendarUtils;
use Morilog\Jalali\Jalalian;

class RestOrderController extends Controller
{



    public function index_order(Request $request)
    {
        $user = auth()->user();

        $user_name = $request->input('user_name');
        $mobile= $request->input('mobile');
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $status= $request->input('status');
        $payment_method = $request->input('payment_method');

        $query = Order::with('user','restaurant');
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
            // کاربر فیلتر تاریخ داده، از آن استفاده کن
            $from = Carbon::parse($from_date)->startOfDay();
            $to = Carbon::parse($to_date)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        } else {
            // پیش‌فرض: فقط سفارش‌های امروز
            $query->whereDate('created_at', Carbon::today());
        }
        $rest = Restaurant::where('user_id', $user->id)->first();

        $orders = $query->where('restaurant_id',$rest->id)
            ->where(function($q){
                $q->where('payment_status', 'paid')
                    ->orWhere('payment_status', 'cash');
            })
            ->latest()
            ->paginate(15);

        $orders->getCollection()->transform(function($order){
            return [
                'id' => $order->id,
                'full_name' => $order->user->name,
                'created' => $order->created_at ? Jalalian::fromCarbon($order->created_at)->format('Y/m/d H:i') : null,
                'payment_method' => $order->payment_method,
                'total_amount' => (int)$order->total_amount,
                'sending_method' => $order->sending_method,
                'status' => $order->status,
                
                'time' => $order->time,
                'get_ready_time' => $order->created_at && Carbon::parse($order->created_at)->isToday()
                    ? ($order->get_ready_time ?? $order->time)
                    : 'now',            ];
        });
        return api_response($orders, 'داده ها با موفقیت ارسال شدند');
    }

    public function show_order($id){

        $order=Order::find($id);

        $items = OrderItem::where('order_id', $order->id)->get();
        $price_item = OrderItem::where('order_id', $order->id)->sum('price');

        $isFirstFromRestaurant = !Order::where('user_id', $order->user_id)
            ->where('restaurant_id', $order->restaurant_id)
            ->where('id', '<', $order->id)
            ->exists();

        if (!$order) {
            return response()->json([
                'message' => 'سفارش مورد نظر یافت نشد'
            ], 404);
        }
        $firstOrder = Order::where('user_id', $order->user_id)
            ->orderBy('created_at', 'asc')
            ->first();



        $data = [
            'id' => $id,
            'full_name'=>$order->user->name,
            'created' => $order->created_at ? Jalalian::fromCarbon($order->created_at)->format('Y/m/d H:i') : null,
            'mobile'=>$order->mobile,
            'address'=>$order->adress?->address,
            'notes'=>$order->notes,
            'status'=>$order->status,
            'time'=>$order->time ,

            'admin_note'=>$order->admin_note,

            'send_price'=>$order->send_price,
            'discount' => 5,
            'total_price'=>$price_item,
            'total_amount'=>$order->total_amount,
            'isFirst' => $isFirstFromRestaurant, // true/false

            'sending_method' => $order->sending_method,
            'payment_method'=>$order->payment_method,
            'message' => $firstOrder && $firstOrder->id === $order->id
                ? 'این اولین سفارش شما بوده 🎉'
                : null,
            'items'=>$items->map(function($item){
                return [
                    'id' => $item->id,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'name' => $item->option?->food?->name .' '.$item?->option?->name,
                    'dish'=>$item->option?->dish,
                    'dish_price'=>$item->option?->dish_price,

                ];
            }),

        ];
        return api_response($data,"اطلاعات با موفقیت ارسال شد");

    }

    public function submit_order(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'time' => 'nullable|integer|min:0',
            'admin_note' => 'nullable',
        ]);

        $order = Order::find($request->order_id);

        if (!$order) {
            return api_response([], 'سفارش یافت نشد', 404);
        }

        $getReadyTime = null;
        if ($request->time) {
            $getReadyTime = Carbon::now('Asia/Tehran')->addMinutes($request->time)->format('H:i');
            $getReadyTimeJalali = Jalalian::fromDateTime($getReadyTime)->format('H:i');


            if ($order->sending_method == 'in_person')
            {
                $mobile = $order->user->mobile;
                $data = [
                    'readytime' =>$getReadyTimeJalali,
                ];
                sms('j5ztbv1xqsaqv6x' ,$mobile , $data );
            }
            if ($order->sending_method == 'pike')
            {
                $mobile = $order->user->mobile;
                $data = [
                    'name' =>$order->user->name ,
                ];
                sms('0xxkazsqtxh2mc2' ,$mobile , $data );
            }

        }

        $user =auth()->user();
        $order->update([
            'status' => 'processing',
            'restaurant_accept' => 1,
            'admin_note' => $request->admin_note,
            'get_ready_time' => $getReadyTime,
        ]);

        $rest = Restaurant::where('user_id', $user->id)->first();
        $name = $rest->name;
        $message = $name.' سفارش '.$request->order_id.' را تایید کرد';

        Notification::query()->create([
            'text' => $message,
            'is_seen' => 0,
        ]);

        return api_response([], 'با موفقیت تایید شد');
    }

    public function completed_order(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'status' => 'required',

        ]);
        $user = auth()->user();
        $rest = Restaurant::where('user_id', $user->id)->first();
        $name = $rest->name;

        if ($request->status == 'completed') {
            $message = $name.' سفارش '.$request->order_id.' را تکمیل کرد و درخواست پیک داد';
        }
        if ($request->status == 'delivery') {
            $message = $name.' سفارش '.$request->order_id.' را تحویل پیک داد';
        }
        if ($request->status == 'rejected') {
            $message = $name.' سفارش '.$request->order_id.' را رد کرد';

        }
        Notification::query()->create([
            'text' => $message,
            'is_seen' => 0,
        ]);
        $order = Order::find($request->order_id);
        $order->update([
            'status' => $request->status,
        ]);
        return api_response([],'با موفقیت وضعیت تغییر کرد' );

    }



}
