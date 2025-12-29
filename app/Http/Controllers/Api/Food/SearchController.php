<?php

namespace App\Http\Controllers\Api\Food;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            ->select('restaurants.*') // اول select
            ->withAvg(['comments as rate' => function ($query) {
                $query->select(DB::raw('avg(rating)'))
                    ->whereHas('order', function ($q) {
                        $q->whereColumn('orders.restaurant_id', 'restaurants.id');
                    });
            }], 'rating')
            ->when($search, function ($query, $search) {
                $query->where('restaurants.name', 'LIKE', "%{$search}%");
            })
            ->when($slug, function ($query, $slug) {
                $query->whereHas('categories', function ($q) use ($slug) {
                    $q->whereIn('categories.id', $slug);
                }, '=', count($slug));
            })
            ->when($taem, function ($query) {
                $query->whereNotNull('team_text');
            })
            ->when($khosh, function ($query) {
                $query->whereNotNull('discount_percentage');
            })
            ->orderByDesc('is_open')
            ->orderByDesc('discount_percentage')
            ->orderByDesc('rate')
            ->take(10)
            ->get();



        // -------------------- FOODS --------------------
        $foods = collect([]);
        if (!empty($search) )  {
            $foods = Food::with('restaurant.categories')
                ->select('foods.*')
                ->when($search, function ($query, $search) {
                    $query->where('foods.name', 'LIKE', "%{$search}%");
                })
                ->take(10)
                ->get();
        }

        // -------------------- MAP --------------------
        $f = $foods->map(function ($item) {
            return [
                'name' => $item->name,
                'description' => $item->description,
                'rate' => $item->restaurant->rate,
                'rate_count' => $item->restaurant->rate,
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

