<?php

namespace App\Http\Controllers\Admin\restaurant;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\User;
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
        $users = User::select(['id', 'first_name', 'mobile'])->get(); // 👈 لیست کاربران

        return view('restaurant.create', compact('categories', 'users'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'address' => 'nullable|string|max:500',
            'text' => 'nullable|string|max:500',
            'tax_enabled' => 'nullable',          // تغییر به nullable
            'panel_editable' => 'nullable',
            'distance_km' => 'required',
            'cost_per_km' => 'required',
            'free_shipping' => 'required',
            'cod_courier' => 'required',
            'online_courier' => 'required',
            'pay_type' => 'required',
            'morning_end' => 'required',
            'afternoon_end' => 'required',
            'afternoon_start' => 'required',
            'morning_start' => 'required',
            'grt_ready_minute' => 'nullable|integer|min:0',
            'sending_way' => 'nullable|string|max:255',
            'minimum_price' => 'nullable|numeric|min:0',
            'work_time' => 'nullable|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'is_open' => 'nullable|boolean',
            'send_price' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'delivery_radius_km' => 'nullable|numeric|min:0',
            'discount' => 'nullable',
            'image' => 'nullable|image|max:2048',
        ]);
        $validated['tax_enabled'] = $request->has('tax_enabled') ? 1 : 0;
        $validated['panel_editable'] = $request->has('panel_editable') ? 1 : 0;
        $validated['free_shipping'] = $request->has('free_shipping') ? 1 : 0;
        $validated['discount'] = $request->has('discount') ? 1 : 0;
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
        $users = User::all();
        $categories = Category::all();
        return view('restaurant.edit',  compact('restaurant', 'users', 'categories'));
    }

    public function update(Request $request, Restaurant $restaurant)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'address' => 'nullable|string|max:500',
            'text' => 'nullable|string|max:500',
            'tax_enabled' => 'nullable',
            'panel_editable' => 'nullable',
            'distance_km' => 'nullable',
            'cost_per_km' => 'nullable',
            'free_shipping' => 'nullable',
            'cod_courier' => 'nullable',
            'online_courier' => 'nullable',
            'pay_type' => 'nullable',
            'morning_end' => 'nullable',
            'afternoon_end' => 'nullable',
            'afternoon_start' => 'nullable',
            'morning_start' => 'nullable',
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
            'discount' => 'nullable',
            'image' => 'nullable|image|max:2048',
        ]);

        $validated['tax_enabled'] = $request->has('tax_enabled') ? 1 : 0;
        $validated['panel_editable'] = $request->has('panel_editable') ? 1 : 0;
        $validated['free_shipping'] = $request->has('free_shipping') ? 1 : 0;
        $validated['discount'] = $request->has('discount') ? 1 : 0;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/restaurants'), $imageName);
            $validated['image'] = 'uploads/restaurants/' . $imageName;
        }

        $restaurant->update($validated);

        if ($request->has('categories')) {
            $restaurant->categories()->sync($request->categories);
        }

        return redirect()->route('admin.restaurants.index')->with('success', 'رستوران با موفقیت به‌روزرسانی شد.');
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
        $order = Order::find($id);
        return view('restaurant.items', compact(['orders' , 'order']));
    }
    public function map()
    {
        $restaurants = Restaurant::select('id', 'name', 'latitude', 'longitude', 'address')->get();
        return view('restaurant.map', compact('restaurants'));
    }

}
