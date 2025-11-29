@extends('layouts.app')

@section('title', 'ویرایش دسته‌بندی - ' . $category->name)

@section('content')
    <div class="container mx-auto py-10 px-4" dir="rtl">
        <div class="max-w-3xl mx-auto">

            <!-- هدر صفحه -->
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-3xl font-extrabold text-gray-800 flex items-center gap-3">
                    ویرایش دسته‌بندی
                </h1>
                <a href="{{ route('admin.categories.index') }}"
                   class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-2">
                    بازگشت به لیست
                </a>
            </div>

            <!-- کارت اصلی -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-6 text-center">
                    <h2 class="text-2xl font-bold">{{ $category->name }}</h2>
                    <p class="text-indigo-100 mt-1">شناسه: #{{ $category->id }}</p>
                </div>

                <div class="p-8">
                    <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- نام دسته‌بندی -->
                        <div class="mb-8">
                            <label for="name" class="block text-sm font-bold text-gray-700 mb-3">نام دسته‌بندی</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required
                                   class="w-full border-2 border-gray-300 rounded-xl px-5 py-4 text-lg focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition duration-200"
                                   placeholder="مثال: پیتزا، برگر، نوشیدنی...">
                            @error('name')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- پیش‌نمایش آیکون فعلی -->
                        <div class="mb-8 text-center">
                            <label class="block text-sm font-bold text-gray-700 mb-4">آیکون فعلی</label>
                            <div class="inline-block">
                                @if($category->icon)
                                    <img src="{{  $category->icon }}"
                                         alt="{{ $category->name }}"
                                         class="w-32 h-32 object-cover rounded-full shadow-2xl border-4 border-white ring-4 ring-indigo-100">
                                    <p class="text-xs text-gray-500 mt-3">برای تغییر، عکس جدید انتخاب کنید</p>
                                @else
                                    <div class="w-32 h-32 bg-gray-200 border-4 border-dashed border-gray-400 rounded-full flex items-center justify-center mx-auto">
                                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 mt-3 text-sm">هیچ آیکونی انتخاب نشده</p>
                                @endif
                            </div>
                        </div>

                        <!-- آپلود آیکون جدید -->
                        <div class="mb-10">
                            <label for="icon" class="block text-sm font-bold text-gray-700 mb-3">آیکون جدید (اختیاری)</label>
                            <input type="file" name="icon" id="icon" accept="image/*"
                                   class="block w-full text-sm text-gray-600
                                      file:mr-4 file:py-4 file:px-6
                                      file:rounded-xl file:border-0
                                      file:text-sm file:font-medium
                                      file:bg-gradient-to-r file:from-indigo-600 file:to-purple-600 file:text-white
                                      hover:file:from-indigo-700 hover:file:to-purple-700
                                      cursor-pointer border-2 border-dashed border-gray-300 rounded-xl p-6">
                            <p class="text-xs text-gray-500 mt-3 text-center">
                                فرمت‌های مجاز: JPG, PNG, WebP, SVG — حداکثر ۲ مگابایت
                            </p>
                            @error('icon')
                            <p class="text-red-500 text-xs mt-2 text-center">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- دکمه‌های عملیات -->
                        <div class="flex justify-center gap-4">
                            <button type="submit"
                                    class="px-10 py-4 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold rounded-xl hover:from-emerald-700 hover:to-teal-700 transition shadow-lg transform hover:scale-105 duration-200 flex items-center gap-3">
                                ذخیره تغییرات
                            </button>

                            <a href="{{ route('admin.categories.index') }}"
                               class="px-10 py-4 bg-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-300 transition shadow-md">
                                انصراف
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- پیام موفقیت (در صورت وجود) -->
            @if(session('success'))
                <div class="mt-8 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-xl text-center font-medium">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>
@endsection
