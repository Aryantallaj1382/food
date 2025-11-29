<?php

namespace App\Http\Controllers\Api\Restaurant;

use App\Models\Restaurant;
use App\Models\TempUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RestLoginController
{
    public function login(Request $request)
    {
        $request->validate([
            'mobile' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('mobile',$request->mobile)->first();
        if (!$user) {
            return  api_response([],'کاربری یافت نشد', 422);
        }
        $rest  = Restaurant::where('user_id', $user->id)->first();
        if (!$rest)
        {
            return api_response([],'شما اجازه ی دسترسی ندارید', 422);
        }
        if (!empty($request->password)) {
            if (!Hash::check($request->password, $user->password)) {
                return api_response([],'شماره موبایل یا رمز عبور اشتباه است.', 422);

            }
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return api_response([
            'token' => $token,
            'mobile' => $user->mobile,
        ], 'ورود موفقیت‌آمیز');
    }

}
