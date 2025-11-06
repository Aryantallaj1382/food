@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto bg-white shadow-lg rounded-2xl p-8 mt-6">
        {{-- نمایش پیام موفقیت --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- نمایش پیام خطای عمومی --}}
        @if(session('error'))
            <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        {{-- لیست ارورهای ولیدیشن --}}
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded mb-4">
                <strong class="block mb-2">لطفاً موارد زیر را اصلاح کنید:</strong>
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center border-b pb-3">➕ ایجاد کاربر جدید</h2>

        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- نام -->
            <div class="grid grid-cols-2">
                <div class="px-2">
                    <label class="block text-gray-700 font-medium mb-2">نام</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                    @error('first_name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="px-2">
                    <label class="block text-gray-700 font-medium mb-2">نام خانوادگی</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                    @error('last_name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>


            <!-- موبایل -->
            <div class="grid grid-cols-2">
                <div class="px-2">
                    <label class="block text-gray-700 font-medium mb-2">شماره موبایل</label>
                    <input type="text" name="mobile" value="{{ old('mobile') }}" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                    @error('mobile') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="px-2">
                    <label class="block text-gray-700 font-medium mb-2">تلفن ثابت</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                    @error('phone ') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>


            <!-- رمز عبور -->
            <div class="grid grid-cols-2">

            <div class="px-2">
                <label class="block text-gray-700 font-medium mb-2">رمز عبور</label>
                <input type="password" name="password" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
                @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- تکرار رمز -->
            <div class="px-2" >
                <label class="block text-gray-700 font-medium mb-2">تکرار رمز عبور</label>
                <input type="password" name="password_confirmation" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:outline-none">
            </div>
            </div>

            <!-- دکمه -->
            <div class="pt-4">
                <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                    ✅ ایجاد کاربر
                </button>
            </div>
        </form>
    </div>
@endsection
