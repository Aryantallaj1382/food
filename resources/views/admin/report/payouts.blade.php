@extends('layouts.app')

@section('content')
    <div class="container py-4" dir="rtl">

        <h2 class="text-xl font-bold mb-4">گزارش واریزی‌ها (رستوران شماره {{ $id }})</h2>

        {{-- فیلتر تاریخ میلادی --}}
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

            <div>
                <label class="block mb-1 text-gray-700 font-medium">از تاریخ (میلادی)</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block mb-1 text-gray-700 font-medium">تا تاریخ (میلادی)</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div class="flex items-end">
                <button class="px-4 py-2 bg-indigo-600 text-white rounded">جستجو</button>
            </div>

        </form>

        {{-- مجموع کل --}}
        <div class="p-4 bg-green-100 border rounded-lg mb-4 text-lg font-bold">
            مجموع واریزی‌ها:
            <span class="text-green-700">{{ number_format($totalAmount) }} تومان</span>
        </div>

        {{-- جدول واریزی‌ها --}}
        <div class="overflow-auto">
            <table class="w-full border-collapse">
                <thead>
                <tr class="bg-gray-200">
                    <th class="p-2 border">#</th>
                    <th class="p-2 border">مبلغ</th>
                    <th class="p-2 border">تاریخ میلادی</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($transactions as $t)
                    <tr>
                        <td class="p-2 border">{{ $t->id }}</td>
                        <td class="p-2 border">{{ number_format($t->amount) }} تومان</td>
                        <td class="p-2 border">
                            {{ $t->created_at->format('Y-m-d H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="p-2 text-center text-gray-500">هیچ واریزی یافت نشد.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $transactions->links() }}
        </div>

    </div>
@endsection
