@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-6 py-10">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center border-b pb-3">وضعیت حساب رستوران‌ها</h2>

        <!-- 🔍 فرم فیلتر -->
        <form method="GET" class="mb-6 flex flex-col md:flex-row items-center gap-4 justify-between bg-white p-4 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center gap-2 w-full md:w-1/3">
                <input type="text" name="search" placeholder="جستجوی نام رستوران..."
                       value="{{ $filters['search'] ?? '' }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none">
            </div>

            <div class="flex items-center gap-2 w-full md:w-1/3">
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                    <option value="">همه وضعیت‌ها</option>
                    <option value="debit" @selected(($filters['status'] ?? '') === 'debit')>بستانکار</option>
                    <option value="credit" @selected(($filters['status'] ?? '') === 'credit')>بدهکار</option>
                    <option value="zero" @selected(($filters['status'] ?? '') === 'zero')>صفر</option>
                </select>
            </div>

            <div class="flex gap-3 w-full md:w-auto justify-center">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                    اعمال فیلتر
                </button>
                <a href="{{ route('admin.restaurants.balance') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                    حذف فیلتر
                </a>
            </div>
        </form>

        <!-- 📊 جدول -->
        <div class="overflow-x-auto bg-white shadow-md rounded-2xl border border-gray-200">
            <table class="min-w-full text-sm text-gray-700">
                <thead class="bg-gradient-to-l from-indigo-600 to-indigo-500 text-white text-center">
                <tr>
                    <th class="py-3 px-4 font-semibold">#</th>
                    <th class="py-3 px-4 font-semibold">نام رستوران</th>
                    <th class="py-3 px-4 font-semibold">شماره رستوران</th>
                    <th class="py-3 px-4 font-semibold">مانده</th>
                    <th class="py-3 px-4 font-semibold">عملیات</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-center">
                @forelse ($restaurants as $index => $restaurant)
                    <tr class="hover:bg-indigo-50 transition">
                        <td class="py-3 px-4 font-medium text-gray-900">{{ $index + 1 }}</td>
                        <td class="py-3 px-4 font-semibold text-gray-800">{{ $restaurant->name }}</td>
                        <td class="py-3 px-4 font-semibold text-gray-800">{{ $restaurant->user?->mobile ?? '---' }}</td>
                        <td class="py-3 px-4">
                            @if($restaurant->balance < 0)
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">
                                    {{ number_format($restaurant->balance) }} بستانکار
                                </span>
                            @elseif($restaurant->balance> 0)
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">
                                    {{ number_format(abs($restaurant->balance)) }} بدهکار
                                </span>
                            @else
                                <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-semibold">
                                    صفر
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 flex justify-center gap-2">
                            <!-- مشاهده جزئیات -->
                            <a href="{{ route('admin.restaurants.transaction.show', $restaurant->id) }}"
                               class="inline-flex items-center gap-1 bg-indigo-500 text-white px-3 py-1.5 rounded-lg hover:bg-indigo-600 transition text-xs">
                                🔍 مشاهده جزئیات
                            </a>

                            <!-- ثبت تراکنش بستانکار -->
                            <a href="{{ route('admin.credit.create', $restaurant->id) }}"
                               class="inline-flex items-center gap-1 bg-green-500 text-white px-3 py-1.5 rounded-lg hover:bg-green-600 transition text-xs">
                                💰 ثبت بستانکار
                            </a>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-gray-500">هیچ رستورانی یافت نشد.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
