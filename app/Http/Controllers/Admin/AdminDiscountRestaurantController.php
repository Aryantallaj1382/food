<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RequestDiscount;
use App\Models\RestaurantDiscount;

class AdminDiscountRestaurantController  extends Controller
{
    public function index()
    {
        // همه درخواست‌های دیده‌نشده رو خودکار "سین" کن
        RequestDiscount::where('is_seen', false)->update(['is_seen' => true]);

        $requests = RequestDiscount::with('restaurant.user')
            ->latest()
            ->paginate(25);

        return view('admin.request-discounts.index', compact('requests'));
    }
    public function show()
    {
        $discounts = RestaurantDiscount::with('restaurant.user')
            ->latest()
            ->paginate(25);

        return view('admin.request-discounts.show', compact('discounts'));
    }

}
