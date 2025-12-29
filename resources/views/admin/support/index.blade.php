@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <!-- کارت اصلی -->
            <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
                <!-- هدر کارت با گرادیانت -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-8 py-6">
                    <h4 class="text-2xl font-bold text-white text-center sm:text-right">
                        تنظیمات شماره‌های پشتیبانی
                    </h4>
                    <p class="text-blue-100 text-center sm:text-right mt-2 text-sm">
                        شماره‌های تماس مشتریان با پشتیبانی را اینجا مدیریت کنید
                    </p>
                </div>

                <!-- بدنه کارت -->
                <div class="p-8 lg:p-10">
                    <!-- پیام موفقیت -->
                    @if(session('success'))
                        <div class="mb-8 p-4 bg-green-100 border border-green-300 text-green-800 rounded-xl flex items-center gap-3 shadow-sm">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                    @endif

                    <!-- فرم -->
                    <form action="{{ route('admin.support.update') }}" method="POST" class="space-y-7">
                        @csrf
                        @method('PUT')

                        <!-- شماره اول -->
                        <div class="group">
                            <label for="mobile1" class="block text-sm font-semibold text-gray-700 mb-2">
                                شماره پشتیبانی اول <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    name="mobile1"
                                    id="mobile1"
                                    dir="ltr"
                                    class="block w-full pr-12 pl-4 py-4 text-lg border-2 border-gray-300 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition duration-200 placeholder-gray-400"
                                    value="{{ old('mobile1', $mobile1) }}"
                                    placeholder="0912 123 4567"
                                    required
                                >
                            </div>
                        </div>

                        <!-- شماره دوم -->
                        <div class="group">
                            <label for="mobile2" class="block text-sm font-semibold text-gray-700 mb-2">
                                شماره پشتیبانی دوم
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    name="mobile2"
                                    id="mobile2"
                                    dir="ltr"
                                    class="block w-full pr-12 pl-4 py-4 text-lg border-2 border-gray-300 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition duration-200 placeholder-gray-400"
                                    value="{{ old('mobile2', $mobile2) }}"
                                    placeholder="021 8877 6655"
                                >
                            </div>
                        </div>

                        <!-- شماره سوم -->
                        <div class="group">
                            <label for="mobile3" class="block text-sm font-semibold text-gray-700 mb-2">
                                شماره پشتیبانی سوم (اختیاری)
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    name="mobile3"
                                    id="mobile3"
                                    dir="ltr"
                                    class="block w-full pr-12 pl-4 py-4 text-lg border-2 border-gray-300 rounded-xl focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition duration-200 placeholder-gray-400"
                                    value="{{ old('mobile3', $mobile3) }}"
                                    placeholder="مثلاً شماره واتساپ"
                                >
                            </div>
                        </div>

                        <!-- دکمه ذخیره -->
                        <div class="pt-6">
                            <button
                                type="submit"
                                class="w-full sm:w-auto px-10 py-4 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-bold text-lg rounded-xl shadow-lg transform transition duration-200 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-indigo-300"
                            >
                                ذخیره تغییرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
