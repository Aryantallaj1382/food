@extends('layouts.app')

@section('title', 'داشبورد مدیریت')

@section('content')
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- کل کاربران --}}
            <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col items-center justify-center hover:shadow-xl transition hover:-translate-y-1">
                <div class="p-3 bg-blue-100 rounded-full mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.19 0 4.256.536 6.121 1.804M12 7a4 4 0 110 8 4 4 0 010-8z" />
                    </svg>
                </div>
                <p class="text-sm text-gray-500 text-center">کل کاربران</p>
                <h2 class="text-2xl font-bold mt-1 text-center">{{$user_count}}</h2>
            </div>

            {{-- موجودی سایت --}}
            <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col items-center justify-center hover:shadow-xl transition hover:-translate-y-1">
                <div class="p-3 bg-green-100 rounded-full mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-3.866 0-7 1.343-7 3v3c0 1.657 3.134 3 7 3s7-1.343 7-3v-3c0-1.657-3.134-3-7-3z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8V5m0 0V3m0 2h6m-6 0H6" />
                    </svg>
                </div>
                <p class="text-sm text-gray-500 text-center">موجودی سایت</p>
                <h2 class="text-2xl font-bold mt-1 text-center">5 یوان</h2>
            </div>

            {{-- کل اساتید --}}
            <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col items-center justify-center hover:shadow-xl transition hover:-translate-y-1">
                <div class="p-3 bg-purple-100 rounded-full mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7a4 4 0 118 0 4 4 0 01-8 0zM6 21v-2a4 4 0 014-4h4a4 4 0 014 4v2" />
                    </svg>
                </div>
                <p class="text-sm text-gray-500 text-center">کل اساتید</p>
                <h2 class="text-2xl font-bold mt-1 text-center">10</h2>
            </div>

            {{-- کل دانش آموزان --}}
            <div class="bg-white rounded-xl shadow-lg p-6 flex flex-col items-center justify-center hover:shadow-xl transition hover:-translate-y-1">
                <div class="p-3 bg-yellow-100 rounded-full mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.19 0 4.256.536 6.121 1.804M12 7a4 4 0 110 8 4 4 0 010-8z" />
                    </svg>
                </div>
                <p class="text-sm text-gray-500 text-center">کل دانش آموزان</p>
                <h2 class="text-2xl font-bold mt-1 text-center">100</h2>
            </div>

        </div>

        {{-- جدول سفارشات اخیر --}}
        {{-- کلاس های خصوصی --}}
        <div class="bg-white rounded-xl shadow-md p-6 mb-6 border-t-4 border-blue-500">
            <h3 class="text-xl font-bold mb-4 text-blue-700">کلاس های خصوصی اخیر</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-300 border">
                    <thead class="bg-blue-100">
                    <tr>
                        <th class="text-center px-4 py-2 text-sm font-medium text-blue-800">نام دانش آموز</th>
                        <th class="text-center px-4 py-2 text-sm font-medium text-blue-800">نام استاد</th>
                        <th class="text-center px-4 py-2 text-sm font-medium text-blue-800">نوع کلاس</th>
                        <th class="text-center px-4 py-2 text-sm font-medium text-blue-800">تعداد جلسات</th>
                        <th class="text-center px-4 py-2 text-sm font-medium text-blue-800">مشاهده</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
{{--                    @foreach($private as $private_class)--}}
{{--                        <tr class="hover:bg-blue-50 transition">--}}
{{--                            <td class="text-center px-4 py-2">{{$private_class->user->name}}</td>--}}
{{--                            <td class="text-center px-4 py-2">{{$private_class->professor->name}}</td>--}}
{{--                            <td class="text-center px-4 py-2">{{$private_class->class_type}}</td>--}}
{{--                            <td class="text-center px-4 py-2 text-green-600 font-semibold">{{$private_class->sessions_count}}</td>--}}
{{--                            <td class="text-center px-4 py-2">--}}
{{--                                <a href="{{ route('admin.private-classes.show', $private_class->id) }}"--}}
{{--                                   class="inline-block px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition">--}}
{{--                                    جزئیات--}}
{{--                                </a>--}}
{{--                            </td>--}}
{{--                        </tr>--}}
{{--                    @endforeach--}}
                    </tbody>
                </table>
            </div>
        </div>

        {{-- نمودار درآمد --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold mb-4">نمودار درآمد ماهانه</h3>
            <canvas id="salesChart" height="150"></canvas>
        </div>

    </div>

    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور'],
                datasets: [{
                    label: 'درآمد',
                    data: [1200, 1900, 3000, 2500, 3200, 4000],
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
@endsection
