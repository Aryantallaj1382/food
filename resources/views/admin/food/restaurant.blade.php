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
            {{--            <a href="{{ route('admin.webinar.create') }}"--}}
            {{--               class="bg-green-500 hover:bg-green-600 text-white p-2 rounded">--}}
            {{--                ایجاد کلاس جدید--}}
            {{--            </a>--}}
        </div>
        <h2 class="text-2xl font-bold text-gray-700 mb-6">  لیست غذا های {{ $rest->name }} </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($foods as $food)
                <div class="bg-white rounded-xl shadow hover:shadow-lg transition overflow-hidden">
                    <!-- عکس کلاس -->
                    <img src="{{ $food->image ?? asset('images/default-class.jpg') }}"
                         alt="{{ $food->name }}"
                         class="w-full h-40 object-cover">

                    <!-- اطلاعات -->
                    <div class="p-1">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">
                            {{ $food->name }}
                        </h3>



                    </div>
                </div>
            @empty
                <p class="col-span-4 text-center text-gray-500">هیچ غذایی موجود نیست.</p>
            @endforelse
        </div>

        <div class="mt-6">

            {{ $foods->links() }}
        </div>

    </div>
@endsection
