@extends('layouts.app')

@section('title', 'افزودن دسته‌بندی')

@section('content')
    <div class="container mx-auto py-6" dir="rtl">
        <h1 class="text-2xl font-extrabold text-gray-800 mb-6 text-center">➕ افزودن دسته‌بندی جدید</h1>

        <div class="bg-white shadow rounded-xl p-6 max-w-lg mx-auto">
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">نام دسته‌بندی</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="w-full border rounded-lg p-2 focus:ring focus:ring-green-200">
                    @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">اسلاگ (Slug)</label>
                    <input type="text" name="slug" value="{{ old('slug') }}"
                           class="w-full border rounded-lg p-2 focus:ring focus:ring-green-200">
                    @error('slug') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">آیکون (تصویر)</label>
                    <input type="file" name="icon" accept="image/*"
                           class="w-full border rounded-lg p-2 focus:ring focus:ring-green-200">
                    @error('icon') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-between items-center mt-6">
                    <a href="{{ route('admin.categories.index') }}" class="text-gray-600 hover:underline">بازگشت</a>
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                        ذخیره
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
