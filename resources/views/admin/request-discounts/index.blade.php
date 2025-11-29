{{-- resources/views/admin/request-discounts/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">درخواست‌های تخفیف رستوران‌ها</h1>
            <div class="text-sm text-gray-600">
                همه درخواست‌ها با ورود شما خودکار "دیده شده" شدند
            </div>
        </div>

        <div class="bg-white shadow-2xl rounded-xl overflow-hidden">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white">
                <tr>
                    <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider">رستوران</th>
                    <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider">عنوان</th>
                    <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider">توضیحات کامل</th>
                    <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider">تاریخ و ساعت</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @forelse($requests as $req)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <!-- رستوران و شماره -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-900">
                                {{ $req->restaurant?->name ?? 'نامشخص' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $req->restaurant?->user?->mobile ?? '—' }}
                            </div>
                        </td>

                        <!-- عنوان -->
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-indigo-700">
                                {{ $req->title }}
                            </div>
                        </td>

                        <!-- توضیحات کامل (قابل گسترش) -->
                        <td class="px-6 py-4 text-sm text-gray-700 leading-relaxed">
                            <details class="group">
                                <summary class="cursor-pointer list-none font-medium text-indigo-600 hover:text-indigo-800">
                                    <span class="text-xs mr-1">کلیک برای جزئیات کامل</span>
                                </summary>
                                <div class="mt-3 p-4 bg-gray-50 rounded-lg border border-gray-200 whitespace-pre-line">
                                    {{ $req->description }}
                                </div>
                            </details>
                        </td>

                        <!-- تاریخ -->
                        <td class="px-6 py-4 text-xs text-gray-500 text-center whitespace-nowrap">
                            <div class="font-medium">{{ jdate($req->created_at)->format('d F Y') }}</div>
                            <div class="text-xs">{{ $req->created_at->format('H:i') }}</div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-16 text-gray-500 text-lg">
                            هنوز هیچ درخواستی ثبت نشده است
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <!-- صفحه‌بندی -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
@endsection
