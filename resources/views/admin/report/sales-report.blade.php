@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6" dir="rtl">

        <h2 class="text-2xl font-bold mb-6 text-gray-800">
            گزارش فروش ({{ $restaurant->name }})
        </h2>

        <!-- فیلتر تاریخ -->
        <form method="GET" class="bg-white p-4 rounded-lg shadow mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">

            <div>
                <label class="block mb-1 text-gray-700 font-medium">از تاریخ</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block mb-1 text-gray-700 font-medium">تا تاریخ</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div class="flex items-end">
                <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                    فیلتر
                </button>
            </div>
        </form>

        <!-- مجموع فروش -->
        <div class="bg-green-100 text-green-800 p-4 rounded-lg shadow mb-6 text-lg font-bold">
            مجموع فروش: {{ number_format($totalSales) }} تومان
        </div>

        <!-- لیست سفارش‌ها -->
        <div class="bg-white shadow rounded-lg overflow-x-auto">
            <table class="min-w-full border">
                <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border">شماره سفارش</th>
                    <th class="px-4 py-2 border">تاریخ</th>
                    <th class="px-4 py-2 border">مبلغ</th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border text-center">{{ $order->id }}</td>
                        <td class="px-4 py-2 border text-center">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-2 border text-center font-bold text-indigo-700">
                            {{ number_format($order->sum_items) }} تومان
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-6 text-gray-500">
                            هیچ سفارشی یافت نشد.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection
