<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'icon' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        // آپلود تصویر با move
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

        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'دسته‌بندی با موفقیت ایجاد شد.');
    }
    public function destroy(Category $category)
    {
        // حذف فایل آیکون اگر وجود داشت
        if ($category->icon && file_exists(public_path($category->icon))) {
            unlink(public_path($category->icon));
        }

        // حذف دسته‌بندی از دیتابیس
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'دسته‌بندی با موفقیت حذف شد.');
    }

}
