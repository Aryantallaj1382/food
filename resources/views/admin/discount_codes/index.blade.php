@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6">
        <div class="flex justify-between mb-4">
            <h1 class="text-2xl font-bold">کدهای تخفیف</h1>
            <a href="{{ route('admin.discount-codes.create') }}" class="px-4 py-2 bg-green-600 text-white rounded">ایجاد کد تخفیف</a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-2 bg-green-200 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <table class="min-w-full bg-white border">
            <thead>
            <tr>
                <th class="border px-4 py-2">نام</th>
                <th class="border px-4 py-2">درصد تخفیف</th>
                <th class="border px-4 py-2">سقف</th>
                <th class="border px-4 py-2">اعتبار تا</th>
                <th class="border px-4 py-2">یکبار مصرف</th>
                <th class="border px-4 py-2">عملیات</th>
            </tr>
            </thead>
            <tbody>
            @foreach($discountCodes as $code)
                <tr>
                    <td class="border px-4 py-2">{{ $code->name }}</td>
                    <td class="border px-4 py-2">{{ $code->percentage }}%</td>
                    <td class="border px-4 py-2">{{ $code->max_discount ?? '-' }}</td>
                    <td class="border px-4 py-2">{{ $code->valid_until?->format('Y-m-d') ?? '-' }}</td>
                    <td class="border px-4 py-2">{{ $code->one_time_use ? 'بله' : 'خیر' }}</td>
                    <td class="border px-4 py-2 flex gap-2">
                        <a href="{{ route('admin.discount-codes.edit', $code->id) }}" class="px-2 py-1 bg-blue-600 text-white rounded">ویرایش</a>
                        <form action="{{ route('admin.discount-codes.destroy', $code->id) }}" method="POST" onsubmit="return confirm('آیا مطمئن هستید؟');">
                            @csrf
                            @method('DELETE')
                            <button class="px-2 py-1 bg-red-600 text-white rounded">حذف</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $discountCodes->links() }}
        </div>
    </div>
@endsection
