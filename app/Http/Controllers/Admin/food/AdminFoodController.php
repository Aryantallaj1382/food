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


    // فعال/غیرفعال کردن غذا
    public function toggle1(Food $food)
    {
        $newStatus = !$food->is_available;

        $food->update(['is_available' => $newStatus]);

        $food->options()->update(['is_available' => $newStatus]);

        $statusText = $newStatus ? 'فعال' : 'غیرفعال';

        return back()->with('success', "وضعیت «{$food->name}» با موفقیت به «{$statusText}» تغییر کرد.");
    }

// فعال کردن همه آپشن‌ها





// Admin/FoodController.php
    public function toggle(FoodOption $option)
    {
        $option->update(['is_available' => ! $option->is_available]);
        $allActive = $option->food->options()->where('is_available', 0)->doesntExist();

        return response()->json([
            'is_active' => $option->is_available,
            'all_active' => $allActive
        ]);
    }
    public function activateAllOptions(Food $food)
    {
        $food->options()->update(['is_available' => 1]);
        return back()->with('success', "همه آپشن‌های «{$food->name}» با موفقیت فعال شدند.");
    }
    public function inactiveFoods()
    {
        $foods = Food::inactive()
            ->with('restaurant')
            ->with('inactiveOptions')
            ->orderBy('restaurant_id')
            ->orderBy('id')
            ->paginate(25);

        return view('admin.foods.inactive', compact('foods'));
    }

    public function restaurant(Request $request, $id)
    {
        $query = Food::with('category')
            ->where('restaurant_id', $id);

        // فیلتر بر اساس نام غذا
        if ($request->filled('search')) {
            $query->where('foods.name', 'like', "%{$request->search}%");
        }


        if ($request->filled('category_id')) {
            $query->where('food_categories_id', $request->category_id);
        }

        $query->join('food_categories', 'food_categories.id', '=', 'foods.food_categories_id')
            ->orderBy('food_categories.name', 'asc')
            ->select('foods.*'); // فقط ستون‌های foods را انتخاب کن


        $foods = $query->paginate(15)->withQueryString();

        $rest = Restaurant::findOrFail($id);

        return view('admin.food.restaurant', compact('foods', 'rest'));
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
            'about_category'        => 'nullable|string|max:255',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'food_categories_id'   => 'required',
            'description' => 'nullable|string',

            // اعتبارسنجی گزینه‌ها
            'options'     => 'required|array|min:1',
            'options.*.title'           => 'required|string|max:100',
            'options.*.price'           => 'required|numeric|min:0',
            'options.*.price_discount'  => 'nullable|numeric|min:0',
            'options.*.is_available'    => 'required|in:0,1',
            'options.*.dish'      => 'nullable|numeric|min:0',
            'options.*.dish_price'      => 'nullable|numeric|min:0',
        ]);

        $data = $request->only(['name', 'food_categories_id', 'description']);
        $data['restaurant_id'] = $restaurant_id;

        if ($request->hasFile('image')) {
            $coverImage = $request->file('image');
            $fileName = time() . '_food.' . $coverImage->getClientOriginalExtension();
            $uploadPath = public_path('uploads/foods');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            $coverImage->move($uploadPath, $fileName);
            $data['image'] = 'uploads/foods/' . $fileName;
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
                'dish'      => $option['dish'] ?? null,
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

    public function update(Request $request, $restaurant_id, $id)
    {
        $food = Food::find($id);
        $request->validate([
            'name'        => 'required|string|max:255',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'food_categories_id'   => 'required',
            'about_category'   => 'nullable',
            'description' => 'nullable|string',

            // اعتبارسنجی گزینه‌ها
            'options'     => 'required|array|min:1',
            'options.*.title'           => 'nullable|string|max:100',
            'options.*.price'           => 'nullable|numeric|min:0',
            'options.*.price_discount'  => 'nullable|numeric|min:0',
            'options.*.is_available'    => 'nullable|in:0,1',
            'options.*.dish_price'      => 'nullable|numeric|min:0',
            'options.*.id'              => 'nullable|exists:food_options,id', // برای گزینه‌های موجود
        ]);

        $data = $request->only(['name', 'food_categories_id', 'description','about_category']);
        if ($request->hasFile('image')) {
            $coverImage = $request->file('image');
            $fileName = time() . '_food.' . $coverImage->getClientOriginalExtension();
            $uploadPath = public_path('uploads/foods');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }if (!empty($data['image']) && file_exists(public_path($data['image']))) {
                unlink(public_path($data['image']));
            }
            $coverImage->move($uploadPath, $fileName);

            $data['image'] = 'uploads/foods/' . $fileName;
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
        $hasAvailableOption = FoodOption::where('food_id', $food->id)
            ->where('is_available', 1)
            ->exists();

        $food->update([
            'is_available' => $hasAvailableOption ? 1 : 0
        ]);

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
