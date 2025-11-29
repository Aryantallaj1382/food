@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6" dir="rtl">

        {{-- ✅ پیام موفقیت --}}
        @if(session('success'))
            <div
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 3000)"
                class="mb-4 p-4 rounded-lg bg-green-100 border border-green-400 text-green-700 flex justify-between items-center"
            >
                <span>{{ session('success') }}</span>
                <button @click="show = false" class="text-green-700 hover:text-green-900 font-bold">&times;</button>
            </div>
        @endif

        {{-- ✅ بالای صفحه: فیلتر + دکمه‌ها --}}
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.restaurants.map') }}"
                   class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                    📍 نمایش روی نقشه
                </a>

                <a href="{{ route('admin.restaurants.create') }}"
                   class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    ➕ ثبت رستوران جدید
                </a>
            </div>

            {{-- فرم فیلتر --}}
            <form method="GET" action="{{ route('admin.restaurants.index') }}" class="flex flex-wrap items-center gap-2">
                <input type="text" name="search" placeholder="جستجو بر اساس نام رستوران"
                       value="{{ request('search') }}"
                       class="border rounded-lg p-2 focus:ring focus:ring-green-200">

                <select name="category_id" class="border rounded-lg p-2 focus:ring focus:ring-green-200">
                    <option value="">همه دسته‌بندی‌ها</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit"
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                    فیلتر
                </button>

                @if(request()->has('search') || request()->has('category_id'))
                    <a href="{{ route('admin.restaurants.index') }}"
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-3 py-2 rounded">
                        حذف فیلتر
                    </a>
                @endif
            </form>
        </div>

        {{-- ✅ لیست رستوران‌ها بصورت جدول --}}
        <h2 class="text-2xl font-bold text-gray-700 mb-6">لیست رستوران‌ها</h2>

        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <table class="min-w-full text-sm text-gray-700 border-collapse">
                <thead class="bg-gray-100 border-b text-gray-700">
                <tr>
                    <th class="py-3 px-4 text-center">#</th>
                    <th class="py-3 px-4 text-center">عکس</th>
                    <th class="py-3 px-4 text-center">نام رستوران</th>
                    <th class="py-3 px-4 text-center">دسته‌بندی</th>
                    <th class="py-3 px-4 text-center">آدرس</th>
                    <th class="py-3 px-4 text-center">موبایل</th>
                    <th class="py-3 px-4 text-center">وضعیت</th>
                    <th class="py-3 px-4 text-center">عملیات</th>
                </tr>
                </thead>

                <tbody>
                @forelse($restaurants as $restaurant)
                    <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition">

                        {{-- شماره --}}
                        <td class="py-3 px-4 text-center">
                            {{ $loop->iteration + ($restaurants->currentPage() - 1) * $restaurants->perPage() }}
                        </td>

                        {{-- عکس --}}
                        <td class="py-3 px-4 text-center">
                            <img src="{{ $restaurant->image ?? asset('images/default-class.jpg') }}"
                                 class="w-16 h-16 rounded object-cover mx-auto shadow">
                        </td>

                        {{-- نام --}}
                        <td class="py-3 px-4 text-center font-semibold">
                            {{ $restaurant->name }}
                        </td>

                        {{-- دسته‌بندی --}}
                        <td class="py-3 px-4 text-center">
                            @if($restaurant->categories->count())
                                @foreach($restaurant->categories as $category)
                                    {{ $category->name }}{{ !$loop->last ? '، ' : '' }}
                                @endforeach
                            @else
                                ---
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center font-semibold">
                            {{ \Illuminate\Support\Str::limit($restaurant->address, 30, '...') }}
                        </td>
                        <td class="py-3 px-4 text-center font-semibold">
                            {{ $restaurant?->user?->mobile ?? '---' }}
                        </td>
                        <td class="py-3 px-4 text-center font-semibold">
                            {{ $restaurant?->is_open ? 'باز است' : 'بسته است'}}
                        </td>

                        {{-- عملیات --}}
                        <td class="py-3 px-4 text-center">
                            <a href="{{ route('admin.restaurants.edit', $restaurant->id) }}"
                               class="text-blue-600 hover:text-blue-800 font-bold">
                                مشاهده
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-gray-500">
                            هیچ رستورانی یافت نشد.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- ✅ صفحه‌بندی --}}
        <div class="mt-6">
            {{ $restaurants->links() }}
        </div>
    </div>
@endsection
