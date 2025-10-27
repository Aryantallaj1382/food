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
    public function index()
    {
        $categories = Category::where('slug' , '!=' , 'additive')->select(['id','name','icon','slug'])->get();

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
                    'food_image' => $foodImage, // عکس یکی از غذاهای تخفیف‌دار
                    'foods' => $discounts,
                ];
            })
            ->values();





        $topRestaurants = Restaurant::whereNotNull('discount_percentage')
            ->orderByDesc('discount_percentage')
            ->take(7)
            ->get()->map(function ($restaurant) {
                return [

                        'id' => $restaurant->id,
                        'image' => $restaurant->image,
                        'is_open' => $restaurant->is_open,
                        'name' => $restaurant->name,
                        'rate' => $restaurant->rate,
                        'food_image' => optional($restaurant->foods->first())->image,
                        'discount_percentage' => $restaurant->discount_percentage,
                        'rate_count' => $restaurant->rate_count,
                ];
            });

        return api_response([
            'slider' => $sliders,
            'categories' => $categories,
            'restaurants' => $topRestaurants,
            'foods' => $foods,
        ]);
    }
}
