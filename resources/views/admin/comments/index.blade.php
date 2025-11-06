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
                        <td class="p-3 text-gray-700 max-w-xs truncate" title="{{ $comment->text }}">
                            {{ \Illuminate\Support\Str::limit($comment->text, 60) }}

                            @if ($comment->replies->count() > 0)
                                <span
                                    class="text-blue-600 cursor-pointer hover:underline ml-2 reply-toggle"
                                    data-id="replies-{{ $comment->id }}"
                                >
                                مشاهده ریپلای
                            </span>

                                <div id="replies-{{ $comment->id }}"
                                     class="hidden mt-2 bg-gray-50 border rounded-lg p-3 text-right shadow-md">
                                    @foreach ($comment->replies as $reply)
                                        <div class="border-b border-gray-200 py-2">
                                            <p class="text-sm text-gray-800">
                                                <strong>{{ $reply->user->name ?? '---' }}:</strong>
                                                {{ $reply->text }}
                                            </p>
                                            <span class="text-xs text-gray-500">
                        {{ Jalalian::forge($reply->created_at)->format('Y/m/d') }}
                    </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </td>

                        <td class="p-3 text-gray-600">
                            {{ Jalalian::forge($comment->created_at)->format('Y/m/d') }}
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
