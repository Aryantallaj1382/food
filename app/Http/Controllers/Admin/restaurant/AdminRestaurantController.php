<?php

namespace App\Http\Controllers\Admin\restaurant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Restaurant;

class AdminRestaurantController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::paginate();
        return view('restaurant.index', compact('restaurants'));
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
    public function map()
    {
        $restaurants = \App\Models\Restaurant::select('id', 'name', 'latitude', 'longitude', 'address')->get();
        return view('restaurant.map', compact('restaurants'));
    }

}
