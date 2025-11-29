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

        $foods = FoodOption::with(['food.restaurant'])
            ->whereNotNull('price_discount')
            ->get()
            ->groupBy(fn($item) => $item->food->restaurant->id)
            ->map(function ($group) {
                $restaurant = $group->first()->food->restaurant;

                $foodImage = optional($group->first()->food)->image;

                $discounts = $group->map(function ($item) {
                    $percent = round((($item->price - $item->price_discount) / $item->price) * 100);
                    return "{$percent} درصد تخفیف برای {$item->food->name} {$item->name}";
                })->values();

                return [
                    'restaurant_name' => $restaurant->name,
                    'restaurant_id' => $restaurant->id,
                    'restaurant_image' => $restaurant->image,
                    'rate' => $restaurant->rate,
                    'rate_count' => $restaurant->rate_count,
                    'is_open' => $restaurant->is_open,
                    'food_image' => $foodImage,
                    'foods' => $discounts,
                ];
            })
            ->values();


        $restaurants = Restaurant::whereNotNull('discount_percentage')
            ->orderByDesc('is_open')
            ->orderBy('discount_percentage')
            ->take(7)
            ->get()->map(function ($restaurant) {
                return [

                        'id' => $restaurant->id,
                        'image' => $restaurant->image,
                        'is_open' => $restaurant->is_open,
                        'name' => $restaurant->name,
                        'rate_count' => $restaurant->rate_count,
                        'rate' => round($restaurant->rate),
                        'food_image' => optional($restaurant->foods->first())->image,
                        'discount_percentage' => $restaurant->discount_percentage,
                ];
            });
        $topRestaurants = Restaurant::withAvg('comments', 'rating')
            ->orderByDesc('comments_avg_rating')
            ->orderByDesc('is_open')
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
                    'rate' => (int)$restaurant->comments_avg_rating,   // ⭐ بهترین امتیاز
                    'food_image' => optional($restaurant->foods->first())->image,
                    'discount_percentage' => $restaurant->discount_percentage,
                    'rate_count' => (float) number_format($restaurant->comments()->avg('rating'), 1),
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
