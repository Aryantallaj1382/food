@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto px-6 py-10">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center border-b pb-3">
            ثبت تراکنش بستانکار برای رستوران: {{ $restaurant->name }}
        </h2>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.credit.store', $restaurant->id) }}" method="POST" class="bg-white p-6 rounded-xl shadow-md border border-gray-200 space-y-4">
            @csrf

            <div>
                <label class="block text-gray-700 mb-2">مبلغ (تومان)</label>
                <input type="number" name="amount" value="{{ old('amount') }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-gray-700 mb-2">توضیحات</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none">{{ old('description') }}</textarea>
                @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-gray-700 mb-2">کد تراکنش</label>
                <input type="text" name="tracking_code" value="{{ old('tracking_code') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                @error('tracking_code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-center gap-3 mt-4">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                    ثبت تراکنش
                </button>
                <a href="{{ route('admin.restaurants.show', $restaurant->id) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                    بازگشت
                </a>
            </div>
        </form>
    </div>
@endsection
