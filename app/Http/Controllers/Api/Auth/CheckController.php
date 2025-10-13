<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CheckController extends Controller
{
    public function check(Request $request)
    {
        $request->validate([
            'mobile'=>'required|numeric'
        ]);
        $mobile = $request->mobile;
        $is_exist = User::where('mobile',$mobile)->exists();
        return api_response(['is_exist'=>$is_exist]);

    }
}
