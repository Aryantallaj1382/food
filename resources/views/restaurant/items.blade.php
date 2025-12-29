@php use Morilog\Jalali\Jalalian; @endphp
@extends('layouts.app')

@section('content')
    <h2 class="text-3xl font-extrabold text-gray-800 mb-8 text-center">
        جزئیات سفارش
    </h2>

    <h4 class="text-3xl font-extrabold text-gray-800 mb-8 text-center">
        {{ $order->restaurant->name ?? '---' }}
    </h4>
    <span class="flex flex-wrap items-center gap-3 text-center ">
    {{-- شماره موبایل اصلی --}}
        @if($order->restaurant->mobile)
            <a href="tel:{{ $order->restaurant->mobile }}"
               class="flex items-center gap-2 px-4 py-2 bg-green-100 text-green-800 rounded-full hover:bg-green-200 transition font-medium shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
            </svg>
            {{ $order->restaurant->mobile }}
        </a>
        @endif

        {{-- شماره موبایل دوم (mobile3) --}}
        @if($order->restaurant->mobile3)
            <a href="tel:{{ $order->restaurant->mobile3 }}"
               class="flex items-center gap-2 px-4 py-2 bg-blue-100 text-blue-800 rounded-full hover:bg-blue-200 transition font-medium shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            {{ $order->restaurant->mobile3 }}
        </a>
        @endif

        {{-- تلفن ثابت --}}
        @if($order->restaurant->phone)
            <a href="tel:{{ $order->restaurant->phone }}"
               class="flex items-center gap-2 px-4 py-2 bg-purple-100 text-purple-800 rounded-full hover:bg-purple-200 transition font-medium shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
            </svg>
            {{ $order->restaurant->phone }}
        </a>
        @endif

        {{-- اگر هیچ شماره‌ای نبود --}}
        @if(!$order->restaurant->mobile && !$order->restaurant->mobile3 && !$order->restaurant->phone)
            <span class="text-gray-500 italic">شماره تماسی ثبت نشده</span>
        @endif
