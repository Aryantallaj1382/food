@extends('layouts.app')

@section('title', 'لیست معرفی رستوران‌ها')

@section('content')
    <div class="container mx-auto py-8" dir="rtl">
        <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
            <div class="p-5 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-800">لیست معرفی رستوران‌ها</h2>
                @if(session('success'))
                    <div class="bg-green-100 text-green-700 px-3 py-1 rounded-lg text-sm">
                        {{ session('success') }}
                    </div>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse text-sm text-gray-700">
                    <thead class="bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-center border-b">#</th>
                        <th class="px-4 py-3 text-center border-b">نام</th>
                        <th class="px-4 py-3 text-center border-b">نام خانوادگی</th>
                        <th class="px-4 py-3 text-center border-b">موبایل</th>
                        <th class="px-4 py-3 text-center border-b">نام رستوران</th>
                        <th class="px-4 py-3 text-center border-b">آدرس</th>
                        <th class="px-4 py-3 text-center border-b">عملیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($introductions as $intro)
                        <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-100 transition">
                            <td class="px-4  py-3 text-center border-b">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 text-center border-b">{{ $intro->first_name }}</td>
                            <td class="px-4 py-3 text-center border-b">{{ $intro->last_name }}</td>
                            <td class="px-4 py-3  border-b text-center font-mono">{{ $intro->mobile }}</td>
                            <td class="px-4 py-3 text-center border-b">{{ $intro->restaurant_name }}</td>
                            <td class="px-4 py-3 text-center border-b">{{ $intro->address }}</td>
                            <td class="px-4 py-3 text-center border-b">
                                <form action="{{ route('admin.restaurant_introductions.destroy', $intro->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('آیا از حذف این رکورد مطمئن هستید؟')"
                                      class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg shadow transition transform hover:scale-105">
                                        حذف
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-gray-500">هیچ رکوردی یافت نشد.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-gray-200">
                {{ $introductions->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
@endsection
