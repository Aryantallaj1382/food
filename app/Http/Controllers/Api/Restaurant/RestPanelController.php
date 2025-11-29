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
        $rest = Restaurant::where('user_id', $user->id)->first();
        $pending = Order::where('restaurant_id', $rest->id)->where('status', 'pending')->count();
       $is_pending = $pending > 0 ? true : false;
        return api_response(['havePending'=>$is_pending]);
    }
    public function panel()
    {
        $user = auth()->user();
        $restaurant = Restaurant::where('user_id', $user->id)->first();
        $order = Order::whereRelation('user' , 'id' , $user->id)->where('payment_status' , 'paid')
            ->whereDate('created_at', Carbon::today())->orWhere('payment_status' , 'cash')
            ->where('status','!=','delivery')->
            where('status','!=','completed')->
            where('status','!=','rejected   ')->
            whereNotNull('time')->where('time','!=','now')->latest()->get();

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
