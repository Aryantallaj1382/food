<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FoodOption;
use App\Models\Restaurant;
use App\Models\Slider;
use App\Models\SystemSetting;
use Illuminate\Http\Request;    use App\Models\Food;
use Illuminate\Support\Facades\Cache;


class MainController extends Controller
{
    public function support()
    {
        $mobile1 = SystemSetting::where('kay', 'mobile1')->value('value');
        $mobile2 = SystemSetting::where('kay', 'mobile2')->value('value');
        $mobile3 = SystemSetting::where('kay', 'mobile3')->value('value');
        return api_response([
            'mobile1' => $mobile1,
            'mobile2' => $mobile2,
            'mobile3' => $mobile3
        ]);
    }
    public function category()
    {
        $categories = Category::where('slug' , '!=' , 'additive')->select(['id','name','icon','slug'])->get()->sortBy('id')->values();
        return api_response(
            $categories,
        );
    }
    public function index()
    {
        $categories = Cache::remember('categories', 1, function () {
            return Category::where('slug', '!=', 'additive')
                ->select(['id','name','icon','slug'])
                ->orderBy('id')
                ->get();
        });

        $sliders = Cache::remember('sliders', 1, function () {
            return Slider::orderBy('order', 'asc')
                ->select(['id','image','link','order'])
                ->get();
        });

        $foods = Cache::remember('foods11', 1, function () {
            return Restaurant::whereNotNull('team_text')
                ->withAvg(['comments as rate' => function ($query) {
                    $query->select(\DB::raw('avg(rating)'))
                        ->whereHas('order', function ($q) {
                            $q->whereColumn('orders.restaurant_id', 'restaurants.id');
                        });
                }], 'rating')
                ->orderByDesc('is_open')
                ->get()
                ->sortByDesc('rate') // بعد از گرفتن نتایج، مرتب‌سازی در Collection
                ->values()
                ->map(function ($restaurant) {
                    $foodImage = optional($restaurant->foods->first())->image;
                    $discounts = [$restaurant->team_text];

                    return [
                        'restaurant_name' => $restaurant->name,
                        'restaurant_id' => $restaurant->id,
                        'restaurant_image' => $restaurant->image,
                        'rate' => $restaurant->rate,
                        'rate_count' => $restaurant->rate,
                        'category' => $restaurant->categories->first()->slug,
                        'is_open' => $restaurant->is_open,
                        'food_image' => $foodImage,
                        'foods' => $discounts,
                    ];
                });
        });

        $restaurants = Cache::remember('restaurants_discount11', 1, function () {
            return Restaurant::whereNotNull('discount_percentage')
                ->withAvg(['comments as rate' => function ($query) {
                    $query->select(\DB::raw('avg(rating)'))
                        ->whereHas('order', function ($q) {
                            $q->whereColumn('orders.restaurant_id', 'restaurants.id');
                        });
                }], 'rating')
                ->orderByDesc('is_open')
                ->orderByDesc('discount_percentage')
                ->take(7)
                ->get()
                ->sortByDesc('rate') // بعد از گرفتن نتایج
                ->values()
                ->map(function ($restaurant) {
                    return [
                        'id' => $restaurant->id,
                        'image' => $restaurant->image,
                        'is_open' => $restaurant->is_open,
                        'category' => $restaurant->categories->first()->slug,

                        'name' => $restaurant->name,
                        'rate_count' => $restaurant->rate,
                        'rate' => $restaurant->rate,
                        'food_image' => optional($restaurant->foods->first())->image,
                        'discount_percentage' => $restaurant->discount_percentage,
                    ];
                });
        });

        $topRestaurants = Cache::remember('top_restaurants11', 1, function () {
            return Restaurant::withAvg('comments', 'rating')
                ->whereHas('categories', function ($q) {
                    $q->whereIn('categories.id', [1, 2]);
                })
                ->orderByDesc('is_open')
                ->take(10)
                ->get()
                ->sortByDesc('comments_avg_rating') // بعد از گرفتن نتایج
                ->values()
                ->map(function ($restaurant) {
                    return [
                        'id' => $restaurant->id,
                        'image' => $restaurant->image,
                        'is_open' => $restaurant->is_open,
                        'name' => $restaurant->name,
                        'rate' => $restaurant->rate,
                        'category' => $restaurant->categories->first()->slug,

                        'food_image' => optional($restaurant->foods->first())->image,
                        'discount_percentage' => $restaurant->discount_percentage,
                        'rate_count' => $restaurant->rate,
                    ];
                });
        });

        return api_response([
            'slider' => $sliders,
            'categories' => $categories,
            'restaurants' => $restaurants,
            'foods' => $foods,
            'top_restaurants' => $topRestaurants,
        ]);
    }

//    public function index()
//    {
//        $categories = Category::where('slug' , '!=' , 'additive')->select(['id','name','icon','slug'])->get()->sortBy('id')->values();
//
//        $sliders = Slider::orderBy('order', 'asc')->select(['id','image','link', 'order'])->get();
//
//        $foods = Restaurant::
//        whereNotNull('team_text')
//            ->orderByDesc('is_open')
//            ->withAvg(['comments as rate' => function ($query) {
//                $query->select(\DB::raw('avg(rating)'))
//                    ->whereHas('order', function ($q) {
//                        $q->whereColumn('orders.restaurant_id', 'restaurants.id');
//                    });
//            }], 'rating')
//            ->orderBy('rate', 'desc')
//            ->get()
//            ->map(function ($restaurant) {
//
//
//                $foodImage = optional($restaurant->foods->first())->image;
//
//                $discounts = [$restaurant->team_text];
//
//                return [
//                    'restaurant_name' => $restaurant->name,
//                    'restaurant_id' => $restaurant->id,
//                    'restaurant_image' => $restaurant->image,
//                    'rate' =>$restaurant->rate,
//                    'rate_count' => $restaurant->rate,
//                    'is_open' => $restaurant->is_open,
//                    'food_image' => $foodImage,
//                    'foods' => $discounts,
//                ];
//            })
//            ->values();
//
//
//        $restaurants = Restaurant::whereNotNull('discount_percentage')
//            ->orderByDesc('is_open')
//            ->orderBy('discount_percentage','desc')
//            ->withAvg(['comments as rate' => function ($query) {
//                $query->select(\DB::raw('avg(rating)'))
//                    ->whereHas('order', function ($q) {
//                        $q->whereColumn('orders.restaurant_id', 'restaurants.id');
//                    });
//            }], 'rating')
//            ->orderBy('rate', 'desc')
//            ->take(7)
//            ->get()->map(function ($restaurant) {
//                return [
//
//                        'id' => $restaurant->id,
//                        'image' => $restaurant->image,
//                        'is_open' => $restaurant->is_open,
//                        'name' => $restaurant->name,
//                        'rate_count' =>$restaurant->rate,
//                        'rate' => $restaurant->rate,
//                        'food_image' => optional($restaurant->foods->first())->image,
//                        'discount_percentage' => $restaurant->discount_percentage,
//                ];
//            });
//        $topRestaurants = Restaurant::withAvg('comments', 'rating')
//            ->orderByDesc('is_open')
//            ->orderByDesc('comments_avg_rating')
//            ->whereHas('categories', function ($q) {
//                $q->whereIn('categories.id', [1, 2]);
//            })
//            ->take(10)
//            ->get()
//            ->map(function ($restaurant) {
//                return [
//                    'id' => $restaurant->id,
//                    'image' => $restaurant->image,
//                    'is_open' => $restaurant->is_open,
//                    'name' => $restaurant->name,
//                    'rate' => $restaurant->rate,   // ⭐ بهترین امتیاز
//                    'food_image' => optional($restaurant->foods->first())->image,
//                    'discount_percentage' => $restaurant->discount_percentage,
//                    'rate_count' => $restaurant->rate,
//                ];
//            });
//
//
//        return api_response([
//            'slider' => $sliders,
//            'categories' => $categories,
//            'restaurants' => $restaurants,
//            'foods' => $foods,
//            'top_restaurants' => $topRestaurants,
//        ]);
//    }
}
