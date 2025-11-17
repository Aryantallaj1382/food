<?php

namespace App\Http\Controllers\Api\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
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

            $fromArray = CalendarUtils::toGregorian(...explode('/', $from_date));
            $toArray   = CalendarUtils::toGregorian(...explode('/', $to_date));

            $fromGregorian = $fromArray[0] . '-' . $fromArray[1] . '-' . $fromArray[2] . ' 00:00:00';
            $toGregorian   = $toArray[0] . '-' . $toArray[1] . '-' . $toArray[2] . ' 23:59:59';

            $query->whereBetween('created_at', [$fromGregorian, $toGregorian]);
        }

        $orders = $query->whereRelation('user' , 'id' , $user->id)->where('payment_status' , 'paid')->orWhere('payment_status' , 'cash')->latest()->paginate(15);
        $orders->getCollection()->transform(function($order){
            return [
                'id' => $order->id,
                'full_name' => $order->user->name,
                'created' => $order->created_at ? Jalalian::fromCarbon($order->created_at)->format('Y/m/d H:i') : null,
                'payment_method' => $order->payment_method,
                'total_amount' => $order->total_amount,
                'sending_method' => $order->sending_method,
                'status' => $order->status,
                'time' => $order->time ,
                'get_ready_time' =>  $order->get_ready_time ?? $order->time,
            ];
        });
        return api_response($orders, 'داده ها با موفقیت ارسال شدند');
    }

    public function show_order($id){

        $order=Order::find($id);

        $items = OrderItem::where('order_id', $order->id)->get();
        $price_item = OrderItem::where('order_id', $order->id)->sum('price');


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

            'admin_note'=>'این توضیح ادمین است',

            'send_price'=>$order->send_price,
            'discount' => 5,
            'total_price'=>$price_item,
            'total_amount'=>$order->total_amount,

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
        }

        $order->update([
            'status' => 'processing',
            'restaurant_accept' => 1,
            'admin_note' => $request->admin_note,
            'get_ready_time' => $getReadyTime,
        ]);

        return api_response([], 'با موفقیت تایید شد');
    }

    public function completed_order(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'status' => 'required',

        ]);
        $order = Order::find($request->order_id);
        $order->update([
            'status' => $request->status,
        ]);
        return api_response([],'با موفقیت وضعیت تغییر کرد' );

    }



}
