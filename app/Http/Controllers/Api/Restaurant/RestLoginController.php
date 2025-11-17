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
            return  api_response([],'کاربری یافت نشد');
        }
        $rest  = Restaurant::where('user_id', $user->id)->first();
        if (!$rest)
        {
            return api_response([],'شما اجازه ی دسترسی ندارید');
        }
        if (!empty($request->password)) {
            if (!Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'mobile' => ['شماره موبایل یا رمز عبور اشتباه است.'],
                ]);
            }
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return api_response([
            'token' => $token,
            'mobile' => $user->mobile,
        ], 'ورود موفقیت‌آمیز');
    }

}
