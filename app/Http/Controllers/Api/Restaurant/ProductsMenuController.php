<?php

namespace App\Http\Controllers\Api\Restaurant;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Food;
use App\Models\FoodCategory;
use App\Models\FoodOption;
use App\Models\Payment;
use App\Models\Restaurant;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;


class ProductsMenuController extends controller
{
    public function categories()
    {
        $user = auth()->user();
// فرض بر این است که مدل Restaurant وجود دارد و رستوران کاربر را پیدا می‌کنیم
        $restaurant = Restaurant::where('user_id', $user->id)->first();
        if (!$restaurant) {
            return api_response([]); // یا خطای مناسب
        }
        $foods = Food::where('restaurant_id', $restaurant->id)->pluck('food_categories_id')->unique()->toArray();
        $categories = FoodCategory::with(['food' => function($query) use ($restaurant) {
            $query->where('restaurant_id', $restaurant->id);
        }])->whereIn('id', $foods)->select(['id', 'name'])->get();
        $categories = $categories->map(function($category) {
// بررسی وضعیت غذاها
            $is_available = $category->food->contains(function($food) {
                return $food->is_available == 1;
            }) ? 1 : 0;
            return [
                'id' => $category->id,
                'name' => $category->name,
                'is_available' => $is_available
            ];
        });
        return api_response($categories);
    }

        public function index(Request $request)
        {
            $user = auth()->user();

            $foodName = $request->input('search');
            $category = $request->input('category_id');
            $query = Food::whereRelation('restaurant', 'user_id', $user->id)->orderBy('is_available')->orderBy('food_categories_id');

            if (!empty($foodName)) {
                $query->where('name','like', '%'.$foodName.'%');
            }
            if (!empty($category)) {
                $query->where('food_categories_id',$category);
            }

            $foods = $query->orderBy('created_at', 'desc')->get();
            $foods  = $foods->map(function ($food) {
                return  [
                    'id' => $food->id,
                    'name' => $food->name,
                    'panel_editable' => $food->restaurant->panel_editable,
                    'category' => ['name'=> $food->category?->name],
                    'is_available' => $food->is_available,
                    'description' => $food->description,
                    'options' => $food->options->map(function ($option) {
                        return [
                            'id' => $option->id,
                            'name' => $option->name,
                            'price' => $option->price,
                            'price_discount' => $option->price_order,
                            'is_available' => $option->is_available,
                        ];
                    }),
                ];
            });

            return api_response($foods, 'فیلتر و جستجو با موفقیت انجام شد');
        }

    public function show($id)
    {
        $food = Food::with(['category', 'options', 'restaurant'])->findOrFail($id);

        $restaurantId = $food->restaurant->id;

        $previous = Food::where('restaurant_id', $restaurantId)
            ->where('id', '<', $food->id)
            ->orderBy('id', 'desc')
            ->first();

        $next = Food::where('restaurant_id', $restaurantId)
            ->where('id', '>', $food->id)
            ->orderBy('id', 'asc')
            ->first();

        $data = [
            'id' => $food->id,
            'name' => $food->name,
            'category' => $food->category?->name,
            'is_available' => $food->is_available,
            'panel_editable' => $food->restaurant->panel_editable,

            'description' => $food->description,
            'options' => $food->options->map(function ($option) {
                return [
                    'id' => $option->id,
                    'name' => $option->name,
                    'price' => $option->price,
                    'price_discount' => $option->discount,
                    'discount_percentage' => $option->discount_percentage,
                    'is_available' => $option->is_available,
                ];
            }),
            'previous_id' => $previous?->id,
            'next_id' => $next?->id,
        ];

        return api_response($data, 'جزئیات غذا با موفقیت دریافت شد');
    }
    public function change_status(Request $request)
    {
        $type = $request->input('type');
        $id   = $request->input('id');

        if ($type == "food") {
            $food = Food::findOrFail($id);

            // تغییر وضعیت غذا
            $food->is_available = !$food->is_available;
            $food->save();

            // همه آپشن‌های این غذا رو با وضعیت غذا همگام کن
            $food->options()->update(['is_available' => $food->is_available]);

            $message = 'وضعیت غذا و آپشن‌های آن تغییر کرد';
        }

        elseif ($type == "option") {
            $option = FoodOption::findOrFail($id);

            // تغییر وضعیت آپشن
            $option->is_available = !$option->is_available;
            $option->save();

            // حالا باید وضعیت غذا رو بر اساس آپشن‌ها بروز کنیم
            $food = $option->food; // فرض: رابطه belongsTo به اسم food داری

            // اگر همه آپشن‌ها غیرفعال شدن → غذا غیرفعال
            // اگر حداقل یکی فعال شد → غذا فعال
            $hasActiveOption = $food->options()->where('is_available', true)->exists();
            $allInactive     = $food->options()->where('is_available', false)->count() === $food->options()->count();

            $shouldBeAvailable = $hasActiveOption; // یا می‌تونی از !$allInactive استفاده کنی

            if ($food->is_available != $shouldBeAvailable) {
                $food->is_available = $shouldBeAvailable;
                $food->save();
            }

            $message = 'وضعیت آپشن و غذا (در صورت نیاز) تغییر کرد';
        }

        elseif ($type == "category") {
            $user     = auth()->user();
            $rest     = Restaurant::where('user_id', $user->id)->firstOrFail();

            $foods = Food::where('restaurant_id', $rest->id)
                ->where('food_categories_id', $id)
                ->get();

            if ($foods->isEmpty()) {
                return api_response([], 'این دسته‌بندی غذایی ندارد');
            }

            // تعداد غذاهای فعال در این دسته
            $activeCount   = $foods->where('is_available', true)->count();
            $totalCount    = $foods->count();

            // اگر بیشتر از نصف فعال بودن → همه رو غیرفعال کن
            // در غیر این صورت → همه رو فعال کن
            $shouldActivate = $activeCount <= $totalCount / 2;

            foreach ($foods as $food) {
                if ($food->is_available != $shouldActivate) {
                    $food->is_available = $shouldActivate;
                    $food->save();
                }

                // آپشن‌ها رو هم با غذا همگام کن
                $food->options()->update(['is_available' => $shouldActivate]);
            }

            $statusText = $shouldActivate ? 'فعال' : 'غیرفعال';
            $message = "همه غذاهای این دسته با موفقیت $statusText شدند";

            return api_response([], $message);
        }
        else {
            return api_response([], 'نوع نامعتبر است', false);
        }

        return api_response([], $message ?? 'وضعیت تغییر کرد');
    }

//    public function change_status(Request $request)
//    {
//      $type =  $request->input('type');
//        $id =  $request->input('id');
//      if ($type == "food") {
//          $food = Food::find($id);
//          if ($food->is_available == false) {
//              $food->is_available = true;
//              $food->save();
//              $food->options()->update(['is_available' => true]);
//          }
//          else{
//              $food->is_available = false;
//              $food->save();
//              $food->options()->update(['is_available' => false]);
//          }
//
//      }
//      elseif ($type == "option") {
//          $option = FoodOption::find($id);
//          if ($option->is_available == false) {
//              $option->is_available = true;
//              $option->save();
//          }
//          else{
//              $option->is_available = false;
//              $option->save();
//          }
//      }
//        if ($type == "category") {
//            $category = Category::find($id);
//            $user = auth()->user();
//
//            $foods = Food::where('user_id', $user->id)
//                ->where('food_categories_id', $id)
//                ->get();
//
//            foreach ($foods as $food) {
//                $food->is_available = false;
//                $food->save();
//                $food->options()->update(['is_available' => false]);
//            }
//        }
//
//        return api_response([],'وضعیت تغییر کرد');
//
//
//    }





