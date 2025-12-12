@extends('layouts.app')

@section('title', 'مدیریت اسلایدرها')

@section('content')
    <div class="container mx-auto py-6" dir="rtl">
        <h1 class="text-3xl font-extrabold text-gray-800 mb-6 text-center">مدیریت اسلایدرها</h1>

        {{-- دکمه افزودن --}}
        <div class="mb-4 text-left">
            <a href="{{ route('admin.sliders.create') }}"
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                + افزودن اسلایدر جدید
            </a>
        </div>

        {{-- جدول --}}
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="w-full text-center border-collapse">
                <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="p-3">#</th>
                    <th class="p-3">تصویر</th>
                    <th class="p-3">لینک</th>
                    <th class="p-3">ترتیب</th>
                    <th class="p-3">عملیات</th>
                </tr>
                </thead>
                <tbody>
                @forelse($sliders as $slider)
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="p-3">{{ $slider->id }}</td>
                        <td class="p-3">
                            <img src="{{ $slider->image }}" class="w-24 h-16 object-cover rounded-lg mx-auto">
                        </td>
                        <td class="p-3 text-blue-600">{{ $slider->link ?? '---' }}</td>
                        <td class="p-3">{{ $slider->order }}</td>
                        <td class="p-3 flex justify-center gap-3">
                            <a href="{{ route('admin.sliders.edit', $slider->id) }}" class="px-2 py-1 bg-blue-600 text-white rounded">ویرایش</a>

                            <form action="{{ route('admin.sliders.destroy', $slider) }}" method="POST"
                                  onsubmit="return confirm('آیا مطمئنی؟')">
                                @csrf
                                @method('DELETE')
                                <button class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                                    حذف
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-6 text-gray-500">هیچ اسلایدری یافت نشد</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- صفحه‌بندی --}}
        <div class="mt-6">
            {{ $sliders->links('pagination::tailwind') }}
        </div>
    </div>
@endsection
