<?php

namespace App\Http\Controllers\Api\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RestPanelController extends Controller
{
    public function panel()
    {
        $user = auth()->user();
        $restaurant = Restaurant::where('user_id', $user->id)->first();
        return api_response([
            'is_open' => $restaurant->is_open
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
