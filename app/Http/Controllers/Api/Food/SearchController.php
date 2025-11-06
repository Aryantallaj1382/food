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
        $slug = $request->input('category');
        $khosh = $request->input('khosh');
        $taem = $request->input('taem');
        $rate = $request->input('rate');

        $restaurants = Restaurant::query()
            ->select('restaurants.*') // همیشه انتخاب کن
            ->when($search, function ($query, $search) {
                $query->selectRaw(
                    'MATCH(restaurants.name) AGAINST(? IN NATURAL LANGUAGE MODE) AS relevance',
                    [$search]
                )
                    ->whereRaw(
                        'MATCH(restaurants.name) AGAINST(? IN NATURAL LANGUAGE MODE)',
                        [$search]
                    )
                    ->orderByDesc('relevance');
            })
            ->when($slug, function ($query, $slug) {
                $query->whereHas('categories', function ($q) use ($slug) {
                    $q->where('slug', $slug);
                });
            })
            ->when($taem, function ($query) {
                $query->whereNotNull('discount');
            })
            ->take(10)
            ->get();

        $foods = Food::with('restaurant.categories')
            ->select('foods.*') // همیشه انتخاب کن
            ->when($search, function ($query, $search) {
                $query->selectRaw(
                    "MATCH(foods.name, foods.description) AGAINST(? IN NATURAL LANGUAGE MODE) AS relevance",
                    [$search]
                )
                    ->whereRaw(
                        "MATCH(foods.name, foods.description) AGAINST(? IN NATURAL LANGUAGE MODE)",
                        [$search]
                    )
                    ->orderByDesc('relevance');
            })
            ->when($slug, function ($query, $slug) {
                $query->whereHas('restaurant.categories', function ($q) use ($slug) {
                    $q->where('slug', $slug);
                });
            })
            ->when($khosh, function ($query, $khosh) {
                $query->whereHas('options', function ($q) {
                    $q->whereColumn('price_discount', '<=', 'price');
                });
            })
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
                'restaurant' => $item->restaurant?->name,
                'image' => $item->image,
                'discount_percentage' => $item->discount_percentage ?? 0
            ];
        });

        return api_response([
            'restaurants' => $restaurants,
            'foods'=> $f
        ]);
    }
}

