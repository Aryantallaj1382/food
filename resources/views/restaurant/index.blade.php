@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6">

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

            {{-- فرم فیلتر و جستجو --}}
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

        {{-- ✅ لیست رستوران‌ها --}}
        <h2 class="text-2xl font-bold text-gray-700 mb-6">لیست رستوران‌ها</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($restaurants as $restaurant)
                <a href="{{ route('admin.restaurants.show', $restaurant->id) }}">
                    <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">
                        <img src="{{ $restaurant->image ?? asset('images/default-class.jpg') }}"
                             alt="{{ $restaurant->name }}"
                             class="w-full h-40 object-cover">
                        <div class="p-2">
                            <h3 class="text-lg text-center font-semibold text-gray-800 mb-2">
                                {{ $restaurant->name }}
                            </h3>
                        </div>
                    </div>
                </a>
            @empty
                <p class="col-span-4 text-center text-gray-500">هیچ رستورانی موجود نیست.</p>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $restaurants->links() }}
        </div>
    </div>
@endsection