    public function filterPayment(Request $request)
    {

        $user = auth()->user();
        $restaurant = Restaurant::where('user_id', $user->id)->firstOrFail();
        $query = Transaction::where('restaurant_id', $restaurant->id);
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        if ($fromDate && $toDate) {
            $query->whereBetween('created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ]);
        }
        $total = (clone $query)->where('type', 'credit')->sum('amount');

        $payment = $query->orderBy('created_at', 'desc')->paginate(50);


        $payment->getCollection()->transform(function ($pay) {
            return [
                'id' => $pay->id,
                'amount' => $pay->amount,
                'status' => $pay->type,
                'tracking_code' => $pay->order_id,
                'code' => $pay->tracking_code,
                'created_at' => $pay->created_at
                    ? Jalalian::fromDateTime($pay->created_at)->format('Y/m/d H:i')
                    : null,
            ];
        });

        return response()->json([
            'data' => $payment->items(),
            'total' => $payment->total(),
            'per_page' => $payment->perPage(),
            'last_page' => $payment->lastPage(),
            'total_amount' => $total ?? 0,
            'next_page_url' => $payment->nextPageUrl(),
        ] );    }


    public function updateFood(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:foods,id',
            'name' => 'nullable|string',
            'is_available' => 'nullable|boolean',
            'description' => 'nullable|string',
            'options' => 'array|nullable',
            'options.*.id' => 'nullable|exists:food_options,id',

            'options.*.name' => 'nullable|string',
            'options.*.price' => 'nullable|numeric',
            'options.*.price_discount' => [
                'nullable',
                'numeric',
                function ($attribute, $value, $fail) {
                    // اگر مقدار ارسال شده بود و صفر یا "0" بود → خطا
                    if ($value !== null && (float)$value == 0) {
                        $index = explode('.', $attribute)[1]; // مثلاً options.2.price_discount → می‌گیره 2
                        $fail("قیمت  نمی‌تواند صفر باشد.");
                    }
                },
            ],            'options.*.discount_percentage' => 'nullable|numeric',
            'options.*.is_available' => 'nullable|boolean',
        ]);
        if (!$request->name) {
            return api_response([],'اسم غذا باید وارد شود',422);
        }
        $food = Food::findOrFail($request->id);

        $food->update([
            'name' => $request->name,
            'is_available' => $request->is_available,
            'description' => $request->description,
        ]);
        $optionIds = [];

        if ($request->has('options')) {
            foreach ($request->options as $opt) {

                // اگر ID دارد → آپدیت
                if (isset($opt['id'])) {
                    $foodOption = FoodOption::where('food_id', $food->id)
                        ->where('id', $opt['id'])
                        ->first();

                    if ($foodOption) {
                        $foodOption->update([
                            'name' => $opt['name'],
                            'price' => $opt['price'],
                            'price_discount' => $opt['price_discount'],
                            'is_available' => $opt['is_available'],
                        ]);

                        $optionIds[] = $foodOption->id;
                    }

                } else {
                    $newOption = $food->options()->create([
                        'name' => $opt['name'],
                        'price' => $opt['price'],
                        'price_discount' => $opt['price_discount'],
                        'is_available' => $opt['is_available'],
                    ]);

                    $optionIds[] = $newOption->id;
                }
            }
        }

        $food->options()
            ->whereNotIn('id', $optionIds)
            ->delete();

        return response()->json([
            'status' => true,
            'message' => 'غذا و آپشن‌ها با موفقیت ویرایش شدند.',
            'data' => $food->load('options')
        ]);
    }




}
