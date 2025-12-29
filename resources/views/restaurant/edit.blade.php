@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto gri bg-white shadow-lg rounded-2xl p-8 mt-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center border-b pb-3">✏️ ویرایش رستوران</h2>

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="px-6 py-4 bg-gray-50 border-t flex flex-wrap justify-end gap-3">

            <a href="{{ route('admin.foods.restaurant', $restaurant->id) }}"
               class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition shadow-sm">
                🍽️ غذاها
            </a>

            <a href="{{ route('admin.restaurants.order', $restaurant->id) }}"
               class="flex items-center gap-2 px-4 py-2 bg-amber-600 text-white rounded-xl hover:bg-amber-700 transition shadow-sm">
                🛒 سفارش‌ها
            </a>

            <a href="{{ route('admin.restaurants.show', $restaurant->id) }}"
               class="flex items-center gap-2 px-4 py-2 bg-yellow-400 text-white rounded-xl hover:bg-yellow-500 transition shadow-sm">
                ✏️ نمایش اطلاعات
            </a>

            <form action="{{ route('admin.restaurants.destroy', $restaurant->id) }}" method="POST" onsubmit="return confirm('آیا مطمئن هستید؟');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="flex items-center gap-2 px-4 py-2 bg-red-500 text-white rounded-xl hover:bg-red-600 transition shadow-sm">
                    🗑️ حذف
                </button>
            </form>

        </div>

        <form action="{{ route('admin.restaurants.update', $restaurant->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- نام -->
            <div>
                <label for="name" class="block text-gray-700 font-medium mb-2">نام رستوران</label>
                <input type="text" name="name" id="name" required value="{{ old('name', $restaurant->name) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>
            <div>
                <label for="mobile" class="block text-gray-700 font-medium mb-2">شماره  ثابت</label>
                <input type="text" name="mobile" id="mobile" required value="{{ old('mobile', $restaurant->mobile) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>
            <div>
                <label for="phone" class="block text-gray-700 font-medium mb-2">شماره تماس 4</label>
                <input type="text" name="phone" id="phone" required value="{{ old('phone', $restaurant->phone) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>
            <div>
                <label for="mobile3" class="block text-gray-700 font-medium mb-2">شماره تماس 5</label>
                <input type="text" name="mobile3" id="mobile3" required value="{{ old('mobile3', $restaurant->mobile3) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>
            <!-- انتخاب کاربر -->
            <div>
                <label for="user_id" class="block text-gray-700 font-medium mb-2">انتخاب کاربر</label>
                <select name="user_id" id="user_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                    <option value="">انتخاب کنید</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $restaurant->user_id == $user->id ? 'selected' : '' }}>
                            {{ $user->first_name }} ({{ $user->mobile ?? 'بدون شماره' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- آدرس -->
            <div>
                <label for="address" class="block text-gray-700 font-medium mb-2">آدرس</label>
                <input type="text" name="address" id="address" value="{{ old('address', $restaurant->address) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <!-- توضیحات -->
            <div>
                <label for="text" class="block text-gray-700 font-medium mb-2">توضیحات ارسال</label>
                <input type="text" name="text" id="text" value="{{ old('text', $restaurant->text) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>
            <div>
                <label for="description" class="block text-gray-700 font-medium mb-2"> توضیحات چشمک زن</label>
                <input type="text" name="description" id="description" value="{{ old('description', $restaurant->description) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>
            <div>
                <label for="team_text" class="block text-gray-700 font-medium mb-2">توضیحات تخفیف طعم دار</label>
                <input type="text" name="team_text" id="team_text" value="{{ old('team_text', $restaurant->team_text) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <!-- تصویر فعلی -->
            @if($restaurant->image)
                <div class="mb-4">
                    <img src="{{ asset($restaurant->image) }}" alt="تصویر رستوران" class="w-32 h-32 object-cover rounded">
                </div>
            @endif

            <!-- تصویر جدید -->
            <div>
                <label for="image" class="block text-gray-700 font-medium mb-2">لوگو رستوران</label>
                <input type="file" name="image" id="image" accept="image/*"
                       class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer focus:outline-none">
            </div>



            @if($restaurant->bg)
                <div class="mb-4">
                    <img src="{{ asset($restaurant->bg) }}" alt="تصویر رستوران" class="w-32 h-32 object-cover rounded">
                </div>
            @endif

            <!-- تصویر جدید -->
            <div>
                <label for="bg" class="block text-gray-700 font-medium mb-2">عکس اصلی</label>
                <input type="file" name="bg" id="bg" accept="image/*"
                       class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer focus:outline-none">
            </div>

            <!-- حداقل قیمت -->
            <div>
                <label for="minimum_price" class="block text-gray-700 font-medium mb-2">حداقل قیمت سفارش (تومان)</label>
                <input type="number" name="minimum_price" id="minimum_price" min="0" value="{{ old('minimum_price', $restaurant->minimum_price) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <!-- زمان آماده سازی -->
            <div>
                <label for="grt_ready_minute" class="block text-gray-700 font-medium mb-2">زمان آماده‌سازی حداقل (دقیقه)</label>
                <input type="number" name="grt_ready_minute" id="grt_ready_minute" min="0" value="{{ old('grt_ready_minute', $restaurant->grt_ready_minute) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>
            <div>
                <label for="grt_ready_maximum" class="block text-gray-700 font-medium mb-2">زمان آماده‌سازی حداکثر (دقیقه)</label>
                <input type="number" name="grt_ready_maximum" id="grt_ready_maximum" min="0" value="{{ old('grt_ready_maximum', $restaurant->grt_ready_maximum) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <!-- فاصله و هزینه کیلومتری -->
            <div>
                <label for="send_price" class="block text-gray-700 font-medium mb-2">هزینه ارسال (تومان)</label>
                <input type="number" name="send_price" id="send_price" min="0" value="{{ old('send_price', $restaurant->send_price) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <div>
                <label for="distance_km" class="block text-gray-700 font-medium mb-2">فاصله کیلومتر</label>
                <input type="number" name="distance_km" id="distance_km" min="0" value="{{ old('distance_km', $restaurant->distance_km) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <div>
                <label for="cost_per_km" class="block text-gray-700 font-medium mb-2">قیمت برای هر کیلومتر</label>
                <input type="number" name="cost_per_km" id="cost_per_km" min="0" value="{{ old('cost_per_km', $restaurant->cost_per_km) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <!-- نوع پیک -->
            <div>
                <label for="cod_courier" class="block text-gray-700 font-medium mb-2">پیک برای پرداخت در محل</label>
                <select name="cod_courier" id="cod_courier" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                    <option value="">انتخاب کنید</option>
                    <option value="restaurant_courier" {{ $restaurant->cod_courier=='restaurant_courier' ? 'selected' : '' }}>پیک رستوران</option>
                    <option value="ghazaresan" {{ $restaurant->cod_courier=='ghazaresan' ? 'selected' : '' }}>پیک غذارسان</option>
                </select>
            </div>

            <div>
                <label for="online_courier" class="block text-gray-700 font-medium mb-2">پیک برای آنلاین</label>
                <select name="online_courier" id="online_courier" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                    <option value="">انتخاب کنید</option>
                    <option value="restaurant_courier" {{ $restaurant->online_courier=='restaurant_courier' ? 'selected' : '' }}>پیک رستوران</option>
                    <option value="ghazaresan" {{ $restaurant->online_courier=='ghazaresan' ? 'selected' : '' }}>پیک غذارسان</option>
                </select>
            </div>

            <!-- نحوه پرداخت -->
            <div>
                <label for="pay_type" class="block text-gray-700 font-medium mb-2">نحوه پرداخت</label>
                <select name="pay_type" id="pay_type" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                    <option value="">انتخاب کنید</option>
                    <option value="cash" {{ $restaurant->pay_type=='cash' ? 'selected' : '' }}>فقط در محل</option>
                    <option value="online" {{ $restaurant->pay_type=='online' ? 'selected' : '' }}>فقط آنلاین</option>
                    <option value="both" {{ $restaurant->pay_type=='both' ? 'selected' : '' }}>آنلاین و در محل</option>
                </select>
            </div>

            <!-- نحوه ارسال -->
            <div>
                <label for="sending_way" class="block text-gray-700 font-medium mb-2">نحوه ارسال</label>
                <select name="sending_way" id="sending_way" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                    <option value="">انتخاب کنید</option>
                    <option value="both" {{ $restaurant->sending_way=='both' ? 'selected' : '' }}>تحویل حضوری و پیک</option>
                    <option value="in_person" {{ $restaurant->sending_way=='in_person' ? 'selected' : '' }}>تحویل حضوری</option>
                </select>
            </div>


            <!-- چک باکس ها -->
            <div class="flex items-center mb-4">
                <input type="checkbox" name="is_open" id="is_open" {{ $restaurant->is_open ? 'checked' : '' }} class="h-5 w-5 text-green-600 border-gray-300 rounded focus:ring-2 focus:ring-green-500">
                <label for="is_open" class="ml-2 block text-gray-700 font-medium">فعال سازی رستوران</label>
            </div>

            <div class="flex items-center mb-4">
                <input type="checkbox" name="tax_enabled" id="tax_enabled" {{ $restaurant->tax_enabled ? 'checked' : '' }} class="h-5 w-5 text-green-600 border-gray-300 rounded focus:ring-2 focus:ring-green-500">
                <label for="tax_enabled" class="ml-2 block text-gray-700 font-medium">آیا مالیات حساب شود</label>
            </div>

            <div class="flex items-center mb-4">
                <input type="checkbox" name="panel_editable" id="panel_editable" {{ $restaurant->panel_editable ? 'checked' : '' }} class="h-5 w-5 text-green-600 border-gray-300 rounded focus:ring-2 focus:ring-green-500">
                <label for="panel_editable" class="ml-2 block text-gray-700 font-medium">امکان ویرایش پنل</label>
            </div>

            <div class="flex items-center mb-4">
                <input type="checkbox" name="free_shipping" id="free_shipping" {{ $restaurant->free_shipping ? 'checked' : '' }} class="h-5 w-5 text-green-600 border-gray-300 rounded focus:ring-2 focus:ring-green-500">
                <label for="free_shipping" class="ml-2 block text-gray-700 font-medium">ارسال رایگان</label>
            </div>

            <div class="flex items-center mb-4">
                <input type="checkbox" name="discount" id="discount" {{ $restaurant->discount ? 'checked' : '' }} class="h-5 w-5 text-green-600 border-gray-300 rounded focus:ring-2 focus:ring-green-500">
                <label for="discount" class="ml-2 block text-gray-700 font-medium">فعال کردن تخفیف</label>
            </div>

            <!-- هزینه ارسال و درصد تخفیف -->
            <div>
                <label for="free_shipping_minimum" class="block text-gray-700 font-medium mb-2">هزینه ارسال رایگان تا چند کیلومتر</label>
                <input type="number" name="free_shipping_minimum" id="free_shipping_minimum" min="0" value="{{ old('free_shipping_minimum', $restaurant->free_shipping_minimum) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <div>
                <label for="fee" class="block text-gray-700 font-medium mb-2">درصد کارمزد</label>
                <input type="number" name="fee" id="fee" min="0" value="{{ old('fee', $restaurant->fee) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <div>
                <label for="discount_percentage" class="block text-gray-700 font-medium mb-2">درصد تخفیف</label>
                <input type="number" name="discount_percentage" id="discount_percentage" min="0" max="100" value="{{ old('discount_percentage', $restaurant->discount_percentage) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <!-- ساعت کاری -->
            <div class="w-full">
                <label for="morning_start" class="block text-gray-700 font-medium mb-2">زمان شروع صبح</label>
                <input type="time" name="morning_start" id="morning_start" value="{{ old('morning_start', $restaurant->morning_start) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>
            <div class="w-full">
                <label for="morning_end" class="block text-gray-700 font-medium mb-2">زمان پایان صبح</label>
                <input type="time" name="morning_end" id="morning_end" value="{{ old('morning_end', $restaurant->morning_end) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>
            <div class="w-full">
                <label for="afternoon_start" class="block text-gray-700 font-medium mb-2">زمان شروع بعد از ظهر</label>
                <input type="time" name="afternoon_start" id="afternoon_start" value="{{ old('afternoon_start', $restaurant->afternoon_start) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>
            <div class="w-full">
                <label for="afternoon_end" class="block text-gray-700 font-medium mb-2">زمان پایان بعد از ظهر</label>
                <input type="time" name="afternoon_end" id="afternoon_end" value="{{ old('afternoon_end', $restaurant->afternoon_end) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>

            <!-- دسته بندی ها -->
            <div>
                <label for="categories" class="block text-gray-700 font-medium mb-2">دسته‌بندی‌ها</label>
                <select name="categories[]" id="categories" multiple class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ in_array($category->id, $restaurant->categories->pluck('id')->toArray()) ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <p class="text-sm text-gray-500 mt-1">برای انتخاب چند مورد، کلید Ctrl را نگه دارید.</p>
            </div>

            <!-- موقعیت روی نقشه -->
            <div>
                <label class="block text-gray-700 font-medium mb-2">📍 موقعیت روی نقشه</label>
                <div id="map" class="w-full h-80 rounded-xl border border-gray-300"></div>
                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $restaurant->latitude) }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $restaurant->longitude) }}">
            </div>

            <!-- دکمه ثبت -->
            <div class="pt-4">
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                    ✅ بروزرسانی رستوران
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const defaultLat = {{ $restaurant->latitude ?? 36.2140 }};
                const defaultLng = {{ $restaurant->longitude ?? 57.6678 }};

                const map = L.map('map').setView([defaultLat, defaultLng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(map);

                let marker = L.marker([defaultLat, defaultLng]).addTo(map)
                    .bindPopup("موقعیت رستوران").openPopup();

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
@endsection
