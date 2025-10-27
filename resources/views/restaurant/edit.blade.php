@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-2xl p-8 mt-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center border-b pb-3">✏️ ویرایش رستوران</h2>

        <form action="{{ route('admin.restaurants.update', $restaurant->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- نام -->
            <div>
                <label for="name" class="block text-gray-700 font-medium mb-2">نام رستوران</label>
                <input type="text" name="name" id="name" required
                       value="{{ old('name', $restaurant->name) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <!-- آدرس -->
            <div>
                <label for="address" class="block text-gray-700 font-medium mb-2">آدرس</label>
                <input type="text" name="address" id="address"
                       value="{{ old('address', $restaurant->address) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <!-- تصویر -->
            <div>
                <label for="image" class="block text-gray-700 font-medium mb-2">عکس رستوران</label>
                <input type="file" name="image" id="image" accept="image/*"
                       class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer focus:outline-none">
                @if($restaurant->image)
                    <img src="{{ $restaurant->image }}" alt="تصویر قبلی" class="w-48 h-32 object-cover rounded mt-2">
                @endif
            </div>

            <!-- حداقل قیمت -->
            <div>
                <label for="minimum_price" class="block text-gray-700 font-medium mb-2">حداقل قیمت سفارش (تومان)</label>
                <input type="number" name="minimum_price" id="minimum_price" min="0"
                       value="{{ old('minimum_price', $restaurant->minimum_price) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <!-- زمان آماده‌سازی -->
            <div>
                <label for="grt_ready_minute" class="block text-gray-700 font-medium mb-2">زمان آماده‌سازی (دقیقه)</label>
                <input type="number" name="grt_ready_minute" id="grt_ready_minute" min="0"
                       value="{{ old('grt_ready_minute', $restaurant->grt_ready_minute) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <!-- نحوه ارسال -->
            <div>
                <label for="sending_way" class="block text-gray-700 font-medium mb-2">نحوه ارسال</label>
                <select name="sending_way" id="sending_way"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                    <option value="">انتخاب کنید</option>
                    <option value="پیک" {{ old('sending_way', $restaurant->sending_way) == 'پیک' ? 'selected' : '' }}>پیک</option>
                    <option value="درب محل" {{ old('sending_way', $restaurant->sending_way) == 'درب محل' ? 'selected' : '' }}>تحویل در محل</option>
                </select>
            </div>

            <!-- هزینه ارسال -->
            <div>
                <label for="send_price" class="block text-gray-700 font-medium mb-2">هزینه ارسال (تومان)</label>
                <input type="number" name="send_price" id="send_price" min="0"
                       value="{{ old('send_price', $restaurant->send_price) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <!-- درصد تخفیف -->
            <div>
                <label for="discount_percentage" class="block text-gray-700 font-medium mb-2">درصد تخفیف</label>
                <input type="number" name="discount_percentage" id="discount_percentage" min="0" max="100"
                       value="{{ old('discount_percentage', $restaurant->discount_percentage) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <!-- دسته‌بندی‌ها -->
            <div>
                <label for="categories" class="block text-gray-700 font-medium mb-2">دسته‌بندی‌ها</label>
                <select name="categories[]" id="categories" multiple
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ in_array($category->id, old('categories', $restaurant->categories->pluck('id')->toArray())) ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <p class="text-sm text-gray-500 mt-1">برای انتخاب چند مورد، کلید Ctrl را نگه دارید.</p>
            </div>

            <!-- موقعیت مکانی روی نقشه -->
            <div>
                <label class="block text-gray-700 font-medium mb-2">📍 موقعیت روی نقشه</label>
                <div id="map" class="w-full h-80 rounded-xl border border-gray-300"></div>
                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $restaurant->latitude) }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $restaurant->longitude) }}">
                <p class="text-sm text-gray-500 mt-2">برای انتخاب موقعیت، روی نقشه کلیک کنید.</p>
            </div>

            <!-- دکمه ذخیره -->
            <div class="pt-4">
                <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                    💾 ذخیره تغییرات
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
            // موقعیت پیش‌فرض سبزوار
            let defaultLat = {{ old('latitude', $restaurant->latitude ?? 36.2140) }};
            let defaultLng = {{ old('longitude', $restaurant->longitude ?? 57.6678) }};

            const map = L.map('map').setView([defaultLat, defaultLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

            let marker = L.marker([defaultLat, defaultLng]).addTo(map)
                .bindPopup("موقعیت فعلی").openPopup();

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

