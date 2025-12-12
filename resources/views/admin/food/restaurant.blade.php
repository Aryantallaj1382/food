@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6">
        @if(session('success'))
            <div
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 3000)"
                class="mb-4 p-4 rounded-lg bg-green-100 border border-green-400 text-green-700 flex justify-between items-center"
            >
                <span>{{ session('success') }}</span>
                <button @click="show = false" class="text-green-700 hover:text-green-900 font-bold">&times;</button>
            </div>
        @endif
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- فرم فیلتر -->
            <div class="mb-6 flex flex-col md:flex-row md:items-center gap-4">
                <form method="GET" action="{{ route('admin.foods.restaurant', $rest->id) }}" class="flex flex-wrap gap-2 items-center">
                    <!-- جستجو بر اساس نام غذا -->
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="جستجو بر اساس نام غذا..."
                           class="border border-gray-300 rounded px-3 py-2 text-sm">

                    <!-- فیلتر بر اساس دسته بندی -->
                    <select name="category_id" class="border border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="">همه دسته‌بندی‌ها</option>
                        @foreach(\App\Models\FoodCategory::orderBy('name')->get() as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm">
                        فیلتر
                    </button>
                </form>
            </div>

        <!-- دکمه اضافه کردن غذا -->
        <div class="flex justify-start md:justify-end mb-6">
            <a href="{{ route('admin.foods.create', $rest->id) }}"
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow transition">
                اضافه کردن غذای جدید
            </a>
        </div>

        <h2 class="text-2xl font-bold text-gray-700 mb-6">لیست غذاهای {{ $rest->name }}</h2>

        <!-- جدول غذاها -->
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full table-auto border-collapse">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تصویر</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">گروه</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نام غذا</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">قیمت</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">توضیحات</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عملیات</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @forelse($foods as $food)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <img src="{{ $food->image ?? asset('images/default-food.jpg') }}"
                                 alt="{{ $food->name }}"
                                 class="w-16 h-16 object-cover rounded-lg shadow-sm">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $food->category->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <div class="font-semibold">{{ $food->name }}</div>

                            @if($food->options && $food->options->count() > 0)
                                <ul class="ml-4 mt-1 text-sm space-y-1">
                                    @foreach($food->options as $option)
                                        <li class="flex items-center justify-start gap-2">
                                            <span>{{ $option->name }}</span>
                                            <span class="px-2 py-0.5 rounded-full text-xs font-bold
                        {{ $option->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $option->is_available ? 'فعال' : 'غیرفعال' }}
                    </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </td>


                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ number_format($food->first?->options?->price) }} تومان
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                            {{ Str::limit($food->description, 50) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2 space-x-reverse">
                            <form action="{{ route('admin.foods.toggle', $food->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        class="{{ $food->is_available ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-400 hover:bg-gray-500' }} text-white px-2 py-1 rounded text-xs">
                                    {{ $food->is_available ? 'غیرفعال کن' : 'فعال کن' }}
                                </button>
                            </form>

                            <!-- دکمه فعال کردن همه آپشن‌ها -->
                            <form action="{{ route('admin.foods.activateAllOptions', $food->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs">
                                    فعال کردن همه آپشن‌ها
                                </button>
                            </form>

                            <a href="{{route('admin.foods.edit', $food->id)}}"
                               class="text-indigo-600 hover:text-indigo-900">ویرایش</a>
                            <form action="{{route('admin.foods.destroy', $food->id)}}"
                                  method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('آیا از حذف این غذا مطمئن هستید؟')"
                                        class="text-red-600 hover:text-red-900">
                                    حذف
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            هیچ غذایی برای این رستوران ثبت نشده است.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- صفحه‌بندی -->
        <div class="mt-6">
            {{ $foods->links() }}
        </div>
    </div>
@endsection
