<?php

namespace App\Http\Controllers\Api\Restaurant;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Food;
use App\Models\FoodCategory;
use App\Models\FoodOption;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;


class ProductsMenuController extends controller
{
    public function categories()
    {
        $categories = FoodCategory::with('food:id,food_categories_id,is_available')->select(['id','name'])->get();

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
        $query = Food::whereRelation('restaurant', 'user_id', $user->id);

        if (!empty($foodName)) {
            $query->where('name','like', '%'.$foodName.'%');
        }
        if (!empty($category)) {
            $query->where('food_categories_id',$category);
        }

        $foods = $query->orderBy('created_at', 'desc')->paginate(5);
        $foods->getCollection()->transform(function ($food) {
            return  [
                'id' => $food->id,
                'name' => $food->name,
                'category' => $food->category?->name,
                'is_available' => $food->is_available,
                'description' => $food->description,
                'options' => $food->options->map(function ($option) {
                    return [
                        'id' => $option->id,
                        'name' => $option->name,
                        'price' => $option->price,
                        'price_discount' => $option->discount,
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
      $type =  $request->input('type');
        $id =  $request->input('id');
      if ($type == "food") {
          $food = Food::find($id);
          if ($food->is_available == false) {
              $food->is_available = true;
              $food->save();
              $food->options()->update(['is_available' => true]);
          }
          else{
              $food->is_available = false;
              $food->save();
              $food->options()->update(['is_available' => false]);
          }

      }
      elseif ($type == "option") {
          $option = FoodOption::find($id);
          if ($option->is_available == false) {
              $option->is_available = true;
              $option->save();
          }
          else{
              $option->is_available = false;
              $option->save();
          }
      }
        if ($type == "category") {
            $category = Category::find($id);
            $user = auth()->user();

            $foods = Food::where('user_id', $user->id)
                ->where('food_categories_id', $id)
                ->get();

            foreach ($foods as $food) {
                $food->is_available = false;
                $food->save();
                $food->options()->update(['is_available' => false]);
            }
        }

        return api_response([],'وضعیت تغییر کرد');


    }





    public function filterPayment(Request $request)
    {
        $user = auth()->user();
        $query = Transaction::whereRelation('restaurant', 'user_id', $user->id);
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        if ($fromDate && $toDate) {
            $query->whereBetween('created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ]);
        }
        $total = $query->where('type', 'credit')->sum('amount');

        $payment = $query->orderBy('created_at', 'desc')->paginate(15);


        $payment->getCollection()->transform(function ($pay) {
            return [
                'id' => $pay->id,
                'amount' => $pay->amount,
                'notes'=> $pay->description,
                'status' => $pay->type,
                'tracking_code' => $pay->tracking_code,
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
            'name' => 'required|string',
            'is_available' => 'required|boolean',
            'description' => 'nullable|string',
            'options' => 'array|nullable',
            'options.*.id' => 'nullable|exists:food_options,id',
            'options.*.name' => 'required|string',
            'options.*.price' => 'required|numeric',
            'options.*.price_discount' => 'nullable|numeric',
            'options.*.discount_percentage' => 'nullable|numeric',
            'options.*.is_available' => 'required|boolean',
        ]);
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
