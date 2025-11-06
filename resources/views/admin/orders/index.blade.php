@extends('layouts.app')
@php use Morilog\Jalali\Jalalian; @endphp

@section('title', 'مدیریت سفارش‌ها')

@section('content')
    <div class="container py-6">
        <h1 class="text-2xl font-semibold mb-4">لیست سفارش‌ها</h1>

        <form class="mb-4 flex gap-2" method="GET" action="{{ route('admin.orders.index') }}">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="جستجو با id، شماره پیگیری یا نام کاربر"
                   class="border p-2 rounded w-1/3">
            <select name="status" class="border p-2 rounded">
                <option value="">همه وضعیت‌ها</option>
                <option value="pending" @if(request('status')=='pending') selected @endif>در انتظار تایید</option>
                <option value="processing" @if(request('status')=='processing') selected @endif>در حال اماده سازی
                </option>
                <option value="completed" @if(request('status')=='completed') selected @endif>تحویل داده شده</option>
                <option value="cancelled" @if(request('status')=='cancelled') selected @endif>لغو‌شده</option>
            </select>

            <select name="payment_status" class="border p-2 rounded">
                <option value="">وضعیت پرداخت</option>
                <option value="paid" @if(request('payment_status')=='paid') selected @endif>پرداخت‌شده</option>
                <option value="failed" @if(request('payment_status')=='failed') selected @endif>پرداخت‌نشده</option>
                <option value="pending" @if(request('payment_status')=='pending') selected @endif>در انتظار</option>
            </select>

            <button class="px-4 py-2 bg-blue-600 text-white rounded">فیلتر</button>
            <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 bg-gray-200 rounded">بازنشانی</a>
        </form>

        <div class="bg-white/80 backdrop-blur-md shadow-xl rounded-2xl border border-gray-200 overflow-x-auto">
            @if($orders->count() > 0)
                <table class="min-w-full text-sm text-gray-700">
                    <thead class="bg-gradient-to-r from-orange-700 to-orange-400 text-white text-center">
                    <tr class="*:whitespace-nowrap">
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">رستوران</th>
                        <th class="py-3 px-4">کاربر</th>
                        <th class="py-3 px-4">موبایل</th>
                        <th class="py-3 px-4">مبلغ کل</th>
                        <th class="py-3 px-4">روش پرداخت</th>
                        <th class="py-3 px-4">تاریخ</th>
                        <th class="py-3 px-4">وضعیت </th>
                        <th class="py-3 px-4">وضعیت پرداخت</th>
                        <th class="py-3 px-4">توضیحات</th>
                        <th class="py-3 px-4">عملیات</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-center bg-white">
                    @foreach($orders as $key => $order)
                        <tr class="hover:bg-gray-50 transition *:whitespace-nowrap">
                            <td class="py-3 px-4">{{ $orders->firstItem() + $key }}</td>
                            <td class="py-3 px-4">{{ $order->restaurant->name ?? '---' }}</td>
                            <td class="py-3 px-4">{{ $order->user->name ?? '---' }}</td>
                            <td class="py-3 px-4">{{ $order->user->mobile ?? '---' }}</td>
                            <td class="py-3 px-4 font-bold text-green-600">{{ number_format($order->total_price ?? 0) }}
                                تومان
                            </td>
                            <td class="py-3 px-4">
                                @if($order->payment_method === 'cache')
                                    <span
                                        class="  px-3 py-1 rounded-full text-xs font-semibold">نقدی</span>
                                @else
                                    <span class=" px-3 py-1 rounded-full text-xs font-semibold">آنلاین</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">{{ Jalalian::fromDateTime($order->created_at)->format('Y/m/d H:i') }}</td>
                            <td class="py-3 px-4">{{ $order->status_fa }}</td>

                            <td class="py-3 px-4">
                                @if($order->payment_status === 'paid')
                                    <span
                                        class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">پرداخت شده</span>
                                @elseif($order->payment_status === 'pending')
                                    <span
                                        class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-semibold">در انتظار</span>
                                @else
                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">ناموفق</span>
                                @endif
                            </td>
                            <td>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold">
                                    {{ \Illuminate\Support\Str::limit($order->notes, 20, '...') }}
                                </span>
                            </td>

                            <td class="py-3 px-4">
                                <a href="{{route('admin.restaurants.items',$order->id)}}"
                                   class="px-3 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition text-xs">
                                    جزئیات
                                </a>
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

        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>
@endsection
