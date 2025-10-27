<?php

namespace App\Http\Controllers\Api\Food;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommentLike;
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
            'discount' => $restaurant->discount ,
            'khosh' => 1000666600 ,
            'bg' =>null,
            'rate' =>4,
            'min_cart' =>(int)$restaurant->minimum_price,
            'text' =>'ارسال به خوابگاه نداریم',
            'pay_type' =>['پرداخت در محل','پرداخت آنلاین'],
            'latitude' => $restaurant->latitude,
            'get_ready_minute' => $restaurant->grt_ready_minute,
            'discount_percentage' =>(int)$restaurant->discount_percentage,
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
                                    'dish' => (int)$option->dish  ,
                                    'dish_price' => (int)$option->dish_price  ,
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
    public function comments($id)
    {
        $user = auth('sanctum')->user();
        $rest = Restaurant::findOrFail($id);
        $comments = Comment::whereRelation('order' , 'restaurant_id' , $id)->latest()->paginate();
        $comments->getCollection()->transform(function ($comment) use ($user , $rest) {
           return [
               'id' => $comment->id,
               'text' => $comment->text,
               'rating' => $comment->rating,
               'user' => $comment->user->name,
               'dislikesCount' => $comment->dislikesCount(),
               'likesCount' => $comment->likesCount(),
               'is_liked' => $user
                   ? $comment->likes->where('user_id', $user->id)->where('is_like', true)->isNotEmpty()
                   : false,
               'is_disliked' => $user
                   ? $comment->likes->where('user_id', $user->id)->where('is_like', false)->isNotEmpty()
                   : false,
               'date' => $comment->created_at?->format('d-m-Y'),
               'items' => $comment->order->items->map(fn($item) => $item->food->name ?? null)->filter()->values(),
               'replies' => $comment->replies->map(function ($reply) use ($user , $rest) {
                   return [
                       'id' => $reply->id,
                       'text' => $reply->text,
                       'image' => $rest->image,
                       'name' => $rest->name,
                   ];
               })->values(),
           ];
        });
        return api_response($comments);

    }
    public function toggle(Request $request, $id)
    {
        $request->validate([
            'is_like' => 'required|boolean',
        ]);

        $user = auth()->user();
        $comment = Comment::findOrFail($id);

        $existing = CommentLike::where('comment_id', $comment->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            if ($existing->is_like == $request->is_like) {
                $existing->delete();
                return response()->json(['message' => 'رأی شما حذف شد']);
            }

            $existing->update(['is_like' => $request->is_like]);
            return response()->json(['message' => 'رأی شما تغییر یافت']);
        }

        CommentLike::create([
            'comment_id' => $comment->id,
            'user_id' => $user->id,
            'is_like' => $request->is_like,
        ]);

        return response()->json(['message' => 'رأی شما ثبت شد']);
    }

}
