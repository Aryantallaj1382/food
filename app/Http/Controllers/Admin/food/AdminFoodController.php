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
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string',

            // اعتبارسنجی گزینه‌ها
            'options'     => 'required|array|min:1',
            'options.*.title'           => 'required|string|max:100',
            'options.*.price'           => 'required|numeric|min:0',
            'options.*.price_discount'  => 'nullable|numeric|min:0',
            'options.*.is_available'    => 'required|in:0,1',
            'options.*.dish_price'      => 'nullable|numeric|min:0',
        ]);

        $data = $request->only(['name', 'price', 'description']);
        $data['restaurant_id'] = $restaurant_id;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('foods', 'public');
        }

        $food = Food::create($data);

        $options = $request->input('options', []);
        foreach ($options as $option) {
            FoodOption::create([
                'food_id'         => $food->id,
                'title'           => $option['title'],
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

    public function update(Request $request, $restaurant_id, $id)
    {
        $food = Food::where('restaurant_id', $restaurant_id)->findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string',

            'options'     => 'required|array|min:1',
            'options.*.title'           => 'required|string|max:100',
            'options.*.price'           => 'required|numeric|min:0',
            'options.*.price_discount'  => 'nullable|numeric|min:0',
            'options.*.is_available'    => 'required|in:0,1',
            'options.*.dish_price'      => 'nullable|numeric|min:0',
        ]);

        $data = $request->only(['name', 'price', 'description']);

        if ($request->hasFile('image')) {
            // حذف عکس قبلی
            if ($food->image) {
                Storage::disk('public')->delete($food->image);
            }
            $data['image'] = $request->file('image')->store('foods', 'public');
        }

        $food->update($data);

        // حذف گزینه‌های قبلی
        $food->options()->delete();

        // ایجاد گزینه‌های جدید
        foreach ($request->input('options', []) as $option) {
            FoodOption::create([
                'food_id'         => $food->id,
                'title'           => $option['title'],
                'price'           => $option['price'],
                'price_discount'  => $option['price_discount'] ?? null,
                'is_available'    => $option['is_available'] ?? 0,
                'dish_price'      => $option['dish_price'] ?? null,
            ]);
        }

        return redirect()->route('admin.foods.restaurant', $restaurant_id)
            ->with('success', 'غذا با موفقیت ویرایش شد.');
    }

    public function destroy($restaurant_id, $id)
    {
        $food = Food::where('restaurant_id', $restaurant_id)->findOrFail($id);

        // حذف عکس
        if ($food->image) {
            Storage::disk('public')->delete($food->image);
        }

        // حذف گزینه‌ها
        $food->options()->delete();

        $food->delete();

        return redirect()->route('admin.foods.restaurant', $restaurant_id)
            ->with('success', 'غذا با موفقیت حذف شد.');
    }
}
