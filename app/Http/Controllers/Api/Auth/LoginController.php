<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\TempUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'mobile' => 'required',
            'password' => 'nullable',
            'otp' => 'nullable',
        ]);
        if (!empty($request->otp) && !empty($request->password)) {
            return api_response([], 'لطفا فقط یکی از رمز یا کد پیامکی را وارد کنید', 422);
        }

        $user = User::where('mobile',$request->mobile)->first();
        if(!$user){
            $tempUser = TempUser::
            where( 'mobile', $request->mobile)
                ->first();

            if (!$tempUser) {
                return response()->json(['message' => 'کد تأیید پیدا نشد'], 422);
            }

            if ($tempUser->sms_sent_code !== $request->otp) {
                return response()->json(['message' => 'کد وارد شده اشتباه است'], 422);
            }

            $sentAt = \Carbon\Carbon::parse($tempUser->sms_sent_date);
            if ($sentAt->diffInMinutes(now()) > 2) {
                return response()->json(['message' => 'کد منقضی شده است'], 422);
            }
            $user = User::create([
                'mobile'   => $request->mobile,
                'password' => \Hash::make($request->password),
            ]);
            $token = $user->createToken('auth_token')->plainTextToken;

            return api_response([
                'token'   => $token,
                'mobile' => $user->mobile,

            ] , 'ثبت نام با موفقیت انجام شد');
        }
        if (!empty($request->password)) {
            if (!Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'mobile' => ['شماره موبایل یا رمز عبور اشتباه است.'],
                ]);
            }
        }
        if (!empty($request->otp)) {
            if ($user->sms_sent_code !== $request->otp) {
                throw ValidationException::withMessages([
                    'otp' => ['کد وارد شده نامعتبر است.'],
                ]);
            }

            $user->update([
                'sms_sent_code' => null,
            ]);
        }
        if (empty($request->otp) && empty($request->password)) {
            return api_response([], 'لطفا رمز عبور یا کد پیامکی را وارد کنید', 422);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return api_response([
            'token' => $token,
            'mobile' => $user->mobile,
        ], 'ورود موفقیت‌آمیز');
    }
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'با موفقیت خارج شدید'
        ], 200);
    }

}
