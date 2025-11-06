@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto gri bg-white shadow-lg rounded-2xl p-8 mt-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center border-b pb-3">🍽️ ثبت رستوران جدید</h2>

        <form action="{{ route('admin.restaurants.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- نام -->
            <div>
                <label for="name" class="block text-gray-700 font-medium mb-2">نام رستوران</label>
                <input type="text" name="name" id="name" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>
            <!-- انتخاب کاربر -->
            <div>
                <label for="user_id" class="block text-gray-700 font-medium mb-2">انتخاب کاربر</label>
                <select name="user_id" id="user_id" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                    <option value="">انتخاب کنید</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->first_name }} ({{ $user->mobile ?? 'بدون شماره' }})</option>
                    @endforeach
                </select>
            </div>


            <!-- آدرس -->
            <div>
                <label for="address" class="block text-gray-700 font-medium mb-2">آدرس</label>
                <input type="text" name="address" id="address"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <!-- تصویر -->
            <div>
                <label for="image" class="block text-gray-700 font-medium mb-2">عکس رستوران</label>
                <input type="file" name="image" id="image" accept="image/*"
                       class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer focus:outline-none">
            </div>

            <!-- حداقل قیمت -->
            <div>
                <label for="minimum_price" class="block text-gray-700 font-medium mb-2">حداقل قیمت سفارش (تومان)</label>
                <input type="number" name="minimum_price" id="minimum_price" min="0"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <!-- زمان آماده‌سازی -->
            <div>
                <label for="grt_ready_minute" class="block text-gray-700 font-medium mb-2">زمان آماده‌سازی (دقیقه)</label>
                <input type="number" name="grt_ready_minute" id="grt_ready_minute" min="0"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <!-- نحوه ارسال -->
            <div>
                <label for="sending_way" class="block text-gray-700 font-medium mb-2">نحوه ارسال</label>
                <select name="sending_way" id="sending_way"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                    <option value="">انتخاب کنید</option>
                    <option value="پیک">پیک</option>
                    <option value="درب محل">تحویل در محل</option>
                </select>
            </div>

            <!-- قیمت ارسال -->
            <div>
                <label for="send_price" class="block text-gray-700 font-medium mb-2">هزینه ارسال (تومان)</label>
                <input type="number" name="send_price" id="send_price" min="0"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <!-- درصد تخفیف -->
            <div>
                <label for="discount_percentage" class="block text-gray-700 font-medium mb-2">درصد تخفیف</label>
                <input type="number" name="discount_percentage" id="discount_percentage" min="0" max="100"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <!-- دسته‌بندی‌ها -->
            <div>
                <label for="categories" class="block text-gray-700 font-medium mb-2">دسته‌بندی‌ها</label>
                <select name="categories[]" id="categories" multiple
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                <p class="text-sm text-gray-500 mt-1">برای انتخاب چند مورد، کلید Ctrl را نگه دارید.</p>
            </div>

            <!-- موقعیت مکانی روی نقشه -->
            <div>
                <label class="block text-gray-700 font-medium mb-2">📍 موقعیت روی نقشه</label>
                <div id="map" class="w-full h-80 rounded-xl border border-gray-300"></div>
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
                <p class="text-sm text-gray-500 mt-2">برای انتخاب موقعیت، روی نقشه کلیک کنید.</p>
            </div>

            <!-- دکمه ثبت -->
            <div class="pt-4">
                <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                    ✅ ثبت رستوران
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // موقعیت پیش‌فرض: سبزوار
            const defaultLat = 36.2140;
            const defaultLng = 57.6678;

            const map = L.map('map').setView([defaultLat, defaultLng], 13);

            // بارگذاری نقشه از OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

            // نشانگر (marker)
            let marker = L.marker([defaultLat, defaultLng]).addTo(map)
                .bindPopup("سبزوار").openPopup();

            // کلیک برای انتخاب موقعیت جدید
            map.on('click', function (e) {
                const { lat, lng } = e.latlng;

                if (marker) map.removeLayer(marker);
                marker = L.marker([lat, lng]).addTo(map)
                    .bindPopup("موقعیت انتخاب شد").openPopup();

                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
            });
        });
    </script>
@endpush
