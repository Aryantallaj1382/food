<?php

namespace App\Http\Controllers\Admin\restaurant;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class AdminRestaurantController extends Controller
{
    public function index(Request $request)
    {
        $query = Restaurant::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }
        $restaurants = $query->paginate(12)->appends($request->query());
        $categories = Category::all();
        return view('restaurant.index', compact('restaurants', 'categories'));
    }


    public function create()
    {
        $categories = Category::select('id', 'name')->get();
        return view('restaurant.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'grt_ready_minute' => 'nullable|integer|min:0',
            'sending_way' => 'nullable|string|max:255',
            'minimum_price' => 'nullable|numeric|min:0',
            'work_time' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'is_open' => 'nullable|boolean',
            'send_price' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'delivery_radius_km' => 'nullable|numeric|min:0',
            'discount' => 'nullable|boolean',
            'image' => 'nullable|image|max:2048',
            'categories' => 'nullable|array',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/restaurants'), $imageName);
            $validated['image'] = 'uploads/restaurants/' . $imageName;
        }


        $restaurant = Restaurant::create($validated);

        if ($request->has('categories')) {
            $restaurant->categories()->sync($request->categories);
        }

        return redirect()->route('admin.restaurants.index')->with('success', 'رستوران با موفقیت ثبت شد.');
    }
    public function edit(Restaurant $restaurant)
    {
        $categories = Category::all();
        return view('restaurant.edit', compact('restaurant', 'categories'));
    }

    public function update(Request $request, Restaurant $restaurant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'minimum_price' => 'nullable|numeric|min:0',
            'grt_ready_minute' => 'nullable|numeric|min:0',
            'sending_way' => 'nullable|string',
            'send_price' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'categories' => 'nullable|array',
            'is_open' => 'nullable|boolean',
            'discount' => 'nullable|boolean',
        ]);

        // آپلود تصویر در پوشه public
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/restaurants'), $imageName);
            $validated['image'] = 'uploads/restaurants/' . $imageName;
        }

        $restaurant->update($validated);

        // دسته‌بندی‌ها
        if(isset($validated['categories'])){
            $restaurant->categories()->sync($validated['categories']);
        }

        return redirect()->route('admin.restaurants.index')->with('success', 'رستوران با موفقیت ویرایش شد.');
    }

    public function destroy(Restaurant $restaurant)
    {
        // حذف تصویر اگر وجود داشت
        if($restaurant->image && file_exists(public_path($restaurant->image))){
            unlink(public_path($restaurant->image));
        }

        $restaurant->delete();
        return redirect()->route('admin.restaurants.index')->with('success', 'رستوران با موفقیت حذف شد.');
    }


    public function show($id)
    {
        $restaurants = Restaurant::find($id);
        return view('restaurant.show', compact('restaurants'));
    }
    public function restaurant_orders($id)
    {
        $orders = Order::where('restaurant_id', $id)->paginate();
        return view('restaurant.orders', compact('orders'));
    }
    public function order_item($id)

    {
        $orders = OrderItem::where('order_id', $id)->paginate();
        return view('restaurant.items', compact('orders'));
    }
    public function map()
    {
        $restaurants = Restaurant::select('id', 'name', 'latitude', 'longitude', 'address')->get();
        return view('restaurant.map', compact('restaurants'));
    }

}
