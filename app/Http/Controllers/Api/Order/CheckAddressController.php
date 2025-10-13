<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class CheckAddressController extends Controller
{
    public function checkAddress(Request $request, $address_id, $restaurant_id)
    {
        $address = Address::findOrFail($address_id);
        $restaurant = Restaurant::findOrFail($restaurant_id);

        $latitude = $address->latitude;
        $longitude = $address->longitude;

        $restaurant_latitude = $restaurant->latitude;
        $restaurant_longitude = $restaurant->longitude;
        $deliveryRadius = $restaurant->delivery_radius_km;

        $earthRadius = 6371;

        $latFrom = deg2rad($restaurant_latitude);
        $lonFrom = deg2rad($restaurant_longitude);
        $latTo = deg2rad($latitude);
        $lonTo = deg2rad($longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        $distance = $earthRadius * $angle;

        if ($distance <= $deliveryRadius) {
            return response()->json([
                'status' => true,
                'message' => 'این آدرس در محدوده ارسال رستوران است.',
                'distance_km' => round($distance, 2),
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'این آدرس خارج از محدوده ارسال رستوران است.',
                'distance_km' => round($distance, 2),
            ]);
        }
    }

}
