{{-- resources/views/admin/sliders/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'ویرایش اسلایدر')

@section('content')
    <div class="container mx-auto py-6" dir="rtl">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">ویرایش اسلایدر</h1>

        <!-- نمایش خطاهای ولیدیشن -->
        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-300 text-red-700 rounded-lg">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.sliders.update', $slider->id) }}" method="POST" enctype="multipart/form-data"
              class="bg-white p-8 rounded-xl shadow-lg space-y-6 max-w-2xl">

            @csrf
            @method('PUT')

            <!-- تصویر فعلی + آپلود جدید -->
            <div>
                <label class="block mb-2 font-semibold text-gray-700">تصویر فعلی</label>
                <div class="mb-4">
                    <img src="{{ $slider->image }}" alt="اسلایدر"
                         class="w-full max-w-md h-64 object-cover rounded-lg shadow border">
                </div>

                <label class="block mb-1 font-semibold text-gray-700">تغییر تصویر (اختیاری)</label>
                <input type="file" name="image" accept="image/*"
                       class="border p-3 rounded-md w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition
                              file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-xs text-gray-500 mt-1">فرمت‌های مجاز: JPG, PNG, WebP | حداکثر ۲ مگابایت</p>
            </div>

            <!-- لینک -->
            <div>
                <label class="block mb-1 font-semibold text-gray-700">لینک (اختیاری)</label>
                <input type="url" name="link" value="{{ old('link', $slider->link) }}"
                       placeholder="https://example.com"
                       class="border p-3 rounded-md w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('link') border-red-500 @enderror">
                @error('link')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- ترتیب نمایش -->
            <div>
                <label class="block mb-1 font-semibold text-gray-700">ترتیب نمایش</label>
                <input type="number" name="order" value="{{ old('order', $slider->order) }}"
                       class="border p-3 rounded-md w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition @error('order') border-red-500 @enderror">
                @error('order')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- دکمه‌ها -->
            <div class="flex gap-3 pt-4">
                <button type="submit"
                        class="px-8 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition shadow">
                    به‌روزرسانی
                </button>
                <a href="{{ route('admin.sliders.index') }}"
                   class="px-8 py-3 bg-gray-500 text-white font-semibold rounded-lg hover:bg-gray-600 transition">
                    بازگشت
                </a>
            </div>
        </form>
    </div>
@endsection
