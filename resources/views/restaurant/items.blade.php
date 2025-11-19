@php use Morilog\Jalali\Jalalian; @endphp
@extends('layouts.app')

@section('content')
    <h2 class="text-3xl font-extrabold text-gray-800 mb-8 text-center">
        جزئیات سفارش
    </h2>

    <h4 class="text-3xl font-extrabold text-gray-800 mb-8 text-center">
        {{ $order->restaurant->name ?? '---' }}
    </h4>
    {{-- 🔸 جدول جزئیات سفارش --}}

    <div class="container mx-auto p-6">
        <div class="bg-white/80 backdrop-blur-md shadow-xl rounded-2xl border mb-6 border-gray-200 overflow-hidden">
            @if($orders->count() > 0)
                <table class="min-w-full text-sm text-gray-700">
                    <thead class="bg-gradient-to-r from-orange-700 to-orange-400 text-white text-center">
                    <tr>
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">نام غذا</th>
                        <th class="py-3 px-4">نوع</th>
                        <th class="py-3 px-4">تعداد</th>
                        <th class="py-3 px-4">مبلغ</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-center bg-white">
                    @foreach($orders as $key => $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4">{{ $orders->firstItem() + $key }}</td>
                            <td class="py-3 px-4">{{ $item->option->food->name ?? '---' }}</td>
                            <td class="py-3 px-4">{{ $item->option->name ?? '---' }}</td>
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
                    <span class="font-semibold text-gray-600">شماره سفارش:</span>
                    <span class="text-gray-800">{{ $order->id }}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600">موبایل رستوران:</span>
                    <span class="text-gray-800">{{ $order->restaurant?->user?->mobile }}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600">وضعیت کاربر:</span>

                    @if($order->user->is_blocked)
                        <span class="text-red-600 font-bold">بلاک شده</span>
                    @else
                        <span class="text-green-600 font-bold">فعال</span>
                    @endif
                </div>

                <div>
                    <span class="font-semibold text-gray-600">یادداشت سفارش:</span>
                    <span class="text-gray-800">{{ $order->notes}}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600">مبلغ کل:</span>
                    <span class="text-green-600 font-bold">{{ number_format($order->total_amount ?? 0) }} تومان</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600">موبایل کاربر:</span>
                    <span class="text-gray-800">{{ $order->mobile }}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600">هزینه ارسال:</span>
                    <span class="text-gray-800">{{ $order->send_price ?? '---' }}</span>
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
                    <span class="font-semibold text-gray-600">تغییر وضعیت:</span>

                    <div class="inline-block relative">
                        <button class="px-3 py-1.5 bg-gray-200 text-gray-800 text-xs rounded-lg hover:bg-gray-300 transition">
                            تغییر وضعیت
                        </button>
                        <ul class="absolute hidden bg-white shadow-lg rounded-lg w-40 mt-1 text-sm z-50">
                            <li class="px-4 py-2 hover:bg-orange-100 cursor-pointer" onclick="changeOrderStatus({{ $order->id }}, 'pending')">در انتظار تایید</li>
                            <li class="px-4 py-2 hover:bg-blue-100 cursor-pointer" onclick="changeOrderStatus({{ $order->id }}, 'processing')">در حال آماده‌سازی</li>
                            <li class="px-4 py-2 hover:bg-emerald-100 cursor-pointer" onclick="changeOrderStatus({{ $order->id }}, 'completed')">در انتظار پیک</li>
                            <li class="px-4 py-2 hover:bg-red-100 cursor-pointer" onclick="changeOrderStatus({{ $order->id }}, 'cancelled')">لغو شده</li>
                            <li class="px-4 py-2 hover:bg-red-100 cursor-pointer" onclick="changeOrderStatus({{ $order->id }}, 'delivery')">تحویل پیک</li>
                            <li class="px-4 py-2 hover:bg-red-100 cursor-pointer" onclick="changeOrderStatus({{ $order->id }}, 'rejected')">رد شده</li>
                        </ul>
                    </div>
                </div>
                <div>
                    <span class="font-semibold text-gray-600">ساعت درخواستی:</span>
                    <span class="text-gray-800">{{ $order->time ?? 'سریع ترین زمان' }}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600">روش ارسال:</span>
                    <span class="text-gray-800">{{ $order->sending_method_fa }}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600">روش پرداخت:</span>
                    <span class="text-gray-800">{{ $order->payment_method_fa }}</span>
                </div>
                <div>
                    <a href="{{ route('admin.users.show',$order->user->id) }}"
                       class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                        نمایش تراکنش های کاربر                </a>
                </div>

            </div>
        </div>

        @if($order->adress)
            <div class="bg-white shadow-md rounded-2xl p-6 mb-6 border border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 mb-3">آدرس سفارش</h3>
                <div class="grid grid-cols-2">

                    <div class="mb-4">
                        <span class="font-semibold text-gray-600">آدرس:</span>
                        <span class="text-gray-800">{{ $order->adress->address }}</span>
                    </div>

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
        <div class="bg-white shadow-md rounded-2xl p-6 mb-6 border border-gray-200">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">

                <div>
                    <span class="font-semibold text-gray-600">نام مشتری:</span>
                    <span class="text-gray-800">{{ $order->user->name ?? '---' }}</span>
                </div>

                <div>
                    <span class="font-semibold text-gray-600">درگاه پرداخت:</span>
                    <span class="text-gray-800">{{ $order->gateway_fa }}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600">کد تخفیف:</span>
                    <span class="text-gray-800">{{ $order->discount_code ?? 'ندارد' }}</span>
                </div>

                <div>
                    <span class="font-semibold text-gray-600">تلفن ثابت:</span>
                    <span class="text-gray-800">{{ $order->phone }}</span>
                </div>


                <div>
                    <span class="font-semibold text-gray-600">وضعیت پرداخت:</span>
                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                    @if($order->payment_status == 'paid') bg-green-500 text-white
                    @elseif($order->payment_status == 'unpaid') bg-red-500 text-white
                    @else bg-gray-400 text-white
                    @endif">
                    {{ $order->payment_status_fa ?? $order->payment_status }}
                </span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600">تاریخ ثبت:</span>
                    <span class="text-gray-800">
                    {{ Jalalian::forge($order->created_at)->format('Y/m/d H:i') }}
                </span>
                </div>

            </div>
        </div>


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

        // تغییر وضعیت با AJAX و ریلود صفحه
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
