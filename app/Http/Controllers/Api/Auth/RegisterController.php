<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegisterController extends Controller
{

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'password' => 'required',
        ]);

        $user = auth()->user();
        $user = $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'password' => bcrypt($request->password),
        ]);
        return api_response([],'ثبت نام با موفقیت انجام شد', 200);
    }
}