</span>
    {{-- 🔸 جدول جزئیات سفارش --}}

    <div class="container mx-auto p-6 text-center ">
        <div class="bg-white/80 backdrop-blur-md shadow-xl rounded-2xl border mb-6 border-gray-200 overflow-hidden">
            @if($orders->count() > 0)
                <table class="min-w-full text-sm text-gray-700">
                    <thead class="bg-gradient-to-r from-orange-700 to-orange-400 text-white text-center">
                    <tr>
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">نام غذا</th>
                        <th class="py-3 px-4">تعداد</th>
                        <th class="py-3 px-4">مبلغ</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-center bg-white">
                    @foreach($orders as $key => $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4">{{ $orders->firstItem() + $key }}</td>
                            <td class="py-3 px-4">{{ $item->option->food->name .' '. $item->option->name ?? '---' }}</td>
                            <td class="py-3 px-4">{{ $item->quantity ?? '---' }}</td>
                            <td class="py-3 px-4 font-bold text-green-600">{{ number_format($item->price ?? 0) }} تومان</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div class="p-4 bg-gray-50 border-t flex justify-center">
                    {{ $orders->links('pagination::tailwind') }}
                </div>
            @else
                <p class="text-center text-gray-600 py-8">هیچ سفارشی ثبت نشده است.</p>
            @endif
        </div>

        {{-- 🔸 آدرس سفارش --}}
        <div class="bg-white shadow-md rounded-2xl p-6 mb-6 border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="font-semibold text-gray-600">توضیحات سفارش:</span>
                    <span class="text-gray-800">{{ $order->notes}}</span>
                </div>
                <div>

                </div>
                <div>
                    <span class="font-semibold text-gray-600">تاریخ ثبت:</span>
                    <span class="text-gray-800">
                    {{ Jalalian::forge($order->created_at)->format('Y/m/d H:i') }}
                </span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600">جمع اقلام:</span>
                    <span class="text-green-600 font-bold">{{ number_format($order->total_price ?? 0) }} تومان</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600">شماره سفارش:</span>
                    <span class="text-gray-800">{{ $order->id }}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600">تخفیف:</span>
                    <span class="text-green-600 font-bold"></span>
                </div>

                <div>
                    <span class="font-semibold text-gray-600">وضعیت سفارش:</span>
                    <span class="px-2 py-1 rounded-full text-white text-xs font-semibold
                    @switch($order->status)
                        @case('pending') bg-yellow-500 @break
                        @case('processing') bg-blue-500 @break
                        @case('completed') bg-green-600 @break
                        @case('cancelled') bg-red-500 @break
                        @default bg-gray-400
                    @endswitch">
                    {{ $order->status_fa ?? $order->status }}
                </span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600">هزینه ارسال:</span>
                    <span class="text-gray-800">{{ $order->send_price ?? '---' }}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600">نحوه پرداخت:</span>
                    <span class="text-green-600 font-bold"> </span>
                </div>

                <div>
                    <span class="font-semibold text-gray-600">مبلغ کل:</span>
                    <span class="text-green-600 font-bold">{{ number_format($order->total_amount ?? 0) }} تومان</span>
                </div>

                <div>
                    <span class="font-semibold text-gray-600">نام مشتری:</span>
                    <span class="text-gray-800">{{ $order->user->name ?? '---' }}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600">مجموع سفارش های مشتری:</span>
                    <span class="text-gray-800">{{ number_format($total_amount) ?? '---' }}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600">موبایل  کاربر:</span>
                    <span class="text-gray-800">{{ $order->user?->mobile }}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600">موجودی کاربر:</span>
                    <span class="text-gray-800">{{ $order->user?->wallet?->balance  }}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600">موبایل 2 کاربر:</span>
                    <span class="text-gray-800">{{ $order->user?->phone }}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600">موجودی کاربر:</span>
                    <span class="text-gray-800">{{ $order->user?->wallet?->balance  }}</span>
                </div>
                <div class="mb-4">
                    <span class="font-semibold text-gray-600">آدرس:</span>
                    <span class="text-gray-800">{{ $order->adress->address }}</span>
                </div>

                <div>
                    <span class="font-semibold text-gray-600">ساعت تحوسل:</span>
                    {{ Jalalian::forge($order->updated_at)->format('H:i') }}
                </div>


                <div>
                    <a href="{{ route('admin.users.show',$order->user->id) }}"
                       class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                        نمایش تراکنش های کاربر                </a>
                </div>

            </div>
            <div>
                <span class="font-semibold text-gray-600">توضیحات مدیر:</span>
                <span class="text-gray-800">{{ $order->admin_note}}</span>
            </div>

        </div>
        @if($order->status === 'pending' || $order->payment_status === 'pending' )
            <div class="bg-white shadow-md rounded-2xl p-6 mb-6 border border-gray-200">
                <div class="grid grid-cols-1 gap-4 text-sm">
                    <div>
                        <div class="mt-2 flex space-x-0 space-x-reverse rtl:space-x-reverse"> <!-- مهم برای زبان‌های RTL -->
                            <button
                                class="w-1/2 px-6 py-3 bg-green-500 text-white text-lg rounded-lg hover:bg-green-600 transition"
                                onclick="changeOrderStatus({{ $order->id }}, 'processing')">
                                تایید
                            </button>

                            <button
                                class="w-1/2 px-6 py-3 bg-red-500 text-white text-lg rounded-lg hover:bg-red-600 transition"
                                onclick="changeOrderStatus({{ $order->id }}, 'rejected')">
                                کنسل
                            </button>

                        </div>
                    </div>
                </div>
            </div>

        @endif
        @if($order->adress)
            <div class="bg-white shadow-md rounded-2xl p-6 mb-6 border border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 mb-3">آدرس سفارش</h3>
                <div class="grid grid-cols-2">



                    <div id="map" class="w-full h-72 rounded-xl border border-gray-300"></div>

                </div>
            </div>

            {{-- نمایش نقشه با Leaflet (اپن سورس و رایگان) --}}
            @push('scripts')
                <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        const lat = {{ $order->adress->latitude ?? '35.6892' }}; // پیش‌فرض تهران
                        const lng = {{ $order->adress->longitude ?? '51.3890' }};
                        const map = L.map('map').setView([lat, lng], 15);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '&copy; OpenStreetMap'
                        }).addTo(map);

                        L.marker([lat, lng]).addTo(map)
                            .bindPopup("آدرس سفارش: {{ $order->adress->address }}")
                            .openPopup();
                    });
                </script>
            @endpush
        @endif

        {{-- 🔸 اطلاعات کلی سفارش --}}


    </div>
    <script>

        document.querySelectorAll('.inline-block.relative > button').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation(); // جلوگیری از بسته شدن خودکار
                const ul = this.nextElementSibling;
                ul.classList.toggle('hidden');
            });
        });

        // بستن منو وقتی بیرون کلیک شد
        document.addEventListener('click', function() {
            document.querySelectorAll('.inline-block.relative ul').forEach(ul => {
                ul.classList.add('hidden');
            });
        });

        function changeOrderStatus(orderId, status) {
            axios.patch(`/admin/order/orders/${orderId}/status`, { status: status })
                .then(response => {
                    alert('وضعیت سفارش با موفقیت تغییر کرد.');
                    location.reload();
                })
                .catch(err => {
                    alert('خطا در تغییر وضعیت. دوباره تلاش کنید.');
                    console.error(err);
                });
        }
    </script>
@endsection
