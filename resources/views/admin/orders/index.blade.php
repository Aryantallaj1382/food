@extends('layouts.app')
@php use Morilog\Jalali\Jalalian; @endphp

@section('title', 'مدیریت سفارش‌ها')

@section('content')
    <div class="container py-6">
        <h1 class="text-2xl font-semibold mb-6 text-gray-800">لیست سفارش‌ها</h1>

        <!-- فرم فیلتر -->
        <form id="filterForm" class="mb-8 bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-6 items-end">

                <!-- جستجو - با debounce خودکار -->
                <div class="lg:col-span-3">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">جستجو</label>
                    <input type="text" name="q" value="{{ request('q') }}"
                           placeholder="آیدی، نام کاربر، رستوران..."
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition"
                           id="searchInput">
                </div>

                <div class="lg:col-span-3">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">تاریخ</label>
                    <input type="date" name="date" value="{{ request('date') }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-500 date-filter">
                </div>
                <!-- روش پرداخت + تاریخ در یک ردیف -->
                <div class="lg:col-span-9">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">روش پرداخت</label>
                    <div class="flex items-center gap-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="payment_status" value=""
                                   {{ !request('payment_status') ? 'checked' : '' }}
                                   class="w-5 h-5 text-orange-600 radio-filter">
                            <span class="mr-2 text-sm font-medium text-gray-700">همه</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="payment_status" value="paid"
                                   {{ request('payment_status')=='paid' ? 'checked' : '' }}
                                   class="w-5 h-5 text-orange-600 radio-filter">
                            <span class="mr-2 text-sm font-medium text-gray-700">آنلاین</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="payment_status" value="cash"
                                   {{ request('payment_status')=='cash' ? 'checked' : '' }}
                                   class="w-5 h-5 text-orange-600 radio-filter">
                            <span class="mr-2 text-sm font-medium text-gray-700">نقدی</span>
                        </label>
                    </div>
                </div>
                <div class="lg:col-span-9">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">وضعیت سفارش</label>
                    <div class="flex flex-wrap items-center gap-4">
                        @php
                            $statuses = [
                                ''          => 'همه',
                                'pending'   => 'در انتظار تایید',
                                'ok' => 'تایید شده',
                                'cancelled' => 'کنسل شده',
                                'processing'=> 'در حال آماده‌سازی',
                                'completed'     => 'در انتظار پیک',
                                'delivery'  => 'تحویل داده شده',
                            ];
                        @endphp

                        @foreach($statuses as $value => $label)
                            <label class="flex items-center cursor-pointer group">
                                <input type="radio" name="status" value="{{ $value }}"
                                       {{ request('status') == $value ? 'checked' : '' }}
                                       class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300 radio-filter">
                                <span
                                    class="mr-2 text-sm font-medium text-gray-700 group-hover:text-orange-600 transition">
                            {{ $label }}
                        </span>
                            </label>
                        @endforeach
                    </div>
                </div>


                <!-- دکمه بازنشانی (کوچک و شیک) -->
                <div class="lg:col-span-3 flex justify-end items-end">
                    @if(request()->hasAny(['q', 'status', 'payment_status', 'date']))
                        <a href="{{ route('admin.orders.index') }}"
                           class="inline-flex items-center gap-2 px-5 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            پاک کردن فیلتر
                        </a>
                    @endif
                </div>
            </div>
        </form>

        @push('scripts')
            <script>
                // جستجو با تاخیر (debounce) - خیلی نرم و حرفه‌ای
                let searchTimeout;
                document.getElementById('searchInput').addEventListener('input', function () {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        document.getElementById('filterForm').submit();
                    }, 600); // 600ms تاخیر
                });

                // هر تغییری در رادیوها یا تاریخ → فوری اعمال
                document.querySelectorAll('.radio-filter, .date-filter').forEach(el => {
                    el.addEventListener('change', function () {
                        document.getElementById('filterForm').submit();
                    });
                });
            </script>
        @endpush
        <!-- جدول سفارش‌ها -->
        <div class="bg-white shadow-xl rounded-2xl border overflow-hidden">
            @if($orders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-[900px] w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-orange-700 to-orange-500 text-white">
                        <tr class="text-center">
                            <th class="py-4 px-3 text-xs font-bold">شماره سفارش</th>
                            <th class="py-4 px-3 text-xs font-bold">تاریخ</th>
                            <th class="py-4 px-3 text-xs font-bold">زمان تحویل</th>
                            <th class="py-4 px-3 text-xs font-bold">رستوران</th>
                            <th class="py-4 px-3 text-xs font-bold">کاربر</th>
                            <th class="py-4 px-3 text-xs font-bold">مبلغ</th>
                            <th class="py-4 px-3 text-xs font-bold">پرداخت</th>
                            <th class="py-4 px-3 text-xs font-bold">وضعیت</th>
                            <th class="py-4 px-3 text-xs font-bold">توضیحات مدیر</th>
                            <th class="py-4 px-3 text-xs font-bold">عملیات</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                        @foreach($orders as $key => $order)
                            <tr class="transition text-center text-sm {{ $order->restaurant_accept ? 'bg-emerald-100/50 border-l-4 border-emerald-500 font-medium' : 'hover:bg-orange-50' }}">
                                <td class="py-4 px-3 whitespace-nowrap">{{ $order->id }}</td>
                                <td class="py-4 px-3 text-xs whitespace-nowrap">{{ Jalalian::fromDateTime($order->created_at)->format('Y/m/d H:i') }}</td>
                                @if($order->time=='now')
                                    <td class="py-4 px-3 bg-black text-amber-50 whitespace-nowrap">{{  $order->get_ready_time ?? '--' }}</td>
                                @else

                                    <td class="py-4 px-3 bg-amber-400 whitespace-nowrap">{{ $order->time ?? '--' }}</td>

                                @endif

                                <td class="py-4 px-3 whitespace-nowrap">{{ $order->restaurant->name ?? '—' }}</td>
                                <td class="py-4 px-3 whitespace-nowrap">
                                    @if($order->user)
                                        <div class="flex items-center gap-2 justify-center whitespace-nowrap">
                                            @if($order->user->is_blocked)
                                                <span class="line-through text-gray-500">{{ $order->user->name }}</span>
                                                <span
                                                    class="inline-flex items-center gap-1 bg-red-100 text-red-700 px-2.5 py-1 rounded-full text-xs font-bold">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                          clip-rule="evenodd"></path>
                                                </svg>
                                                بلاک شده
                                            </span>
                                            @else
                                                <span class="text-gray-800 font-medium">{{ $order->user->name }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">کاربر حذف شده</span>
                                    @endif
                                </td>
                                <td class="py-4 px-3 font-bold text-green-600 whitespace-nowrap">
                                    @if($order->restaurant->free_shipping)
                                        <div class="font-bold text-red-500">
                                            **
                                        </div>
                                    @endif
                                    {{ number_format($order->total_amount) }}
                                    <span
                                        class="text-sm text-gray-500">({{ number_format($order->total_price) }})</span>
                                    تومان

                                </td>
                                <td class="py-4 px-3 whitespace-nowrap">
                                <span
                                    class="{{ $order->payment_method === 'cash' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }} px-3 py-1 rounded-full text-xs font-medium">
                                    {{ $order->payment_method === 'cash' ? 'پرداخت در محل' : 'آنلاین' }}
                                </span>

                                </td>
                                <td class="py-4 px-3 whitespace-nowrap">
                                <span class="px-3 py-1 rounded-full text-xs font-bold
                                    {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-800' :
                                       ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' :
                                       ($order->status === 'processing' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800')) }}">
                                    {{ $order->status_fa }}
                                </span>
                                </td>
                                <td class="py-4 px-3 text-xs">
                                    <span
                                        class="text-gray-600">{{ \Illuminate\Support\Str::limit($order->admin_note, 30, '...') }}</span>
                                </td>
                                <td class="py-4 px-3 space-x-2 whitespace-nowrap">
                                    <button
                                        onclick="openAdminNoteModal({{ $order->id }}, '{{ addslashes($order->admin_note ?? '') }}')"
                                        class="px-3 py-1.5 bg-indigo-600 text-white text-xs rounded-lg hover:bg-indigo-700 transition">
                                        ویرایش توضیحات
                                    </button>
                                    @php
                                        // فقط اگر وضعیت سفارش در لیست مجاز باشد
                                        $canChangeStatus = in_array($order->status, ['processing', 'completed']);
                                        // فقط اگر مبلغ پرداخت شده باشد
                                        $isPaymentDone = $order->payment_status != 'pending';
                                    @endphp

                                    <div class="inline-block relative group">
                                        <button
                                            class="px-3 py-1.5 text-xs rounded-lg transition
               {{ ($canChangeStatus && $isPaymentDone) ? 'bg-gray-200 text-gray-800 hover:bg-gray-300 cursor-pointer' : 'bg-gray-100 text-gray-400 cursor-not-allowed' }}"
                                            {{ ($canChangeStatus && $isPaymentDone) ? '' : 'disabled' }}>
                                            تغییر وضعیت
                                        </button>

                                        @if($canChangeStatus && $isPaymentDone)
                                            <ul class="absolute hidden group-hover:block bg-white shadow-lg rounded-lg w-40 mt-1 text-sm z-50">
                                                <li class="px-4 py-2 hover:bg-red-100 cursor-pointer"
                                                    onclick="changeOrderStatus({{ $order->id }}, 'delivery')">
                                                    تحویل پیک
                                                </li>
                                            </ul>
                                        @endif
                                    </div>
                                    <a href="{{ route('admin.restaurants.items', $order->id) }}"
                                       class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition inline-block">
                                        جزئیات
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="bg-gray-50 px-6 py-4 border-t">
                    {{ $orders->appends(request()->query())->links() }}
                </div>
            @else
                <p class="text-center py-16 text-gray-500 text-lg">هیچ سفارشی یافت نشد.</p>
            @endif
        </div>
    </div>

    {{-- مدال ویرایش توضیحات مدیر --}}
    <div id="adminNoteModal"
         class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">ویرایش توضیحات مدیر</h3>
                <button onclick="closeAdminNoteModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="adminNoteForm">
                @csrf
                @method('PATCH')
                <input type="hidden" id="orderId" value="">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">توضیحات مدیر</label>
                    <textarea id="adminNote" rows="5"
                              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 resize-none"></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeAdminNoteModal()"
                            class="px-5 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">لغو
                    </button>
                    <button type="submit"
                            class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        ذخیره تغییرات
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- اسکریپت مدال + Ajax --}}
    <script>
        function openAdminNoteModal(orderId, currentNote) {
            document.getElementById('orderId').value = orderId;
            document.getElementById('adminNote').value = currentNote;
            document.getElementById('adminNoteModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeAdminNoteModal() {
            document.getElementById('adminNoteModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        document.getElementById('adminNoteForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const orderId = document.getElementById('orderId').value;
            const note = document.getElementById('adminNote').value;

            axios.patch(`/admin/orders/${orderId}/admin-note`, {admin_note: note})
                .then(response => {
                    closeAdminNoteModal();
                    setTimeout(() => location.reload(), 1200);
                })
                .catch(err => {
                    alert('خطا در ذخیره‌سازی. دوباره تلاش کنید.');
                });
        });

        document.querySelectorAll('.inline-block.relative > button').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation(); // جلوگیری از بسته شدن خودکار
                const ul = this.nextElementSibling;
                ul.classList.toggle('hidden');
            });
        });

        // بستن منو وقتی بیرون کلیک شد
        document.addEventListener('click', function () {
            document.querySelectorAll('.inline-block.relative ul').forEach(ul => {
                ul.classList.add('hidden');
            });
        });

        // تغییر وضعیت با AJAX و ریلود صفحه
        function changeOrderStatus(orderId, status) {
            axios.patch(`/admin/order/orders/${orderId}/status`, {status: status})
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
