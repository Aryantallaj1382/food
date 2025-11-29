<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FoodCategory;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = FoodCategory::latest()->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }
    public function x()
    {
        $categories = Category::latest()->paginate(10);
        return view('admin.categories.x', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }
    public function edit(FoodCategory $category)
    {
        return view('admin.categories.edit', compact('category'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required',
        ]);
        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            $filename = time() . '_' . $file->getClientOriginalName(); // اسم یکتا
            $destination = public_path('uploads/categories');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);
            $validated['icon'] = 'uploads/categories/' . $filename;
        }

        FoodCategory::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'دسته‌بندی با موفقیت ایجاد شد.');
    }
    public function update(Request $request, FoodCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ]);


        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            $filename = time() . '_' . $file->getClientOriginalName(); // اسم یکتا
            $destination = public_path('uploads/categories');

            // ایجاد فولدر در صورت وجود نداشتن
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);
            $validated['icon'] = 'uploads/categories/' . $filename;
        }

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'دسته‌بندی با موفقیت بروزرسانی شد.');
    }
    public function destroy(FoodCategory $category)
    {
      if ($category->icon && file_exists(public_path($category->icon))) {
            unlink(public_path($category->icon));
        }

        // حذف دسته‌بندی از دیتابیس
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'دسته‌بندی با موفقیت حذف شد.');
    }
    public function delete(Category $category)
    {
        if ($category->icon && file_exists(public_path($category->icon))) {
            unlink(public_path($category->icon));
        }

        // حذف دسته‌بندی از دیتابیس
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'دسته‌بندی با موفقیت حذف شد.');
    }

}
