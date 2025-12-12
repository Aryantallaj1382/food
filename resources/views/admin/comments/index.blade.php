@php use Morilog\Jalali\Jalalian; @endphp
@extends('layouts.app')

@section('title', 'مدیریت کامنت‌ها')

@section('content')
    <div class="container mx-auto py-6" dir="rtl">
        <h1 class="text-3xl font-extrabold text-gray-800 mb-6 text-center">
            لیست کامنت‌ها
        </h1>

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="w-full border-collapse text-center">
                <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="p-3">#</th>
                    <th class="p-3">کاربر</th>
                    <th class="p-3">رستوران</th>
                    <th class="p-3">سفارش</th>
                    <th class="p-3">امتیاز</th>
                    <th class="p-3">متن کامنت</th>
                    <th class="p-3">تاریخ ثبت</th>
                    <th class="p-3">عملیات</th>

                </tr>
                </thead>
                <tbody>
                @forelse ($comments as $comment)
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="p-3">{{ $comment->id }}</td>
                        <td class="p-3 font-medium text-gray-800">{{ $comment->user->name ?? '---' }}</td>
                        <td class="p-3 text-blue-700 font-semibold">
                            {{ $comment->order?->restaurant?->name ?? '---' }}
                        </td>
                        <td class="p-3 text-blue-900 font-semibold">
                            <a href=" {{route('admin.restaurants.order' ,$comment->order_id)}}"> مشاهده</a>

                        </td>
                        <td class="p-3">
                            <span class="text-yellow-500 font-bold">
                                ⭐ {{ $comment->rating }}
                            </span>
                        </td>
                        <td class="p-3">
                            <div class="space-y-4">

                                <!-- کامنت کاربر -->
                                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                    <p class="text-sm text-gray-800 leading-relaxed">
                                        {{ \Illuminate\Support\Str::limit($comment->text, 120) }}
                                    </p>
                                    <div class="flex items-center justify-between mt-3 text-xs text-gray-500">
                <span class="font-medium">
                    {{ $comment->user->name ?? 'کاربر مهمان' }}
                </span>
                                        <span>
                    {{ Jalalian::forge($comment->created_at)->format('Y/m/d H:i') }}
                </span>
                                    </div>
                                </div>

                                <!-- پاسخ رستوران (اگر وجود داشت) — بدون دکمه، فقط نمایش -->
                                @if($comment->replies->isNotEmpty())
                                    @foreach($comment->replies as $reply)
                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mr-8 relative">
                                            <!-- فلش کوچک سمت چپ برای حس چت -->
                                            <div class="absolute -left-2 top-5 w-0 h-0
                                border-t-8 border-b-8 border-r-8
                                border-transparent border-r-blue-200">
                                            </div>

                                            <div class="flex items-start gap-3">
                                                <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                </svg>
                                                <div class="flex-1">
                                                    <p class="text-xs font-bold text-blue-800 mb-1">پاسخ رستوران:</p>
                                                    <p class="text-sm text-gray-800 leading-relaxed">
                                                        {{ $reply->text }}
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-2">
                                                        {{ $reply->user?->name ?? 'مدیر' }}
                                                        <span class="mx-2">•</span>
                                                        {{ Jalalian::forge($reply->created_at)->ago() }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                            </div>
                        </td>
                        <td class="p-3 text-gray-600">
                            {{ Jalalian::forge($comment->created_at)->format('Y/m/d') }}
                        </td>
                        <td class="p-3 flex justify-center gap-2">

                            {{-- حذف کامنت --}}
                            <form action="{{ route('admin.comments.destroy', $comment->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('آیا از حذف این کامنت مطمئن هستید؟');">

                                @csrf
                                @method('DELETE')

                                <button class="bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700 transition">
                                    حذف
                                </button>
                            </form>

                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-6 text-gray-500">هیچ کامنتی یافت نشد</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- صفحه‌بندی --}}
        <div class="mt-6">
            {{ $comments->links('pagination::tailwind') }}
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.reply-toggle').forEach(function (toggleBtn) {
                toggleBtn.addEventListener('click', function () {
                    const target = document.getElementById(this.dataset.id);
                    if (target.classList.contains('hidden')) {
                        target.classList.remove('hidden');
                        this.textContent = 'بستن';
                    } else {
                        target.classList.add('hidden');
                        this.textContent = 'مشاهده ی ریپلای';
                    }
                });
            });
        });
    </script>
@endsection
