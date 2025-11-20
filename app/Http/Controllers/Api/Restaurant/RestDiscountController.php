<?php

namespace App\Http\Controllers\Api\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\RequestDiscount;
use App\Models\Restaurant;
use App\Models\RestaurantDiscount;
use Illuminate\Http\Request;

class RestDiscountController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $rest = Restaurant::where('user_id', $user->id)->first();

        $discount = RestaurantDiscount::where('restaurant_id' , $rest->id )->first();
        if (!$discount) {
            $discount =  RestaurantDiscount::query()->create([
                'restaurant_id' => $rest->id,
            ]);
        }
        return api_response($discount);
    }
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'nullable',
            'status' => 'nullable',
            'text' => 'nullable',

        ]);
        $user = auth()->user();
        $rest = Restaurant::where('user_id', $user->id)->first();
        $discount = RestaurantDiscount::firstOrCreate([
            'restaurant_id' => $rest->id
        ]);

        if ($request->type == "tam_dar") {
            if ($request->status == true)
            {
                RequestDiscount::query()->create([
                    'restaurant_id' => $rest->id,
                    'title' => 'رستوران '.$rest->name.' در خواست تخفیف طعم دار داد متن درخواست : ',
                    'description' => $request->text,
                ]);

            }
            else{
                RequestDiscount::query()->create([
                    'restaurant_id' => $rest->id,
                    'title' => 'رستوران '.$rest->name.' در خواست لغو تخفیف طعم دار داد متن درخواست :',
                    'description' => $request->text,
                ]);
            }
            $discount->update([
                'tam_dar_status' => $request->status,
                'tam_dar_text' => $request->text,
            ]);


        }

        if ($request->type == "khosh") {
            if ($request->status == true)
            {
                RequestDiscount::query()->create([
                    'restaurant_id' => $rest->id,
                    'title' => 'رستوران '.$rest->name.' در خواست تخفیف خوشمزه دار داد متن درخواست : ',
                    'description' => $request->text,
                ]);

            }
            else{
                RequestDiscount::query()->create([
                    'restaurant_id' => $rest->id,
                    'title' => 'رستوران '.$rest->name.' در خواست لغو تخفیف خوشمزه دار داد متن درخواست :',
                    'description' => $request->text,
                ]);
            }
            $discount->update([
                'khosh_status' => $request->status,
                'khosh_text' => $request->text,
            ]);


        }

        if ($request->type == "first") {
            if ($request->status == true)
            {
                RequestDiscount::query()->create([
                    'restaurant_id' => $rest->id,
                    'title' => 'رستوران '.$rest->name.' در خواست تخفیف اولین سفارش داد متن درخواست : ',
                    'description' => $request->text,
                ]);
            }
            else{
                RequestDiscount::query()->create([
                    'restaurant_id' => $rest->id,
                    'title' => 'رستوران '.$rest->name.' در خواست لغو تخفیف  اولین سفارش داد متن درخواست :',
                    'description' => $request->text,
                ]);
            }
            $discount->update([
                'first_status' => $request->status,
                'first_text' => $request->text,
            ]);
        }
        if ($request->type == "code") {
            if ($request->status == true)
            {
                RequestDiscount::query()->create([
                    'restaurant_id' => $rest->id,
                    'title' => 'رستوران '.$rest->name.' در خواست کد تخفیف داد متن درخواست : ',
                    'description' => $request->text,
                ]);
            }
            else{
                RequestDiscount::query()->create([
                    'restaurant_id' => $rest->id,
                    'title' => 'رستوران '.$rest->name.' در خواست لغو کد تخفیف  داد متن درخواست :',
                    'description' => $request->text,
                ]);
            }
            $discount->update([
                'code_status' => $request->status,
                'code_text' => $request->text,
            ]);
        }

        if ($request->type == "send") {
            if ($request->status == true)
            {
                RequestDiscount::query()->create([
                    'restaurant_id' => $rest->id,
                    'title' => 'رستوران '.$rest->name.' در خواست ارسال رایگان داد',
                    'description' => $request->text,
                ]);

            }
            else{
                RequestDiscount::query()->create([
                    'restaurant_id' => $rest->id,
                    'title' => 'رستوران '.$rest->name.' در خواست لغو ارسال رایگان داد',
                ]);
            }
            $discount->update([
                'send_status' => $request->status,
            ]);


        }
        return api_response([], 'درخواست ثبت شد');

    }

}
