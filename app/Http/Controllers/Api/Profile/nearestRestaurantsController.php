<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class nearestRestaurantsController extends Controller
{
    public function nearestRestaurants(Request $request)
    {
        $latitude = $request->input('lat', 36.2354);
        $longitude = $request->input('lng', 57.6704);
        $radius = $request->input('radius', 4);

        if (!$latitude || !$longitude) {
            return api_response(['error' => 'latitude and longitude are required'], 400);
        }

        $restaurants = Restaurant::select('*')
            ->selectRaw("
            (6371 * acos(
                cos(radians(?)) *
                cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) *
                sin(radians(latitude))
            )) AS distance
        ", [$latitude, $longitude, $latitude])
            ->having('distance', '<=', $radius)
            ->orderBy('distance', 'asc')
            ->get();

        $a = $restaurants->map(function ($restaurant) {
            $distance = $restaurant->distance;
            if ($distance < 1) {
                $distanceValue = round($distance * 1000); // تبدیل به متر و گرد کردن
                $distanceUnit = 'm';
            } else {
                $distanceValue = round($distance, 2); // کیلومتر با دو رقم اعشار
                $distanceUnit = 'km';
            }

            return [
                'id' => $restaurant->id,
                'name' => $restaurant->name,
                'address' => $restaurant->address,
                'latitude' => $restaurant->latitude,
                'longitude' => $restaurant->longitude,
                'rate' => $restaurant->rate,
                'all_rate' =>  $restaurant->rate_count,
                'image' => $restaurant->image,
                'distance' => $distanceValue,
                'distance_unit' => $distanceUnit,

            ];
        });

        return api_response($a);
    }


}
