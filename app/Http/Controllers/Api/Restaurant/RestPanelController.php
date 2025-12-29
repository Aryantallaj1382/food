<?php

namespace App\Http\Controllers\Api\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RestPanelController extends Controller
{

    public function hasPendingOrder()
    {
        $user = auth()->user();
        $restaurant = Restaurant::where('user_id', $user->id)->firstOrFail();

        $now = Carbon::now(); // زمان فعلی
        $today = $now->format('Y-m-d');

        $hasPending = Order::where('restaurant_id', $restaurant->id)
            ->where('status', 'pending')
            ->whereDate('created_at', $today) // فقط سفارش‌های امروز
            ->where(function ($query) use ($now) {
                $query->where('time', '!=','now') // اگر "now" بود → همیشه در انتظار حساب میشه
                ->Where(function ($q) use ($now) {
                    $q->whereNotNull('time')
                        ->where('time', '!=', 'now')
                        ->whereRaw("STR_TO_DATE(CONCAT(DATE(created_at), ' ', time), '%Y-%m-%d %H:%i') > ?", [$now]);
                });
            })
            ->exists();

        return api_response([
            'havePending' => $hasPending
        ]);
    }
    public function panel()
    {
        $user = auth()->user();
        $restaurant = Restaurant::where('user_id', $user->id)->first();
        $order = Order::whereRelation('restaurant', 'user_id', $user->id)
            ->whereIn('payment_status', ['paid', 'cash'])
            ->whereDate('created_at', Carbon::today())
            ->whereNotIn('status', ['delivery', 'completed', 'rejected'])
            ->whereNotNull('time')
            ->where('time', '!=', 'now')
            ->latest()
            ->get();

        return api_response([
            'is_open' => $restaurant->is_open,
            'name' => $restaurant->name,
            'order' =>$order->isEmpty() ? 0 : 1 ,
        ]);

    }
    public function open()
    {
        $user = auth()->user();
        $restaurant = Restaurant::where('user_id', $user->id)->first();
        if ($restaurant->is_open == true)
        {
            $restaurant->update(['is_open' => false]);
            return api_response([],'وضعیت رستوران به بسته شده تغییر کرد');
        }
        else{
            $restaurant->update(['is_open' => true]);
            return api_response([],'وضعیت رستوران به باز تغییر کرد');

        }
    }
    public function changePassword(Request $request)
    {
        $user = auth()->user();


        $request->validate([
            'password' => 'required',
        ]);
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return response()->json(['message' => 'رمز عبور با موفقیت تغییر کرد.']);


    }

}
