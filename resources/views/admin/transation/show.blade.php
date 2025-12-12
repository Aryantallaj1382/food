@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-6 py-10">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center border-b pb-3">
            جزئیات تراکنش رستوران: {{ $restaurant->name }}
        </h2>

        <!-- 💰 خلاصه حساب -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">

            <div class="bg-indigo-100 text-indigo-700 px-4 py-2 rounded-lg font-semibold flex items-center gap-3">
                <span>مانده: {{ number_format(abs($balance)) }} تومان</span>
                <span class="{{ $statusColor }}">
        ({{ $statusText }})
    </span>
            </div>

        </div>

        <!-- 🔍 فرم فیلتر تراکنش‌ها -->
        <form method="GET" class="mb-6 flex flex-col md:flex-row items-center gap-4 justify-between bg-white p-4 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center gap-2 w-full md:w-1/3">
                <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-400 focus:outline-none">
                    <option value="">همه نوع‌ها</option>
                    <option value="debit" @selected(($filters['type'] ?? '') === 'debit')>بستانکار</option>
                    <option value="credit" @selected(($filters['type'] ?? '') === 'credit')>بدهکار</option>
                </select>
            </div>

            <div class="flex gap-3 w-full md:w-auto justify-center">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                    اعمال فیلتر
                </button>
                <a href="{{ route('admin.restaurants.show', $restaurant->id) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                    حذف فیلتر
                </a>
            </div>
        </form>

        <!-- 📊 جدول تراکنش‌ها -->
        <div class="overflow-x-auto bg-white shadow-md rounded-2xl border border-gray-200">
            <table class="min-w-full text-sm text-gray-700">
                <thead class="bg-gradient-to-l from-indigo-600 to-indigo-500 text-white text-center">
                <tr>
                    <th class="py-3 px-4 font-semibold">#</th>
                    <th class="py-3 px-4 font-semibold">نوع تراکنش</th>
                    <th class="py-3 px-4 font-semibold">مبلغ (تومان)</th>
                    <th class="py-3 px-4 font-semibold">توضیحات</th>
                    <th class="py-3 px-4 font-semibold">کد تراکنش</th>
                    <th class="py-3 px-4 font-semibold">تاریخ</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-center">
                @forelse ($transactions as $index => $transaction)
                    <tr class="hover:bg-indigo-50 transition">
                        <td class="py-3 px-4 font-medium text-gray-900">{{ $index + $transactions->firstItem() }}</td>
                        <td class="py-3 px-4 font-semibold {{ $transaction->type === 'debit' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->type === 'debit' ? 'بستانکار' : 'بدهکار' }}
                        </td>

                        <td class="py-3 px-4">{{ number_format($transaction->amount) }}</td>
                        <td class="py-3 px-4">{{$transaction->description }}</td>
                        <td class="py-3 px-4">{{$transaction->tracking_code }}</td>
                        <td class="py-3 px-4">{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 text-gray-500">هیچ تراکنشی یافت نشد.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- 🧭 صفحه‌بندی -->
        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </div>
@endsection
