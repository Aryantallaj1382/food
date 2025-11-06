@extends('layouts.app')

@section('title', 'مدیریت دسته‌بندی‌ها')

@section('content')
    <div class="container mx-auto py-6" dir="rtl">
        <h1 class="text-3xl font-extrabold text-gray-800 mb-6 text-center">📂 لیست دسته‌بندی‌ها</h1>

        <a href="{{ route('admin.categories.create') }}"
           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg mb-4 inline-block">
            ➕ افزودن دسته‌بندی جدید
        </a>

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="w-full text-center border-collapse">
                <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="p-3">#</th>
                    <th class="p-3">نام</th>
                    <th class="p-3">اسلاگ</th>
                    <th class="p-3">آیکون</th>
                    <th class="p-3">تاریخ ایجاد</th>
                    <th class="p-3">عملیات</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($categories as $category)
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="p-3">{{ $category->id }}</td>
                        <td class="p-3 font-semibold">{{ $category->name }}</td>
                        <td class="p-3 text-gray-600">{{ $category->slug }}</td>
                        <td class="p-3 text-gray-700">
                            @if($category->icon)
                                <img src="{{ $category->icon }}" class="w-10 h-10 object-cover rounded-full mx-auto">
                            @else
                                ---
                            @endif
                        </td>

                        <td class="p-3 text-gray-500">
                            {{ \Morilog\Jalali\Jalalian::forge($category->created_at)->format('Y/m/d') }}
                        </td>
                        <td class="p-3 text-center">
                            <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('آیا مطمئن هستید که می‌خواهید این دسته را حذف کنید؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                                    حذف
                                </button>
                            </form>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-6 text-gray-500">هیچ دسته‌بندی‌ای یافت نشد.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $categories->links('pagination::tailwind') }}
        </div>
    </div>
@endsection
