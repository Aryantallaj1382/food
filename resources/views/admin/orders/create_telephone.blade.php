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

                <!-- مرحله 3: انتخاب غذاها -->
                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold mb-4">3️⃣ انتخاب غذاها</h3>
                    <div id="foodsContainer" class="space-y-4">
                        <p class="text-gray-500">ابتدا یک رستوران انتخاب کنید تا غذاها نمایش داده شود.</p>
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

        document.addEventListener('DOMContentLoaded', function () {

            let orderItems = [];

            const restaurantSelect = document.getElementById('restaurant_id');
            const foodsContainer = document.getElementById('foodsContainer');
            const orderTable = document.getElementById('orderItemsTable');
            const totalAmountSpan = document.getElementById('totalAmount');
            const deliveryFeeInput = document.getElementById('delivery_fee');

            // بررسی شماره موبایل و پر کردن اطلاعات کاربر
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


            // وقتی رستوران انتخاب شد، غذاها رو بارگذاری کن
            restaurantSelect.addEventListener('change', function() {
                const restaurantId = this.value;
                if(!restaurantId) {
                    foodsContainer.innerHTML = '<p class="text-gray-500">ابتدا یک رستوران انتخاب کنید.</p>';
                    return;
                }

                fetch(`/admin/orders/restaurants/${restaurantId}/foods`)
                    .then(res => res.json())
                    .then(foods => {
                        if(foods.length === 0) {
                            foodsContainer.innerHTML = '<p class="text-gray-500">غذایی موجود نیست.</p>';
                            return;
                        }

                        foodsContainer.innerHTML = '';
                        foods.forEach(food => {
                            const div = document.createElement('div');
                            div.className = 'p-4 border rounded-lg mb-2';

                            let optionsHtml = '';
                            food.options.forEach(option => {
                                optionsHtml += `
                        <div class="flex items-center justify-between mb-2 gap-2">
                            <span class="font-semibold">${food.name} - ${option.name}</span>
                            <input type="number" min="1" value="1" class="p-2 border rounded w-20" data-option-id="${option.id}" placeholder="تعداد">
                            <span class="text-gray-700">${option.price} تومان</span>
                            <button type="button" class="bg-blue-600 text-white px-3 py-1 rounded addOptionBtn"
                                data-option-id="${option.id}" data-food-name="${food.name}" data-option-name="${option.name}" data-price="${option.price}">
                                افزودن
                            </button>
                        </div>
                    `;
                            });

                            div.innerHTML = optionsHtml;
                            foodsContainer.appendChild(div);
                        });

                        document.querySelectorAll('.addOptionBtn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const optionId = this.dataset.optionId;
                                const foodName = this.dataset.foodName;
                                const optionName = this.dataset.optionName;
                                const price = parseInt(this.dataset.price);
                                const qtyInput = this.previousElementSibling.previousElementSibling; // input
                                const qty = parseInt(qtyInput.value) || 1;

                                orderItems.push({option_id: optionId, name: foodName, option: optionName, qty: qty, price: price});
                                renderOrderTable();
                            });
                        });
                    });
            });

            // حذف آیتم سفارش
            function removeItem(index) {
                orderItems.splice(index, 1);
                renderOrderTable();
            }

            // رندر جدول سفارش و جمع کل
            function renderOrderTable() {
                orderTable.innerHTML = '';
                let total = 0;
                orderItems.forEach((item, idx) => {
                    const row = document.createElement('tr');
                    const rowTotal = item.price * item.qty;
                    total += rowTotal;

                    row.innerHTML = `
                <td class="p-2 border">${item.name}</td>
                <td class="p-2 border">-</td>
                <td class="p-2 border">${item.qty}</td>
                <td class="p-2 border">${item.price}</td>
                <td class="p-2 border">${rowTotal}</td>
                <td class="p-2 border"><button type="button" class="text-red-600" onclick="removeItem(${idx})">حذف</button></td>
            `;
                    orderTable.appendChild(row);
                });

                const deliveryFee = parseInt(deliveryFeeInput.value) || 0;
                totalAmountSpan.textContent = total + deliveryFee;

                // اضافه کردن input های hidden برای فرم
                const form = document.getElementById('telephoneOrderForm');
                form.querySelectorAll('input[name="foods"]').forEach(i => i.remove());
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'foods';
                hiddenInput.value = JSON.stringify(orderItems);
                form.appendChild(hiddenInput);
            }

            // وقتی هزینه ارسال تغییر کرد، جمع کل بروزرسانی شود
            deliveryFeeInput.addEventListener('input', renderOrderTable);

            // تابع removeItem را در window قرار میدهیم تا از onclick استفاده شود
            window.removeItem = removeItem;

        });
    </script>
@endpush
