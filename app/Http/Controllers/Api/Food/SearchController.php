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

        // -------------------- RESTAURANTS --------------------
        $restaurants = Restaurant::query()
            ->orderByDesc('is_open')
            ->orderBy('discount_percentage')
            ->select('restaurants.*')
            ->when($search, function ($query, $search) {
                $query->where('restaurants.name', 'LIKE', "%{$search}%");
            })
            ->when($slug, function ($query, $slug) {
                $query->whereHas('categories', function ($q) use ($slug) {
                    $q->whereIn('categories.id', $slug);
                });
            })

            ->when($taem, function ($query) {
                $query->whereNotNull('discount');
            })

            ->take(10)
            ->get();

        // -------------------- FOODS --------------------
        if (!empty($slug) || !empty($taem)) {
            $foods = collect([]);
        } elseif (empty($search) && empty($khosh)) {
            $foods = collect([]);
        } else {
            $foods = Food::with('restaurant.categories')
                ->select('foods.*')
                ->when($search, function ($query, $search) {
                    $query->where('foods.name', 'LIKE', "%{$search}%");
                })

                ->when($khosh, function ($query) {
                    $query->whereHas('options', function ($q) {
                        $q->whereColumn('price_discount', '<=', 'price');
                    });
                })

                ->take(10)
                ->get();
        }

        // -------------------- MAP --------------------
        $f = $foods->map(function ($item) {
            return [
                'name' => $item->name,
                'description' => $item->description,
                'rate' => 3,
                'rate_count' => 50,
                'pay_type' => $item->restaurant->pay_type,
                'restaurant_id' => $item->restaurant->id,
                'id' => $item->id,
                'category' => $item->restaurant?->categories?->pluck('name')?->toArray(),
                'is_open' => $item->restaurant?->is_open ?? 0,
                'restaurant' => $item->restaurant?->name,
                'image' => $item->image,
                'discount_percentage' => $item->discount_percentage ?? 0,
                'options' => $item->options,
            ];
        });

        return api_response([
            'restaurants' => $restaurants,
            'foods' => $f,
        ]);
    }
}

