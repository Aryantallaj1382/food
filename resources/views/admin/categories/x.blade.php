@extends('layouts.app')

@section('title', 'مدیریت دسته‌بندی‌ها')

@section('content')
    <div class="container mx-auto py-6" dir="rtl">
        <h1 class="text-3xl font-extrabold text-gray-800 mb-6 text-center">📂 لیست دسته‌بندی‌ها</h1>


        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="w-full text-center border-collapse">
                <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="p-3">#</th>
                    <th class="p-3">نام</th>
                    <th class="p-3">اسلاگ</th>
                    <th class="p-3">آیکون</th>
                    <th class="p-3">تاریخ ایجاد</th>
                    <th class="p-3">عملیات</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($categories as $category)
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="p-3">{{ $category->id }}</td>
                        <td class="p-3 font-semibold">{{ $category->name }}</td>
                        <td class="p-3 text-gray-600">{{ $category->slug }}</td>
                        <td class="p-3 text-gray-700">
                            @if($category->icon)
                                <img src="{{ $category->icon }}" class="w-10 h-10 object-cover rounded-full mx-auto">
                            @else
                                ---
                            @endif
                        </td>

                        <td class="p-3 text-gray-500">
                            {{ \Morilog\Jalali\Jalalian::forge($category->created_at)->format('Y/m/d') }}
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-3">

                                <!-- ویرایش -->
                                <a href="#"
                                   class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-700 hover:bg-emerald-200 px-4 py-2 rounded-lg transition font-medium text-sm shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    ویرایش
                                </a>

                                <!-- حذف -->
                                <form action="{{ route('admin.category.delete', $category) }}"
                                      method="POST"
                                      onsubmit="return confirm('⚠️ آیا مطمئن هستید که می‌خواهید «{{ $category->name }}» را حذف کنید؟ این عمل قابل بازگشت نیست!')"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 bg-red-100 text-red-700 hover:bg-red-200 px-4 py-2 rounded-lg transition font-medium text-sm shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        حذف
                                    </button>
                                </form>
                            </div>
                        </td>


                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-6 text-gray-500">هیچ دسته‌بندی‌ای یافت نشد.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $categories->links('pagination::tailwind') }}
        </div>
    </div>
@endsection
