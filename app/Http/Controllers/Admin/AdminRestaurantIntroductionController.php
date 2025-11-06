<?php

namespace App\Http\Controllers\Admin;

use App\Models\RestaurantIntroduction;

class AdminRestaurantIntroductionController
{
    public function index()
    {
        $introductions = RestaurantIntroduction::latest()->paginate(10);
        return view('admin.restaurant_introductions.index', compact('introductions'));
    }

    // حذف
    public function destroy($id)
    {
        $item = RestaurantIntroduction::findOrFail($id);
        $item->delete();

        return redirect()->back()->with('success', 'رکورد با موفقیت حذف شد.');
    }

}
