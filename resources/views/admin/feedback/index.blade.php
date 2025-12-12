{{-- resources/views/admin/feedback/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">مدیریت نظرات و فیدبک‌های کاربران</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="px-6 py-4 text-right text-xs font-bold uppercase">کاربر</th>
                    <th class="px-6 py-4 text-right text-xs font-bold uppercase">متن نظر</th>
                    <th class="px-6 py-4 text-center text-xs font-bold uppercase">امتیاز</th>
                    <th class="px-6 py-4 text-center text-xs font-bold uppercase">تاریخ</th>
                    <th class="px-6 py-4 text-right text-xs font-bold uppercase">وضعیت</th>
                    <th class="px-6 py-4 text-center text-xs font-bold uppercase">پاسخ ادمین</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                @forelse($feedbacks as $feedback)
                    <tr class="{{ $feedback->answer ? 'bg-green-50' : 'bg-red-50' }} hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm">
                            <div class="font-medium">{{ $feedback->user->name ?? 'کاربر حذف شده' }}</div>
                            <div class="text-gray-500 text-xs">{{ $feedback->user->mobile ?? '-' }}</div>
                        </td>

                        <td class="px-6 py-4 text-sm max-w-md">
                            <div class="line-clamp-3">{{ $feedback->text }}</div>
                        </td>

                        <td class="px-6 py-4 text-center text-2xl font-bold text-yellow-600">
                            {{ $feedback->rating }}
                        </td>

                        <td class="px-6 py-4 text-xs text-gray-500 text-center">
                            {{ jdate($feedback->created_at)->format('d F Y') }}
                            <br>
                            <span class="text-xs">{{ $feedback->created_at->format('H:i') }}</span>
                        </td>

                        <td class="px-6 py-4 text-sm text-center">
                            @if($feedback->answer)
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold">پاسخ داده شده</span>
                            @else
                                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-bold">در انتظار پاسخ</span>
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            <form action="{{ route('admin.feedback.update', $feedback) }}" method="POST" class="max-w-xs">
                                @csrf
                                @method('PUT')

                                <textarea
                                    name="answer"
                                    rows="3"
                                    class="w-full text-xs border rounded-lg px-3 py-2 focus:outline-none focus:border-indigo-500 resize-none {{ $feedback->answer ? 'border-green-300 bg-green-50' : 'border-gray-300' }}"
                                    placeholder="پاسخ خود را اینجا بنویسید..."
                                    required>{{ old('answer', $feedback->answer) }}</textarea>

                                <div class="mt-2 flex gap-2">
                                    <button type="submit"
                                            class="flex-1 bg-indigo-600 text-white text-xs py-2 rounded hover:bg-indigo-700 transition">
                                        {{ $feedback->answer ? 'به‌روزرسانی پاسخ' : 'ارسال پاسخ' }}
                                    </button>


                                </div>
                            </form>
                        </td>                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-12 text-gray-500 text-lg">
                            هنوز هیچ نظری ثبت نشده است
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- صفحه‌بندی -->
        <div class="mt-6">
            {{ $feedbacks->links() }}
        </div>
    </div>
    </div>
@endsection
