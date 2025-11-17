@extends('layouts.app')
@php use Morilog\Jalali\Jalalian; @endphp

@section('title', 'مدیریت سفارش‌ها')

@section('content')
    <div class="container py-6">
        <h1 class="text-2xl font-semibold mb-6 text-gray-800">لیست سفارش‌ها</h1>

        <!-- فرم فیلتر -->
        <form class="mb-6 flex flex-wrap gap-3 items-end bg-white p-5 rounded-xl shadow-sm border" method="GET">
            <div class="flex-1 min-w-64">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="جستجو (آیدی، کاربر، رستوران...)"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
            </div>
            <div>
                <select name="status" class="border border-gray-300 rounded-lg px-4 py-2">
                    <option value="">همه وضعیت‌ها</option>
                    <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>در انتظار تایید</option>
                    <option value="processing" {{ request('status')=='processing' ? 'selected' : '' }}>در حال آماده‌سازی</option>
                    <option value="completed" {{ request('status')=='completed' ? 'selected' : '' }}>تحویل داده شده</option>
                    <option value="cancelled" {{ request('status')=='cancelled' ? 'selected' : '' }}>لغو شده</option>
                </select>
            </div>
            <div>
                <select name="payment_status" class="border border-gray-300 rounded-lg px-4 py-2">
                    <option value="">پرداخت</option>
                    <option value="paid" {{ request('payment_status')=='paid' ? 'selected' : '' }}>آنلاین</option>
                    <option value="cash" {{ request('payment_status')=='cash' ? 'selected' : '' }}>نقدی</option>
                </select>
            </div>
            <div>
                <input type="date" name="date" value="{{ request('date') }}" class="border border-gray-300 rounded-lg px-4 py-2">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-5 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">فیلتر</button>
                <a href="{{ route('admin.orders.index') }}" class="px-5 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">بازنشانی</a>
            </div>
        </form>

        <!-- جدول سفارش‌ها -->
        <div class="bg-white shadow-xl rounded-2xl border overflow-hidden">
            @if($orders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-orange-700 to-orange-500 text-white">
                        <tr class="text-center">
                            <th class="py-4 px-3 text-xs font-bold">#</th>
                            <th class="py-4 px-3 text-xs font-bold">رستوران</th>
                            <th class="py-4 px-3 text-xs font-bold">کاربر</th>
                            <th class="py-4 px-3 text-xs font-bold">موبایل</th>
                            <th class="py-4 px-3 text-xs font-bold">مبلغ</th>
                            <th class="py-4 px-3 text-xs font-bold">پرداخت</th>
                            <th class="py-4 px-3 text-xs font-bold">تاریخ</th>
                            <th class="py-4 px-3 text-xs font-bold">وضعیت</th>
                            <th class="py-4 px-3 text-xs font-bold">توضیحات</th>
                            <th class="py-4 px-3 text-xs font-bold">توضیحات مدیر</th>
                            <th class="py-4 px-3 text-xs font-bold">عملیات</th>
                        </tr>

                        </thead>
                        <tbody class="divide-y divide-gray-100">
                        @foreach($orders as $key => $order)
                            <tr class="transition text-center text-sm {{ $order->restaurant_accept ? 'bg-emerald-100/50 border-l-4 border-emerald-500 font-medium' : 'hover:bg-orange-50' }}">                                <td class="py-4 px-3 font-medium">{{ $orders->firstItem() + $key }}</td>
                                <td class="py-4 px-3">{{ $order->restaurant->name ?? '—' }}</td>
                                <td class="py-4 px-3">{{ $order->user->name ?? '—' }}</td>
                                <td class="py-4 px-3">{{ $order->user->mobile ?? '—' }}</td>
                                <td class="py-4 px-3 font-bold text-green-600">{{ number_format($order->total_price) }} ₺</td>
                                <td class="py-4 px-3">
                                    <span class="{{ $order->payment_method === 'cash' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }} px-3 py-1 rounded-full text-xs font-medium">
                                        {{ $order->payment_method === 'cash' ? 'نقدی' : 'آنلاین' }}
                                    </span>
                                </td>
                                <td class="py-4 px-3 text-xs">
                                    {{ Jalalian::fromDateTime($order->created_at)->format('Y/m/d H:i') }}
                                </td>
                                <td class="py-4 px-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold
                                        {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-800' :
                                           ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' :
                                           ($order->status === 'processing' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800')) }}">
                                        {{ $order->status_fa }}
                                    </span>
                                </td>
                                <td class="py-4 px-3 text-xs">
                                    <span class="text-gray-600">
                                        {{ \Illuminate\Support\Str::limit($order->notes, 30, '...') }}
                                    </span>
                                </td>
                                <td class="py-4 px-3 text-xs">
                                    <span class="text-gray-600">
                                        {{ \Illuminate\Support\Str::limit($order->admin_note, 30, '...') }}
                                    </span>
                                </td>
                                <td class="py-4 px-3 space-x-2">
                                    <!-- دکمه مدال -->
                                    <button onclick="openAdminNoteModal({{ $order->id }}, '{{ addslashes($order->admin_note ?? '') }}')"
                                            class="px-3 py-1.5 bg-indigo-600 text-white text-xs rounded-lg hover:bg-indigo-700 transition">
                                        ویرایش توضیحات
                                    </button>

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
    <div id="adminNoteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">ویرایش توضیحات مدیر</h3>
                <button onclick="closeAdminNoteModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="adminNoteForm">
                @csrf
                @method('PATCH')
                <input type="hidden" id="orderId" value="">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">توضیحات مدیر</label>
                    <textarea id="adminNote" rows="5" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 resize-none"></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeAdminNoteModal()" class="px-5 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">لغو</button>
                    <button type="submit" class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
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

        document.getElementById('adminNoteForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const orderId = document.getElementById('orderId').value;
            const note = document.getElementById('adminNote').value;

            axios.patch(`/admin/orders/${orderId}/admin-note`, { admin_note: note })
                .then(response => {
                    closeAdminNoteModal();
                    toastr.success('توضیحات مدیر با موفقیت ذخیره شد.');

                    // ← فقط این خط اضافه شد
                    setTimeout(() => location.reload(), 1200);
                })
                .catch(err => {
                    toastr.error('خطا در ذخیره‌سازی. دوباره تلاش کنید.');
                });
        });
    </script>@endsection
