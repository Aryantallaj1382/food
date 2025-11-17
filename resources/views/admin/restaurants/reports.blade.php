@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6" dir="rtl">

        <h2 class="text-2xl font-bold mb-6 text-gray-800">
            گزارشات رستوران: {{ $restaurant->name }}
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- گزارش فروش بازه زمانی -->
            <a href="{{route('admin.restaurants.reports.sales', $restaurant->id)}}"
               class="block bg-white shadow-md p-6 rounded-xl border hover:shadow-lg transition text-center">
                <h3 class="text-xl font-bold text-indigo-700 mb-2">📊 گزارش فروش</h3>
                <p class="text-gray-600">نمایش فروش مجموعه در بازه زمانی دلخواه</p>
            </a>

            <!-- واریزی‌های خالص -->
            <a href="{{route('admin.restaurants.reports.payouts', $restaurant->id)}}"
               class="block bg-white shadow-md p-6 rounded-xl border hover:shadow-lg transition text-center">
                <h3 class="text-xl font-bold text-green-700 mb-2">💰 واریزی‌های خالص</h3>
                <p class="text-gray-600">ریز واریزی‌ها و تسویه حساب نهایی</p>
            </a>

            <!-- تعداد سفارش‌ها -->
            <a href="{{route('admin.restaurants.reports.orders_count', $restaurant->id)}}"
               class="block bg-white shadow-md p-6 rounded-xl border hover:shadow-lg transition text-center">
                <h3 class="text-xl font-bold text-blue-700 mb-2">🛒 تعداد سفارش‌ها</h3>
                <p class="text-gray-600">نمایش تعداد کل سفارش‌های ثبت شده</p>
            </a>

        </div>

    </div>
@endsection
