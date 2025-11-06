<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RestaurantIntroduction;
use Illuminate\Http\Request;

class RestaurantIntroductionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'store_name' => 'required',
            'address' => 'required',
            'mobile' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',

        ]);
        RestaurantIntroduction::create([
            'restaurant_name' => $request->store_name,
            'address' => $request->address,
            'mobile' => $request->mobile,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,

        ]);
        return api_response([],'با موفقیت ثبت شد');
    }

}
