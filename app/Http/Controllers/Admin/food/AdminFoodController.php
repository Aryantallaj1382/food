<?php

namespace App\Http\Controllers\Admin\food;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\FoodOption;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminFoodController extends Controller
{
// Admin/FoodController.php
    public function toggle(FoodOption $option)
    {
        $option->update(['is_available' => ! $option->is_available]);

        // چک کن ببین همه آپشن‌های این غذا فعال شدن یا نه
        $allActive = $option->food->options()->where('is_available', 0)->doesntExist();

        return response()->json([
            'is_active' => $option->is_available,
            'all_active' => $allActive
        ]);
    }
    public function activateAllOptions(Food $food)
    {
        // همه آپشن‌های این غذا رو فعال کن
        $food->options()->update(['is_available' => 1]);

        // یا اگر می‌خوای لاگ بگیری:
        // DB::table('food_options')->where('food_id', $food->id)->update(['is_active' => 1]);

        return back()->with('success', "همه آپشن‌های «{$food->name}» با موفقیت فعال شدند.");
    }
    public function inactiveFoods()
    {
        $foods = Food::inactive()
            ->with('restaurant')
            ->with('inactiveOptions') // فقط اپشن‌های غیرفعال بارگذاری می‌شود
            ->orderBy('restaurant_id')
            ->orderBy('id')
            ->paginate(25);

        return view('admin.foods.inactive', compact('foods'));
    }

    public function restaurant($id)
    {
        $foods = Food::where('restaurant_id',$id)->paginate();
        $rest = Restaurant::find($id);
        return view('admin.food.restaurant',compact(['foods', 'rest']));

    }
    public function create($restaurant_id)
    {
        $restaurant = Restaurant::findOrFail($restaurant_id);
        return view('admin.food.create', compact('restaurant'));
    }

    public function store(Request $request, $restaurant_id)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'food_categories_id'   => 'required',
            'description' => 'nullable|string',

            // اعتبارسنجی گزینه‌ها
            'options'     => 'required|array|min:1',
            'options.*.title'           => 'required|string|max:100',
            'options.*.price'           => 'required|numeric|min:0',
            'options.*.price_discount'  => 'nullable|numeric|min:0',
            'options.*.is_available'    => 'required|in:0,1',
            'options.*.dish_price'      => 'nullable|numeric|min:0',
        ]);

        $data = $request->only(['name', 'food_categories_id', 'description']);
        $data['restaurant_id'] = $restaurant_id;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('foods', 'public');
        }

        $food = Food::create($data);

        $options = $request->input('options', []);
        foreach ($options as $option) {
            FoodOption::create([
                'food_id'         => $food->id,
                'name'           => $option['title'],
                'price'           => $option['price'],
                'price_discount'  => $option['price_discount'] ?? null,
                'is_available'    => $option['is_available'] ?? 0,
                'dish_price'      => $option['dish_price'] ?? null,
            ]);
        }

        return redirect()->route('admin.foods.restaurant', $restaurant_id)
            ->with('success', 'غذا با موفقیت اضافه شد.');
    }
    public function edit( $id)
    {
        $food = Food::with('options')
            ->findOrFail($id);
        $restaurant_id = $food->restaurant_id;

        return view('admin.food.edit', compact('food', 'restaurant_id'));
    }

    public function update(Request $request, $restaurant_id, Food $food)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'food_categories_id'   => 'required',
            'description' => 'nullable|string',

            // اعتبارسنجی گزینه‌ها
            'options'     => 'required|array|min:1',
            'options.*.title'           => 'required|string|max:100',
            'options.*.price'           => 'required|numeric|min:0',
            'options.*.price_discount'  => 'nullable|numeric|min:0',
            'options.*.is_available'    => 'required|in:0,1',
            'options.*.dish_price'      => 'nullable|numeric|min:0',
            'options.*.id'              => 'nullable|exists:food_options,id', // برای گزینه‌های موجود
        ]);

        $data = $request->only(['name', 'food_categories_id', 'description']);

        // تصویر جدید
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('foods', 'public');
        }

        $food->update($data);

        $existingOptionIds = $food->options()->pluck('id')->toArray();
        $submittedOptionIds = [];

        $options = $request->input('options', []);
        foreach ($options as $option) {
            if (!empty($option['id'])) {
                // بروزرسانی گزینه موجود
                $opt = FoodOption::find($option['id']);
                $opt->update([
                    'name'           => $option['title'],
                    'price'          => $option['price'],
                    'price_discount' => $option['price_discount'] ?? null,
                    'is_available'   => $option['is_available'] ?? 0,
                    'dish_price'     => $option['dish_price'] ?? null,
                ]);
                $submittedOptionIds[] = $opt->id;
            } else {
                // ایجاد گزینه جدید
                $newOpt = FoodOption::create([
                    'food_id'        => $food->id,
                    'name'           => $option['title'],
                    'price'          => $option['price'],
                    'price_discount' => $option['price_discount'] ?? null,
                    'is_available'   => $option['is_available'] ?? 0,
                    'dish_price'     => $option['dish_price'] ?? null,
                ]);
                $submittedOptionIds[] = $newOpt->id;
            }
        }

        // حذف گزینه‌هایی که دیگر ارسال نشده‌اند
        $optionsToDelete = array_diff($existingOptionIds, $submittedOptionIds);
        if (!empty($optionsToDelete)) {
            FoodOption::whereIn('id', $optionsToDelete)->delete();
        }

        return redirect()->route('admin.foods.restaurant', $restaurant_id)
            ->with('success', 'غذا با موفقیت بروزرسانی شد.');
    }


    public function destroy($id)
    {
        $food = Food::findOrFail($id);


        // حذف عکس
        if ($food->image) {
            Storage::disk('public')->delete($food->image);
        }

        // حذف گزینه‌ها
        $food->options()->delete();

        $food->delete();

        return redirect()->route('admin.foods.restaurant',$food->restaurant_id)
            ->with('success', 'غذا با موفقیت حذف شد.');
    }
}
