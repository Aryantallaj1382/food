@extends('layouts.app')
@php use Morilog\Jalali\Jalalian; @endphp


@section('content')
    <div class="p-6 bg-gray-100 min-h-screen">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div
            class="flex items-center justify-between px-6 py-4 border-b border-gray-300 bg-white mb-6 rounded-lg shadow">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">👤 جزئیات کاربر: {{ $user->name }}</h1>
            </div>

            <a href="{{ route('admin.users.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg shadow transition transform hover:scale-105">
                ← بازگشت
            </a>
        </div>

        <div class="mb-6 p-4 bg-white rounded-lg shadow text-center">
            <h2 class="text-xl font-semibold text-gray-800 mb-2">💰 کیف پول</h2>
            <p class="text-gray-600">موجودی: {{ number_format($user->wallet->balance ?? 0) }} تومان</p>
        </div>

            <div class="bg-white/80 backdrop-blur-md shadow-xl rounded-2xl border border-gray-200 overflow-hidden">
                @if($orders->count() > 0)
                    <table class="min-w-full text-sm text-gray-700">
                        <thead class="bg-gradient-to-r from-orange-700 to-orange-400 text-white text-center">
                        <tr>
                            <th class="py-3 px-4">#</th>
                            <th class="py-3 px-4">رستوران</th>
                            <th class="py-3 px-4">موبایل</th>
                            <th class="py-3 px-4">مبلغ کل</th>
                            <th class="py-3 px-4">روش پرداخت</th>
                            <th class="py-3 px-4">تاریخ</th>
                            <th class="py-3 px-4">وضعیت پرداخت</th>
                            <th class="py-3 px-4">عملیات</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-center bg-white">
                        @foreach($orders as $key => $order)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-3 px-4">{{ $orders->firstItem() + $key }}</td>
                                <td class="py-3 px-4">{{ $order->restaurant->name ?? '---' }}</td>
                                <td class="py-3 px-4">{{ $order->mobile ?? '---' }}</td>
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

            <div class="container py-6" dir="rtl">
                <h2 class="text-2xl font-bold mb-4">📍 آدرس‌های کاربر </h2>

                @if($address->count() > 0)
                    <div class="bg-white shadow rounded-lg p-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 table-auto">
                            <thead class="bg-gray-100">
                            <tr>
                                <th class="text-center px-4 py-2">#</th>
                                <th class="text-center px-4 py-2">آدرس کامل</th>
                                <th class="text-center px-4 py-2">نمایش روی نقشه</th>

                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 text-center">
                            @foreach($address as $key => $addr)
                                <tr class="h-12">
                                    <td class="px-4 py-2">{{ $key + 1 }}</td>
                                    <td class="px-4 py-2">{{ $addr->address ?? '---' }}</td>
                                    <td class="px-4 py-2">
                                        @if($addr->latitude && $addr->longitude)
                                            <button class="show-map-btn px-2 py-1 bg-blue-500 text-white rounded"
                                                    data-lat="{{ $addr->latitude }}"
                                                    data-lng="{{ $addr->longitude }}">
                                                نمایش روی نقشه
                                            </button>
                                        @else
                                            ---
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-gray-600 py-4">هیچ آدرسی ثبت نشده است.</p>
                @endif
            </div>

            <!-- مدال نقشه -->
            <div id="map-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg w-11/12 md:w-1/2 p-4 relative">
                    <button id="map-close" class="absolute top-2 right-2 px-3 py-1 bg-red-500 text-white rounded">✖</button>
                    <div id="map" class="w-full h-96 rounded"></div>
                </div>
            </div>




    </div>

    <script>
        document.querySelectorAll('.show-map-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const lat = parseFloat(this.dataset.lat);
                const lng = parseFloat(this.dataset.lng);

                // باز کردن مدال
                document.getElementById('map-modal').classList.remove('hidden');

                // ساخت نقشه
                const map = L.map('map').setView([lat, lng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                L.marker([lat, lng]).addTo(map)
                    .bindPopup("مکان کاربر")
                    .openPopup();
            });
        });

        // بستن مدال
        document.getElementById('map-close').addEventListener('click', function () {
            document.getElementById('map-modal').classList.add('hidden');
            document.getElementById('map').innerHTML = ''; // حذف نقشه قبلی
        });
    </script>

@endsection
