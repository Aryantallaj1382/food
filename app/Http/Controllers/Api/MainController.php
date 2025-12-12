<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FoodOption;
use App\Models\Restaurant;
use App\Models\Slider;
use Illuminate\Http\Request;    use App\Models\Food;


class MainController extends Controller
{
    public function category()
    {
        $categories = Category::where('slug' , '!=' , 'additive')->select(['id','name','icon','slug'])->get()->sortBy('id')->values();
        return api_response(
            $categories,
        );
    }
    public function index()
    {
        $categories = Category::where('slug' , '!=' , 'additive')->select(['id','name','icon','slug'])->get()->sortBy('id')->values();

        $sliders = Slider::orderBy('order', 'asc')->select(['id','image','link', 'order'])->get();

        $foods = Restaurant::
        whereNotNull('team_text')
            ->orderByDesc('is_open')
            ->withAvg(['comments as rate' => function ($query) {
                $query->select(\DB::raw('avg(rating)'))
                    ->whereHas('order', function ($q) {
                        $q->whereColumn('orders.restaurant_id', 'restaurants.id');
                    });
            }], 'rating')
            ->orderBy('rate', 'desc')
            ->get()
            ->map(function ($restaurant) {


                $foodImage = optional($restaurant->foods->first())->image;

                $discounts = [$restaurant->team_text];

                return [
                    'restaurant_name' => $restaurant->name,
                    'restaurant_id' => $restaurant->id,
                    'restaurant_image' => $restaurant->image,
                    'rate' =>$restaurant->rate,
                    'rate_count' => $restaurant->rate,
                    'is_open' => $restaurant->is_open,
                    'food_image' => $foodImage,
                    'foods' => $discounts,
                ];
            })
            ->values();


        $restaurants = Restaurant::whereNotNull('discount_percentage')
            ->orderByDesc('is_open')
            ->orderBy('discount_percentage','desc')
            ->withAvg(['comments as rate' => function ($query) {
                $query->select(\DB::raw('avg(rating)'))
                    ->whereHas('order', function ($q) {
                        $q->whereColumn('orders.restaurant_id', 'restaurants.id');
                    });
            }], 'rating')
            ->orderBy('rate', 'desc')
            ->take(7)
            ->get()->map(function ($restaurant) {
                return [

                        'id' => $restaurant->id,
                        'image' => $restaurant->image,
                        'is_open' => $restaurant->is_open,
                        'name' => $restaurant->name,
                        'rate_count' =>$restaurant->rate,
                        'rate' => $restaurant->rate,
                        'food_image' => optional($restaurant->foods->first())->image,
                        'discount_percentage' => $restaurant->discount_percentage,
                ];
            });
        $topRestaurants = Restaurant::withAvg('comments', 'rating')
            ->orderByDesc('is_open')
            ->orderByDesc('comments_avg_rating')
            ->whereHas('categories', function ($q) {
                $q->whereIn('categories.id', [1, 2]);
            })
            ->take(10)
            ->get()
            ->map(function ($restaurant) {
                return [
                    'id' => $restaurant->id,
                    'image' => $restaurant->image,
                    'is_open' => $restaurant->is_open,
                    'name' => $restaurant->name,
                    'rate' => $restaurant->rate,   // ⭐ بهترین امتیاز
                    'food_image' => optional($restaurant->foods->first())->image,
                    'discount_percentage' => $restaurant->discount_percentage,
                    'rate_count' => $restaurant->rate,
                ];
            });


        return api_response([
            'slider' => $sliders,
            'categories' => $categories,
            'restaurants' => $restaurants,
            'foods' => $foods,
            'top_restaurants' => $topRestaurants,
        ]);
    }
}
