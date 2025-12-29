<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class RestaurantServiceTimeController extends Controller
{
    public function edit($restaurantId)
    {
        $restaurant = Restaurant::with('serviceTimes')->findOrFail($restaurantId);

        $daysOfWeek = [
            'saturday' => 'شنبه',

            'sunday' => 'یکشنبه',
            'monday' => 'دوشنبه',
            'tuesday' => 'سه‌شنبه',
            'wednesday' => 'چهارشنبه',
            'thursday' => 'پنج‌شنبه',
            'friday' => 'جمعه',
        ];

        $mealTypes = [
            'breakfast' => 'شیفت صبح',
            'dinner' => 'شیفت بعد از ظهر',
        ];
        return view('admin.restaurant.service_times.edit', compact('restaurant', 'daysOfWeek', 'mealTypes'));
    }

    public function update(Request $request, $restaurantId)
    {
        $restaurant = Restaurant::findOrFail($restaurantId);

        // پاک کردن زمان‌های قبلی
        $restaurant->serviceTimes()->delete();

        foreach ($request->service_times as $day => $meals) {
            foreach ($meals as $mealType => $times) {
                if($times['start_time'] && $times['end_time']){
                    $restaurant->serviceTimes()->create([
                        'day_of_week' => $day,
                        'meal_type' => $mealType,
                        'start_time' => $times['start_time'],
                        'end_time' => $times['end_time'],
                    ]);
                }
            }
        }

        return redirect()->back()->with('success','ساعت‌های کاری رستوران ذخیره شد.');
    }


}
