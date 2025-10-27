@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6">
        <div class="px-6 py-4 bg-gray-50 border-t flex flex-wrap justify-end gap-3">

            <a href="{{ route('admin.foods.restaurant', $restaurants->id) }}"
               class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition shadow-sm">
                🍽️ غذاها
            </a>

            <a href="{{ route('admin.restaurants.order', $restaurants->id) }}"
               class="flex items-center gap-2 px-4 py-2 bg-amber-600 text-white rounded-xl hover:bg-amber-700 transition shadow-sm">
                🛒 سفارش‌ها
            </a>

            <a href="{{ route('admin.restaurants.edit', $restaurants->id) }}"
               class="flex items-center gap-2 px-4 py-2 bg-yellow-400 text-white rounded-xl hover:bg-yellow-500 transition shadow-sm">
                ✏️ ویرایش
            </a>

            <form action="{{ route('admin.restaurants.destroy', $restaurants->id) }}" method="POST" onsubmit="return confirm('آیا مطمئن هستید؟');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="flex items-center gap-2 px-4 py-2 bg-red-500 text-white rounded-xl hover:bg-red-600 transition shadow-sm">
                    🗑️ حذف
                </button>
            </form>

        </div>

        <h2 class="text-3xl font-extrabold text-gray-800 mb-8 text-center">
            📘 جزئیات رستوران: {{ $restaurants->name }}
        </h2>

        <div class="bg-white/80 backdrop-blur-md shadow-xl rounded-2xl overflow-hidden border border-gray-200">
            <!-- هدر تصویر -->
            <div class="relative">
                <img src="{{ $restaurants->image ?? asset('images/default-class.jpg') }}"
                     alt="{{ $restaurants->name }}"
                     class="w-full h-72 object-cover">

                <div class="absolute bottom-0 w-full bg-gradient-to-t from-black/70 to-transparent p-4">
                    <h3 class="text-2xl font-bold text-white">{{ $restaurants->name }}</h3>
                </div>
            </div>

            <!-- اطلاعات -->
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-700">
                <div class="space-y-3">
                    <p><span class="font-semibold">آدرس:</span> {{ $restaurants->address ?? '---' }}</p>
                    <p><span class="font-semibold">درصد تخفیف رستوران:</span> {{ $restaurants->discount_percentage ?? '---' }}</p>
                    <p><span class="font-semibold"> زمان اماده سازی:</span> {{ $restaurants->grt_ready_minute ?? '---' }}</p>
                    <p><span class="font-semibold">روش ارسال:</span> {{ $restaurants->sending_way ?? '---' }}</p>
                </div>
                <div class="space-y-3">
                    <p><span class="font-semibold"> حداقل قیمت خرید:</span> {{ $restaurants->minimum_price ?? '---' }}</p>
                    <p><span class="font-semibold"> ساعت کاری:</span> {{ $restaurants->work_time ?? '---' }}</p>
                    <p><span class="font-semibold"> وضعیت:</span> {{ $restaurants->is_open? 'باز است' :'بسته است' }}</p>
                    <p><span class="font-semibold">کیلومتر ارسالی:</span> {{ $restaurants->delivery_radius_km?? '---' }}</p>

                </div>
            </div>

            <div class="p-6">
                <h3 class="text-xl font-bold mb-4">📍 موقعیت روی نقشه</h3>
                <div id="map" class="w-full h-96 rounded-2xl border"></div>
            </div>

{{--            <!-- دکمه‌ها -->--}}

        </div>

    </div>
@endsection
@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const lat = {{ $restaurants->latitude ?? 0 }};
            const lng = {{ $restaurants->longitude ?? 0 }};

            // ایجاد نقشه
            const map = L.map('map').setView([lat, lng], 15);

            // لایه‌ی کاشی (Tile Layer)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // اضافه کردن نشانگر (Marker)
            if (lat && lng) {
                L.marker([lat, lng])
                    .addTo(map)
                    .bindPopup("<b>{{ $restaurants->name }}</b><br>{{ $restaurants->address ?? '' }}")
                    .openPopup();
            } else {
                document.getElementById('map').innerHTML = "<p class='text-center text-red-600 mt-4'>مختصات ثبت نشده است.</p>";
            }
        });
    </script>
@endpush
