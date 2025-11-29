<?php

namespace App\Http\Controllers\Api\Food;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Food;
use App\Models\FoodCategory;
use App\Models\Restaurant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;

class RestaurantController extends Controller
{

    public function show($id)
    {
        $restaurant = Restaurant::find($id);
        if (is_null($restaurant)) {
            return response()->json(['message' => 'Restaurant not found'], 404);
        }
        $today = Carbon::now();
        $dayName = $today->format('l');
        $return = [
            'today' => $dayName,
            'name' => $restaurant->name,
            'image' => $restaurant->image,
            'sending_way' => $restaurant->sending_way,
            'categories' => $restaurant->categories->pluck('name')->toArray(),
            'address' => $restaurant->address,
            'is_open' => $restaurant->is_open,
            'longitude' => $restaurant->longitude,
            'work_time' => $restaurant->work_time,
            'discount' => $restaurant->discount ,
            'pay_type' => $restaurant->pay_type ,
            'cod_courier' => $restaurant->cod_courier ,
            'online_courier' => $restaurant->online_courier ,
            'free_shipping' => $restaurant->free_shipping ,
            'khosh' =>(int)$restaurant->minimum_price ,
            'bg' =>null,
            'rate' =>round($restaurant->rate),
            'min_cart' =>(int)$restaurant->minimum_price,
            'text' =>$restaurant->text,
            'latitude' => $restaurant->latitude,
            'get_ready_minute' => $restaurant->grt_ready_minute,
            'discount_percentage' =>(int)$restaurant->discount_percentage,
        ];
        return api_response($return);
    }
    public function menu(Request $request, $id)
    {
        $search = $request->input('search');

        $restaurant = Restaurant::with([
            'foods.category',
            'foods.options',
            'foods.options.food'
        ])->find($id);

        if (!$restaurant) {
            return api_response([], 'Restaurant not found', 404);
        }
        $foods = $restaurant->foods;
        if ($search) {
            $foods = $foods->filter(function ($food) use ($search) {
                return stripos($food->name, $search) !== false;
            });
        }

        $grouped = $foods
            ->groupBy(function ($food) {
                return $food->category->name ?? 'بدون دسته‌بندی';
            })
            ->map(function ($foods, $categoryName) use ($restaurant) {
                $category = $foods->first()->category;
                $categoryId = $category->id ?? null;
                $categoryImage = $category->icon ?? null;
                $aboutCategory = Food::where('restaurant_id', $restaurant->id)
                    ->whereNotNull('about_category')->where('food_categories_id', $categoryId)->value('about_category') ?? null;
                return [
                    'category_id' => $categoryId,
                    'note' => $aboutCategory,
                    'category' => $categoryName,
                    'image' => $categoryImage,
                    'items' => $foods
                        ->sortByDesc(fn($food) => $food->availability_score)   // ←← سورت غذا بر اساس منطق جدید
                        ->sortByDesc('is_available')
                        ->map(function ($food) {
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
                            'subCategories' => $food->options
                                ->sortByDesc(fn($opt) => $opt->availability_score)
                                ->map(function ($option) use ($food) {
                                $discountPercent = null;
                                if ($option->price && $option->price_discount) {
                                    $discountPercent = round((($option->price - $option->price_discount) / $option->price) * 100);
                                }
                                $one = $food->options()->count() > 1 ? false : true;

                                return [
                                    'a' => $one,
                                    'id' => $option->id,
                                    'name' => $option->name,
                                    'dish' => (int)$option->dish,
                                    'dish_price' => (int)$option->dish_price,
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
               'user' => $comment->user->first_name,
               'dislikesCount' => $comment->dislikesCount(),
               'likesCount' => $comment->likesCount(),
               'is_liked' => $user
                   ? $comment->likes->where('user_id', $user->id)->where('is_like', true)->isNotEmpty()
                   : false,
               'is_disliked' => $user
                   ? $comment->likes->where('user_id', $user->id)->where('is_like', false)->isNotEmpty()
                   : false,
               'date' => $comment->created_at
                   ? Jalalian::fromDateTime($comment->created_at)->format('Y/m/d')
                   : null,
               'items' => $comment->order->items->map(fn($item) => $item->option?->food?->name . '  '.$item->option?->name)->filter()->values(),
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


    public function getAvailableTimes($restaurantId)
    {
            $restaurant = Restaurant::findOrFail($restaurantId);

        $now = Carbon::now('Asia/Tehran');           // ساعت الان تهران
        $startFrom = $now->copy()->addHours(2);      // دو ساعت بعد از الان

        $times = [];

        $intervals = [
            [Carbon::parse($restaurant->morning_start, 'Asia/Tehran'), Carbon::parse($restaurant->morning_end, 'Asia/Tehran')],
            [Carbon::parse($restaurant->afternoon_start, 'Asia/Tehran'), Carbon::parse($restaurant->afternoon_end, 'Asia/Tehran')],
        ];

        foreach ($intervals as [$start, $end]) {
            $current = $start->copy();

            while ($current->lte($end)) {
                // فقط تایم‌هایی که بعد از دو ساعت بعد الان هستند
                if ($current->gte($startFrom)) {
                    $times[] = $current->format('H:i');
                }
                $current->addMinutes(30);
            }
        }

        return api_response($times);
    }






}
