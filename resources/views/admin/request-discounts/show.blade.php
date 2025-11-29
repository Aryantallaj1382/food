{{-- resources/views/admin/restaurant-discounts/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8 text-gray-800 flex items-center gap-3">
            مدیریت تخفیف‌های رستوران‌ها
        </h1>

        <div class="bg-white shadow-2xl rounded-xl overflow-hidden">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white">
                <tr>
                    <th class="px-6 py-4 text-right text-xs font-bold uppercase">رستوران</th>
                    <th class="px-6 py-4 text-center text-xs font-bold uppercase">تعم‌دار</th>
                    <th class="px-6 py-4 text-center text-xs font-bold uppercase">خوش مزه</th>
                    <th class="px-6 py-4 text-center text-xs font-bold uppercase">اولین سفارش</th>
                    <th class="px-6 py-4 text-center text-xs font-bold uppercase">کد تخفیف</th>
                    <th class="px-6 py-4 text-center text-xs font-bold uppercase">ارسال رایگان</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                @forelse($discounts as $discount)
                    <tr class="hover:bg-gray-50 transition">
                        <!-- نام رستوران و موبایل -->
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">
                                {{ $discount->restaurant?->name ?? 'رستوران حذف شده' }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $discount->restaurant?->user?->mobile ?? '—' }}
                            </div>
                        </td>

                        <!-- تعم‌دار -->
                        <td class="px-6 py-4 text-center">
                            @if($discount->tam_dar_status)
                                <div class="text-green-600 font-bold">فعال</div>
                                <div class="text-xs text-gray-600 mt-1" title="{{ $discount->tam_dar_text }}">
                                    {{ Str::limit($discount->tam_dar_text, 30) }}
                                </div>
                            @else
                                <span class="text-gray-400">غیرفعال</span>
                            @endif
                        </td>

                        <!-- خوش‌آمدگویی -->
                        <td class="px-6 py-4 text-center">
                            @if($discount->khosh_status)
                                <div class="text-green-600 font-bold">فعال</div>
                                <div class="text-xs text-gray-600 mt-1" title="{{ $discount->khosh_text }}">
                                    {{ Str::limit($discount->khosh_text, 30) }}
                                </div>
                            @else
                                <span class="text-gray-400">غیرفعال</span>
                            @endif
                        </td>

                        <!-- اولین سفارش -->
                        <td class="px-6 py-4 text-center">
                            @if($discount->first_status)
                                <div class="text-green-600 font-bold">فعال</div>
                                <div class="text-xs text-gray-600 mt-1" title="{{ $discount->first_text }}">
                                    {{ Str::limit($discount->first_text, 30) }}
                                </div>
                            @else
                                <span class="text-gray-400">غیرفعال</span>
                            @endif
                        </td>

                        <!-- کد تخفیف -->
                        <td class="px-6 py-4 text-center">
                            @if($discount->code_status)
                                <div class="text-green-600 font-bold">فعال</div>
                                <div class="text-xs text-gray-600 mt-1" title="{{ $discount->code_text }}">
                                    {{ Str::limit($discount->code_text, 30) }}
                                </div>
                            @else
                                <span class="text-gray-400">غیرفعال</span>
                            @endif
                        </td>

                        <!-- ارسال رایگان -->
                        <td class="px-6 py-4 text-center">
                            @if($discount->send_status)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                    فعال
                                </span>
                            @else
                                <span class="text-gray-400">غیرفعال</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-16 text-gray-500 text-lg">
                            هنوز هیچ رستورانی تخفیف فعال نکرده است
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <!-- صفحه‌بندی -->
            <div class="bg-gray-50 px-6 py-4 border-t">
                {{ $discounts->links() }}
            </div>
        </div>
    </div>
@endsection
