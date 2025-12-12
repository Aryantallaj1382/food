<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $addresses = $user->addresses->where('is_main', true)->first();
        $order = Order::where('user_id', $user->id)
            ->where('status', 'processing')
            ->Orwhere('status', 'pending')
            ->where('is_received', false)
            ->whereDate('created_at', Carbon::today()) // فقط امروز
            ->get();
        return api_response([
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'mobile' => $user->mobile,
            'phone' => $user->phone,
            'balance' => (int)$user->wallet?->balance ?? 0,
            'address' => $addresses,
            'orders' => $order->map(function ($order) {
                return [
                    'name' => $order->restaurant->name,
                    'id' => $order->id,
                    'get_ready_time' => $order->created_at && Carbon::parse($order->created_at)->isToday()
                        ? ($order->get_ready_time ?? $order->time)
                        : 'now',
                    ];
            })
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'nullable',
            'last_name' => 'nullable',
            'phone' => 'nullable',
            'password' => 'nullable',
        ]);
        $user = auth()->user();
        $updateData =  [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
        ];
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }
        $user->update($updateData);

        return api_response([], 'اطلاعات ویرایش شد');
    }
//    public function get_order(Request $request)
//    {
//        $user = auth()->user();
//        $order = Order::where('user_id', $user->id)->find($request->id);
//        if ($request->status == 'yes') {
//            $order->update([
//                'status' => $request->status,
//            ]);
//        }
//
//    }

    public function completed_order(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'received' => 'required',

        ]);
        $order = Order::find($request->order_id);
        if ($request->received == false) {
            $message = ' به دست کاربر نرسیده است'.$order->id.'سافرش شماره ';
            Notification::query()->create([
                'text' => $message,
                'is_seen' => 0,
            ]);
        }
        elseif ($request->received == true) {
            $message = ' به دست کاربر رسید'.$order->id.'سافرش شماره ';
            Notification::query()->create([
                'text' => $message,
                'is_seen' => 0,
            ]);
            $order->update([
                'is_received' => true,
            ]);
        }
        return api_response([],'با موفقیت وضعیت تغییر کرد' );

    }
}
