@extends('layouts.app')

@section('title', 'افزودن اسلایدر جدید')

@section('content')
    <div class="container mx-auto py-6" dir="rtl">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">افزودن اسلایدر جدید</h1>

        <form action="{{ route('admin.sliders.store') }}" method="POST" enctype="multipart/form-data"
              class="bg-white p-6 rounded-xl shadow space-y-4">
            @csrf

            <div>
                <label class="block mb-1 font-semibold">تصویر</label>
                <input type="file" name="image" class="border p-2 rounded-md w-full">
            </div>

            <div>
                <label class="block mb-1 font-semibold">لینک (اختیاری)</label>
                <input type="text" name="link" value="{{ old('link') }}" class="border p-2 rounded-md w-full">
            </div>

            <div>
                <label class="block mb-1 font-semibold">ترتیب نمایش</label>
                <input type="number" name="order" value="{{ old('order', 0) }}" class="border p-2 rounded-md w-full">
            </div>

            <div class="pt-3">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    ذخیره
                </button>
                <a href="{{ route('admin.sliders.index') }}" class="px-6 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 transition">
                    بازگشت
                </a>
            </div>
        </form>
    </div>
@endsection
