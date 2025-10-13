<?php

namespace App\Http\Controllers\Api\Food;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function show($id)
    {
        $restaurant = Restaurant::find($id);
        if (is_null($restaurant)) {
            return response()->json(['message' => 'Restaurant not found'], 404);
        }
        $return = [
            'name' => $restaurant->name,
            'image' => $restaurant->image,
            'sending_way' => $restaurant->sending_way,
            'categories' => $restaurant->categories->pluck('name')->toArray(),
            'address' => $restaurant->address,
            'is_open' => $restaurant->is_open,
            'longitude' => $restaurant->longitude,
            'work_time' => $restaurant->work_time,
            'bg' =>null,
            'rate' =>4,
            'min_cart' =>10000,
            'text' =>'ارسال به خوابگاه به هیچ عنوان نداریم',
            'pay_type' =>['پرداخت در محل','پرداخت آنلاین'],
            'latitude' => $restaurant->latitude,
            'get_ready_minute' => $restaurant->grt_ready_minute,
            'discount_percentage' => $restaurant->discount_percentage,
        ];
        return api_response($return);
    }
    public function menu($id)
    {
        $restaurant = Restaurant::with('foods.category', 'foods.options')->find($id);

        if (!$restaurant) {
            return api_response([], 'Restaurant not found', 404);
        }

        // گروه‌بندی غذاها بر اساس کتگوری
        $grouped = $restaurant->foods
            ->groupBy(function ($food) {
                return $food->category->name ?? 'بدون دسته‌بندی';
            })
            ->map(function ($foods, $categoryName) {
                $category = $foods->first()->category;
                $categoryId = $category->id ?? null;
                $categoryImage = $category->icon ?? null;

                return [
                    'category_id' => $categoryId,
                    'category' => $categoryName,
                    'image' => $categoryImage,
                    'items' => $foods->map(function ($food) {
                        $price = $food->options->min('price') ?? null;
                        $discountPrice = $food->options->min('price_discount') ?? null;
                        $discountPercent = null;
                        if ($price && $discountPrice) {
                            $discountPercent = round((($price - $discountPrice) / $price) * 100);
                        }

                          return [
                            'id' => $food->id,
                            'name' => $food->name,
                            'image' => $food->image,
                            'description' => $food->description,
                            'available' => (bool) $food->is_available,
                            'price' => $price,
                            'discountPrice' => $discountPrice,
                            'discountPercent' => $discountPercent,
                            'subCategories' => $food->options->map(function ($option) use ($food) {
                                $discountPercent = null;
                                if ($option->price && $option->price_discount) {
                                    $discountPercent = round((($option->price - $option->price_discount) / $option->price) * 100);
                                }
                                $one = $food->options()->count() > 1 ?false : true;

                                return [
                                    'a' => $one,
                                    'id' => $option->id,
                                    'name' => $option->name  ,
                                    'food' => $option->food->name,
                                    'price' => $option->price,
                                    'discountPrice' => $option->discount,
                                    'discountPercent' => $discountPercent,
                                    'available' => (bool) $option->is_available,
                                ];
                            })->values(),
                        ];
                    })->values(),
                ];
            })
            ->values();

        // مرتب‌سازی: کتگوری با آیدی 4 آخر لیست قرار گیرد
        $grouped = $grouped->sortBy(function ($category) {
            return $category['category_id'] == 4 ? 1 : 0;
        })->values();

        return api_response($grouped, 'success');
    }

    public function times($id)
    {
        $restaurant = Restaurant::findOrFail($id);

        $serviceTimes = $restaurant->serviceTimes
            ->groupBy('day_of_week')
            ->map(function ($dayGroup) {
                return $dayGroup->map(function ($time) {
                    return [
                        'meal_type'  => $time->meal_type,
                        'start_time' => $time->start_time,
                        'end_time'   => $time->end_time,
                    ];
                });
            });

        return api_response([
         $serviceTimes,
        ]);
    }


}
