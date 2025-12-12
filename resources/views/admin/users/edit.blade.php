@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto bg-white shadow-lg rounded-2xl p-8 mt-6">
        {{-- بالای فرم، بعد از <form> --}}
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-300 text-red-700 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <strong class="font-bold">خطا در اطلاعات وارد شده!</strong>
                </div>
                <ul class="mt-3 list-disc list-inside text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center border-b pb-3">✏️ ویرایش کاربر</h2>

        <form action="{{ route('admin.users.update_user', $user->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2">
                <div class="px-2">
                    <label class="block text-gray-700 font-medium mb-2">نام</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>

                <div class="px-2">
                    <label class="block text-gray-700 font-medium mb-2">نام خانوادگی</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
            </div>

            <div class="grid grid-cols-2">
                <div class="px-2">
                    <label class="block text-gray-700 font-medium mb-2">شماره موبایل</label>
                    <input type="text" name="mobile" value="{{ old('mobile', $user->mobile) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>

                <div class="px-2">
                    <label class="block text-gray-700 font-medium mb-2">تلفن ثابت</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
            </div>

            <div class="grid grid-cols-2">
                <div class="px-2">
                    <label class="block text-gray-700 font-medium mb-2">رمز عبور جدید</label>
                    <input type="password" name="password"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>

                <div class="px-2">
                    <label class="block text-gray-700 font-medium mb-2">تکرار رمز</label>
                    <input type="password" name="password_confirmation"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
            </div>

            <div class="pt-4">
                <button type="submit"
                        class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg">
                    💾 ذخیره تغییرات
                </button>
            </div>
        </form>
    </div>
@endsection
