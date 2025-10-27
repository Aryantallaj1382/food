@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6">
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
        <div class="flex justify-start md:justify-end">
            <a href="{{ route('admin.restaurants.map') }}"

               class="bg-green-500 hover:bg-green-600 text-white p-2 rounded">
                رستوران ها روی نقشه
            </a>
        </div>
        <h2 class="text-2xl font-bold text-gray-700 mb-6">لیست رستوران ها</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($restaurants as $restaurant)
                <a href="{{ route('admin.restaurants.show', $restaurant->id) }}">

                <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">
                    <!-- عکس کلاس -->
                    <img src="{{ $restaurant->image ?? asset('images/default-class.jpg') }}"
                         alt="{{ $restaurant->name }}"
                         class="w-full h-40 object-cover">

                    <!-- اطلاعات -->
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
