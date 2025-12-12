<?php

namespace App\Http\Controllers\Admin\discount;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use App\Models\Restaurant;
use Illuminate\Http\Request;
class AdminDiscountCodeController extends Controller
{
    public function index()
    {
        $discountCodes = DiscountCode::paginate(10);
        return view('admin.discount_codes.index', compact('discountCodes'));
    }

    public function create()
    {
        $restaurants = Restaurant::all();
        return view('admin.discount_codes.create',compact('restaurants'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:discount_codes,name',
            'percentage' => 'required|numeric|min:0|max:100',
            'max_discount' => 'nullable|numeric|min:0',
            'valid_until' => 'nullable|date',
            'one_time_use' => 'boolean',
        ]);

        DiscountCode::create($data);

        return redirect()->route('admin.discount-codes.index')
            ->with('success', 'کد تخفیف با موفقیت ایجاد شد.');
    }

    public function edit(DiscountCode $discountCode)
    {
        $restaurants = Restaurant::all();

        return view('admin.discount_codes.edit', compact(['discountCode' , 'restaurants']));
    }

    public function update(Request $request, DiscountCode $discountCode)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0|max:100',
            'max_discount' => 'nullable|numeric|min:0',
            'valid_until' => 'nullable|date',
            'one_time_use' => 'boolean',
        ]);

        $discountCode->update($data);

        return redirect()->route('admin.discount-codes.index')
            ->with('success', 'کد تخفیف با موفقیت بروزرسانی شد.');
    }

    public function destroy(DiscountCode $discountCode)
    {
        $discountCode->delete();

        return redirect()->route('admin.discount-codes.index')
            ->with('success', 'کد تخفیف با موفقیت حذف شد.');
    }
}
