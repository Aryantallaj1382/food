@extends('layouts.app')

@section('title', 'مدیریت کاربران')

@section('content')
    <div class="container py-4" dir="rtl">
        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg shadow transition transform hover:scale-105">
ایجاد کاربر        </a>

        <h3 class="text-2xl font-bold mb-4">👥 لیست کاربران</h3>
        <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4 flex flex-wrap gap-3 items-end" dir="rtl">
            {{-- جست‌وجو --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">جست‌وجو (نام یا موبایل)</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="مثلاً: علی یا 0912..."
                       class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            {{-- فیلتر کیف پول --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">فیلتر کیف پول</label>
                <select name="wallet_balance"
                        class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">همه</option>
                    <option value="has_balance" {{ request('wallet_balance') == 'has_balance' ? 'selected' : '' }}>دارای موجودی</option>
                    <option value="zero_balance" {{ request('wallet_balance') == 'zero_balance' ? 'selected' : '' }}>موجودی صفر</option>
                    <option value="no_wallet" {{ request('wallet_balance') == 'no_wallet' ? 'selected' : '' }}>بدون کیف پول</option>
                </select>
            </div>

            {{-- فیلتر وفاداری --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">مرتب‌سازی بر اساس</label>
                <select name="sort"
                        class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">جدیدترین</option>
                    <option value="loyal" {{ request('sort') == 'loyal' ? 'selected' : '' }}>وفاداری (تعداد خرید)</option>
                </select>
            </div>

            <div>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    اعمال فیلتر 🔍
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition">
                    پاک کردن
                </a>
            </div>
        </form>

        <div class="bg-white rounded-xl shadow p-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 table-auto">
                <thead class="bg-gray-100">
                <tr>
                    <th class="text-center px-4 py-2  text-sm font-medium text-gray-700">#</th>
                    <th class="text-center px-4 py-2  text-sm font-medium text-gray-700">نام</th>
                    <th class="text-center px-4 py-2  text-sm font-medium text-gray-700">موبایل</th>
                    <th class="text-center px-4 py-2  text-sm font-medium text-gray-700">تاریخ ثبت‌نام</th>
                    <th class="text-center px-4 py-2 text-sm font-medium text-gray-700">موجودی کیف پول</th>
                    <th class="text-center px-4 py-2 text-sm font-medium text-gray-700">تعداد خرید</th>

                    <th class="text-center px-4 py-2  text-sm font-medium text-gray-700">عملیات</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                @foreach($users as $user)
                    <tr class="h-12">
                        <td class="text-center px-4 py-2">{{ $user->id }}</td>
                        <td class="text-center px-4 py-2">{{ $user->name }}</td>
                        <td class="text-center px-4 py-2">{{ $user->mobile }}</td>
                        <td class="text-center px-4 py-2">{{ $user->created_at?->format('Y/m/d') }}</td>
                        <td class="text-center px-4 py-2">
                            {{ number_format($user->wallet->balance ?? 0) }} تومان
                        </td>
                        <td class="text-center px-4 py-2">
                            {{ $user->orders_count ?? $user->orders()->count() }}
                        </td>

                        <td class="text-center px-4 py-2">
                            <div class="flex items-center justify-center gap-2">
                                <!-- دکمه مشاهده -->
                                <a href="{{route('admin.users.show',$user->id)}}"
                                   class="inline-flex items-center gap-1 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200
                  px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200">
                                    👁 مشاهده
                                </a>

                                <!-- دکمه حذف -->
                                <form action="{{route('admin.users.delete' , $user->id)}}" method="POST"
                                      onsubmit="return confirm('آیا از حذف این کاربر مطمئن هستید؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 text-red-600 bg-red-50 hover:bg-red-100 border border-red-200
                           px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200">
                                        🗑 حذف
                                    </button>
                                </form>
                            </div>
                        </td>


                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
