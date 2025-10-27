@extends('layouts.app')

@section('content')
    <div class="p-6">
        <h3 class="text-xl font-bold mb-4">📍 موقعیت تمام رستوران‌ها</h3>
        <div id="map" class="w-full h-[600px] rounded-2xl border"></div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // گرفتن لیست رستوران‌ها از PHP
            const restaurants = @json($restaurants);

            // اگر داده‌ای نباشد
            if (restaurants.length === 0) {
                document.getElementById('map').innerHTML = "<p class='text-center text-red-600 mt-4'>هیچ رستورانی ثبت نشده است.</p>";
                return;
            }

            // مقدار پیش‌فرض برای مرکز نقشه (اولین رستوران)
            const map = L.map('map').setView([restaurants[0].latitude || 0, restaurants[0].longitude || 0], 13);

            // لایه‌ی نقشه
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // گروه مارکرها برای تنظیم بهتر محدوده‌ی نمایش
            const markers = [];

            // اضافه کردن همه‌ی مارکرها
            restaurants.forEach((r) => {
                if (r.latitude && r.longitude) {
                    const marker = L.marker([r.latitude, r.longitude])
                        .addTo(map)
                        .bindPopup(`<b>${r.name}</b><br>${r.address ?? ''}`);
                    markers.push(marker);
                }
            });

            // تنظیم نقشه بر اساس محدوده‌ی تمام مارکرها
            if (markers.length > 0) {
                const group = L.featureGroup(markers);
                map.fitBounds(group.getBounds().pad(0.3));
            }
        });
    </script>
@endpush
