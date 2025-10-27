@php use Morilog\Jalali\Jalalian; @endphp
@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6">
        <h2 class="text-3xl font-extrabold text-gray-800 mb-8 text-center">
           جزئیات سفارش
        </h2>

        <div class="bg-white/80 backdrop-blur-md shadow-xl rounded-2xl border border-gray-200 overflow-hidden">
            @if($orders->count() > 0)
                <table class="min-w-full text-sm text-gray-700">
                    <thead class="bg-gradient-to-r from-orange-700 to-orange-400 text-white text-center">
                    <tr>
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">نام غذا</th>
                        <th class="py-3 px-4">نوع</th>
                        <th class="py-3 px-4">تعداد </th>
                        <th class="py-3 px-4">مبلغ </th>

                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-center bg-white">
                    @foreach($orders as $key => $order)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4">{{ $orders->firstItem() + $key }}</td>
                            <td class="py-3 px-4">{{ $order->option->food->name ?? '---' }}</td>
                            <td class="py-3 px-4">{{ $order->option->name ?? '---' }}</td>
                            <td class="py-3 px-4">{{ $order->quantity ?? '---' }}</td>
                            <td class="py-3 px-4 font-bold text-green-600">{{ number_format($order->price ?? 0) }}
                                تومان
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <!-- صفحه‌بندی -->
                <div class="p-4 bg-gray-50 border-t flex justify-center">
                    {{ $orders->links('pagination::tailwind') }}
                </div>
            @else
                <p class="text-center text-gray-600 py-8">هیچ سفارشی ثبت نشده است.</p>
            @endif
        </div>
    </div>
@endsection
