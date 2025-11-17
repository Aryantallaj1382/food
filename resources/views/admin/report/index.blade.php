@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6" dir="rtl">

        <h2 class="text-2xl font-bold mb-6 text-gray-800">لیست رستوران‌ها</h2>

        <!-- Search Box -->
        <form method="GET" class="mb-6">
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="جستجو بر اساس نام رستوران..."
                class="w-full md:w-1/2 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500"
            >
            <button class="mt-3 md:mt-0 md:mr-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                جستجو
            </button>
        </form>

        <!-- Table -->
        <div class="overflow-x-auto bg-white shadow rounded-lg">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-2 border text-center">#</th>
                    <th class="px-4 py-2 border text-center">نام رستوران</th>
                    <th class="px-4 py-2 border text-center">عملیات</th>
                </tr>
                </thead>

                <tbody>
                @forelse ($restaurants as $restaurant)
                    <tr class="hover:bg-gray-50">
                        <td class="border px-4 py-2 text-center">{{ $restaurant->id }}</td>
                        <td class="border px-4 py-2 text-center font-medium text-gray-700">
                            {{ $restaurant->name }}
                        </td>
                        <td class="border px-4 py-2 text-center">
                            <a href="{{ route('admin.restaurants.reports.show', $restaurant->id) }}"
                               class="px-4 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                مشاهده
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-6 text-center text-gray-500">
                            هیچ رستورانی یافت نشد.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-5">
            {{ $restaurants->links() }}
        </div>

    </div>
@endsection
