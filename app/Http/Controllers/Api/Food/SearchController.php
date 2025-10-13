<?php

namespace App\Http\Controllers\Api\Food;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $search = $request->input('search');
        $restaurants = Restaurant::select('restaurants.*')
            ->selectRaw("MATCH(name) AGAINST(? IN NATURAL LANGUAGE MODE) AS relevance", [$search])
            ->whereRaw("MATCH(name) AGAINST(? IN NATURAL LANGUAGE MODE)", [$search])
            ->orderByDesc('relevance')
            ->take(10)
            ->get();
        $foods = Food::select('foods.*')
            ->selectRaw("MATCH(name, description) AGAINST(? IN NATURAL LANGUAGE MODE) AS relevance", [$search])
            ->whereRaw("MATCH(name, description) AGAINST(? IN NATURAL LANGUAGE MODE)", [$search])
            ->orderByDesc('relevance')
            ->take(10)
            ->get();
        $f = $foods->map(function ($item) {
            return [
                'name' => $item->name,
                'description' => $item->description,
                'rate' => 3,
                'rate_count' => 50,
                'pay_type' => ['آنلاین'],
                'category' => $item->restaurant?->categories?->pluck('name')?->toArray(),
                'is_open' => $item->restaurant?->is_open ?? 0,
                'restaurant' => $item->restaurant_name,
                'image' => $item->image,
                'discount_percentage' => $item->discount_percentage
            ];
        });
        return api_response([
            'restaurants' => $restaurants,
            'foods'=> $f
        ]);

    }
}

