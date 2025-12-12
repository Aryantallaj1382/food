@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6 max-w-5xl">
        <div class="bg-white shadow-lg rounded-2xl p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">📞 ثبت سفارش تلفنی جدید</h2>

            <!-- نمایش خطاها -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="telephoneOrderForm" action="{{ route('admin.orders.telephone.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- مرحله 1: اطلاعات کاربر -->
                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold mb-4">1️⃣ اطلاعات کاربر</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">شماره موبایل</label>
                            <input type="text" name="mobile" id="mobile" class="w-full p-3 border rounded-lg" placeholder="مثلاً 09123456789" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">نام</label>
                            <input type="text" name="first_name" id="first_name" class="w-full p-3 border rounded-lg" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">نام خانوادگی</label>
                            <input type="text" name="last_name" id="last_name" class="w-full p-3 border rounded-lg" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">آدرس</label>
                            <input type="text" name="address" id="address" class="w-full p-3 border rounded-lg" required>
                        </div>
                    </div>
                </div>

                <!-- مرحله 2: انتخاب رستوران -->
                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold mb-4">2️⃣ انتخاب رستوران</h3>
                    <select id="restaurant_id" name="restaurant_id" class="w-full p-3 border rounded-lg" required>
                        <option value="">انتخاب رستوران</option>
                        @foreach($restaurants as $restaurant)
                            <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
                        @endforeach
                    </select>
                </div>


                    <div class="border-b pb-6">
                        <h3 class="text-lg font-semibold mb-4">3- انتخاب غذاها</h3>

                        <!-- سرچ باکس -->
                        <div class="mb-6">
                            <input type="text" id="foodSearch" placeholder="جستجوی غذا..."
                                   class="w-full p-3 border rounded-lg text-lg focus:outline-none focus:border-blue-500">
                        </div>

                        <div id="foodsContainer" class="space-y-8 max-h-96 overflow-y-auto pr-2">
                            <p class="text-gray-500 text-center">در حال بارگذاری غذاها...</p>
                        </div>
                    </div>

                <!-- مرحله 4: فاکتور و توضیحات -->
                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold mb-4">4️⃣ فاکتور سفارش</h3>
                    <div class="mb-4">
                        <table class="w-full border rounded-lg text-left">
                            <thead>
                            <tr class="bg-gray-100">
                                <th class="p-2 border">نام غذا</th>
                                <th class="p-2 border">گزینه</th>
                                <th class="p-2 border">تعداد</th>
                                <th class="p-2 border">قیمت</th>
                                <th class="p-2 border">جمع</th>
                                <th class="p-2 border">حذف</th>
                            </tr>
                            </thead>
                            <tbody id="orderItemsTable"></tbody>
                        </table>
                    </div>

                    <div class="flex justify-between items-center mb-4">
                        <label class="text-gray-700 font-medium">هزینه ارسال (تومان)</label>
                        <input type="number" name="delivery_fee" id="delivery_fee" class="p-2 border rounded-lg w-32" value="0">
                    </div>

                    <div class="flex justify-between items-center mb-4">
                        <span class="text-gray-700 font-semibold">جمع کل:</span>
                        <span id="totalAmount" class="font-bold text-lg">0</span>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-2">توضیحات سفارش (اختیاری)</label>
                        <textarea name="note" class="w-full p-3 border rounded-lg" rows="3"></textarea>
                    </div>
                </div>

                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg transition">
                    ثبت سفارش
                </button>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>

        document.getElementById('mobile').addEventListener('blur', function() {
            const mobile = this.value;
            if(!mobile) return;

            fetch(`{{ route('admin.orders.checkUser') }}?mobile=${mobile}`)
                .then(res => res.json())
                .then(data => {
                    if(data.exists) {
                        document.getElementById('first_name').value = data.user.first_name;
                        document.getElementById('last_name').value = data.user.last_name;
                        document.getElementById('address').value = data.user.address;
                    } else {
                        document.getElementById('first_name').value = '';
                        document.getElementById('last_name').value = '';
                        document.getElementById('address').value = '';
                    }
                });
        });

        document.addEventListener('DOMContentLoaded', function () {
            let orderItems = [];
            const restaurantSelect = document.getElementById('restaurant_id');
            const foodsContainer = document.getElementById('foodsContainer');
            const orderTable = document.getElementById('orderItemsTable');
            const totalAmountSpan = document.getElementById('totalAmount');
            const deliveryFeeInput = document.getElementById('delivery_fee');
            const foodSearch = document.getElementById('foodSearch');

            // وقتی رستوران عوض شد
            restaurantSelect.addEventListener('change', loadFoods);
            foodSearch.addEventListener('input', filterFoods); // سرچ لحظه‌ای

            function loadFoods() {
                const restaurantId = restaurantSelect.value;
                if (!restaurantId) {
                    foodsContainer.innerHTML = '<p class="text-gray-500 text-center">لطفاً یک رستوران انتخاب کنید.</p>';
                    return;
                }

                foodsContainer.innerHTML = '<p class="text-center text-gray-500">در حال بارگذاری غذاها...</p>';

                fetch(`/admin/orders/restaurants/${restaurantId}/foods`)
                    .then(res => res.json())
                    .then(categories => {
                        window.allCategories = categories; // برای سرچ ذخیره می‌کنیم
                        renderFoods(categories);
                    })
                    .catch(() => {
                        foodsContainer.innerHTML = '<p class="text-red-500 text-center">خطا در بارگذاری غذاها</p>';
                    });
            }

            function renderFoods(categories) {
                if (!categories || categories.length === 0) {
                    foodsContainer.innerHTML = '<p class="text-gray-500 text-center">غذایی یافت نشد.</p>';
                    return;
                }

                foodsContainer.innerHTML = '';

                categories.forEach(cat => {
                    const catDiv = document.createElement('div');
                    catDiv.className = 'category-group';

                    catDiv.innerHTML = `
            <h4 class="font-bold text-xl mb-4 text-blue-700 border-b-2 border-blue-200 pb-2">
                ${cat.category}
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 food-items">
                ${cat.foods.map(food => `
                    <div class="bg-gray-50 p-4 rounded-lg border food-item shadow-sm">
                        <h5 class="font-semibold text-lg mb-3 text-gray-800">${food.name}</h5>
                        <div class="space-y-3">
                            ${food.options.map(option => {
                        const hasDiscount = option.price_discount && option.price_discount < option.price;
                        const displayPrice = hasDiscount ? option.price_discount : option.price;

                        return `
                                    <div class="flex items-center justify-between gap-3 bg-white p-3 rounded border hover:shadow transition">
                                        <div class="flex-1">
                                            <span class="font-medium">${option.name}</span>
                                            <div class="text-sm mt-1">
                                                ${hasDiscount ? `
                                                    <div>
                                                        <span class="line-through text-gray-400 text-xs">${formatPrice(option.price)}</span>
                                                        <span class="text-green-600 font-bold mr-2">${formatPrice(option.price_discount)} تومان</span>
                                                    </div>
                                                ` : `
                                                    <span class="text-gray-700">${formatPrice(option.price)} تومان</span>
                                                `}
                                            </div>
                                        </div>
                                        <input type="number" min="1" value="1" class="w-20 p-2 border rounded text-center qty-input focus:ring-2 focus:ring-green-500"
                                               data-option-id="${option.id}">
                                        <button type="button"
                                                class="relative bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm add-to-cart transition-all ${hasDiscount ? 'ring-2 ring-green-300 ring-offset-2' : ''}"
                                                data-option-id="${option.id}"
                                                data-food-name="${food.name}"
                                                data-option-name="${option.name}"
                                                data-price="${displayPrice}">
                                            افزودن
                                            ${hasDiscount ? '<span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full animate-pulse">تخفیف</span>' : ''}
                                        </button>
                                    </div>
                                `;
                    }).join('')}
                        </div>
                    </div>
                `).join('')}
            </div>
        `;

                    foodsContainer.appendChild(catDiv);
                });

                // اضافه کردن ایونت به دکمه‌های افزودن (بعد از رندر)
                document.querySelectorAll('.add-to-cart').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const optionId = this.dataset.optionId;
                        const foodName = this.dataset.foodName;
                        const optionName = this.dataset.optionName;
                        const price = parseInt(this.dataset.price);
                        const qtyInput = this.previousElementSibling;
                        const qty = parseInt(qtyInput.value) || 1;

                        const existing = orderItems.find(item => item.option_id == optionId);
                        if (existing) {
                            existing.qty += qty;
                        } else {
                            orderItems.push({
                                option_id: optionId,
                                name: foodName,
                                option: optionName,
                                qty: qty,
                                price: price
                            });
                        }

                        qtyInput.value = 1;
                        renderOrderTable();
                        showToast(`${foodName} - ${optionName} (${qty} عدد) به سبد اضافه شد`, 'success');
                    });
                });
            }
            // سرچ لحظه‌ای
            function filterFoods() {
                const query = foodSearch.value.trim().toLowerCase();
                if (!window.allCategories) return;

                if (query === '') {
                    renderFoods(window.allCategories);
                    return;
                }

                const filtered = window.allCategories.map(cat => {
                    const filteredFoods = cat.foods.filter(food =>
                        food.name.toLowerCase().includes(query) ||
                        food.options.some(opt => opt.name.toLowerCase().includes(query))
                    );

                    return { ...cat, foods: filteredFoods };
                }).filter(cat => cat.foods.length > 0);

                renderFoods(filtered);
            }

            // فرمت قیمت
            function formatPrice(price) {
                return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }

            // حذف آیتم
            window.removeItem = function(index) {
                orderItems.splice(index, 1);
                renderOrderTable();
            };

            // رندر جدول سفارش
            function renderOrderTable() {
                orderTable.innerHTML = '';
                let total = 0;

                orderItems.forEach((item, idx) => {
                    const rowTotal = item.price * item.qty;
                    total += rowTotal;

                    const row = document.createElement('tr');
                    row.innerHTML = `
                <td class="p-2 border">${item.name}</td>
                <td class="p-2 border">${item.option}</td>
                <td class="p-2 border">${item.qty}</td>
                <td class="p-2 border">${formatPrice(item.price)}</td>
                <td class="p-2 border">${formatPrice(rowTotal)}</td>
                <td class="p-2 border">
                    <button type="button" class="text-red-600 hover:underline" onclick="removeItem(${idx})">حذف</button>
                </td>
            `;
                    orderTable.appendChild(row);
                });

                const delivery = parseInt(deliveryFeeInput.value) || 0;
                totalAmountSpan.textContent = formatPrice(total + delivery) + ' تومان';

                // hidden input برای ارسال
                // hidden input برای ارسال
                const form = document.getElementById('telephoneOrderForm');
                form.querySelectorAll('input[name="foods"]').forEach(i => i.remove()); // قبلاً order_items بود

                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'foods';        // درستش اینه!
                hidden.value = JSON.stringify(orderItems);
                form.appendChild(hidden);
            }

            deliveryFeeInput.addEventListener('input', renderOrderTable);

            // تابع توست ساده (همون قبلی)
            function showToast(msg, type = 'success') {
                const toast = document.createElement('div');
                toast.className = `fixed top-4 left-1/2 -translate-x-1/2 px-6 py-3 rounded-lg text-white font-bold z-50 transition-all duration-300 ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
                toast.textContent = msg;
                document.body.appendChild(toast);
                setTimeout(() => toast.classList.add('opacity-100'), 100);
                setTimeout(() => {
                    toast.classList.remove('opacity-100');
                    setTimeout(() => toast.remove(), 300);
                }, 2500);
            }
        });
    </script>
@endpush
