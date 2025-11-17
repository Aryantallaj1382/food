@extends('layouts.app')

@section('content')
    <div class="container py-4" dir="rtl">

        <h2 class="text-xl font-bold mb-4">تعداد سفارشات (رستوران شماره {{ $id }})</h2>

        {{-- فیلترها --}}
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

            {{-- از تاریخ --}}
            <div>
                <label class="block mb-1 text-gray-700 font-medium">از تاریخ (میلادی)</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            {{-- تا تاریخ --}}
            <div>
                <label class="block mb-1 text-gray-700 font-medium">تا تاریخ (میلادی)</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            {{-- نوع پرداخت --}}
            <div>
                <label class="block mb-1 text-gray-700 font-medium">نوع پرداخت</label>
                <select name="payment_method" class="w-full border rounded px-3 py-2">
                    <option value="">همه</option>
                    <option value="online" {{ request('payment_method')=='online' ? 'selected' : '' }}>آنلاین</option>
                    <option value="cash" {{ request('payment_method')=='cash' ? 'selected' : '' }}>نقدی</option>
                </select>
            </div>

            <div class="flex items-end">
                <button class="px-4 py-2 bg-indigo-600 text-white rounded">جستجو</button>
            </div>

        </form>

        {{-- تعداد کل --}}
        <div class="p-4 bg-blue-100 border rounded-lg mb-4 text-lg font-bold">
            تعداد کل سفارش‌ها:
            <span class="text-blue-700">{{ number_format($ordersCount) }}</span>
        </div>

        {{-- جدول سفارش‌ها --}}
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                <tr class="bg-gray-200">
                    <th class="p-2 border">#</th>
                    <th class="p-2 border">مبلغ</th>
                    <th class="p-2 border">نوع پرداخت</th>
                    <th class="p-2 border">تاریخ</th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td class="p-2 border">{{ $order->id }}</td>
                        <td class="p-2 border">{{ number_format($order->total_amount) }} تومان</td>
                        <td class="p-2 border">
                            @if($order->payment_method == 'online')
                                <span class="text-green-600 font-bold">آنلاین</span>
                            @else
                                <span class="text-orange-600 font-bold">نقدی</span>
                            @endif
                        </td>
                        <td class="p-2 border">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-2 text-center text-gray-500">سفارشی یافت نشد.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $orders->links() }}
        </div>

    </div>
@endsection
