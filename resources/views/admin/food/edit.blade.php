{{-- resources/views/admin/food/edit.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6 max-w-4xl">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">
                ویرایش غذای {{ $food->name }} در رستوران
            </h2>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.foods.update', [$restaurant_id, $food->id]) }}" method="POST" enctype="multipart/form-data" id="foodForm">
                @csrf
                @method('PUT')

                <!-- فیلدهای اصلی -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">نام غذا</label>
                        <input type="text" name="name" value="{{ old('name', $food->name) }}"
                               class="w-full p-3 border rounded-lg @error('name') border-red-500 @enderror">
                        @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-2">وضعیت</label>
                        <select name="is_available" class="w-full p-3 border rounded-lg">
                            <option value="1" {{ old('is_available', $food->is_available) == 1 ? 'selected' : '' }}>در دسترس</option>
                            <option value="0" {{ old('is_available', $food->is_available) == 0 ? 'selected' : '' }}>ناموجود</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-2">تصویر</label>
                        <input type="file" name="image" accept="image/*"
                               class="w-full p-2 border rounded-lg @error('image') border-red-500 @enderror">
                        @if($food->image)
                            <img src="{{$food->image }}" alt="تصویر غذا" class="mt-2 w-32 h-32 object-cover rounded">
                        @endif
                        @error('image') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-2">گروه ها</label>
                        <select name="food_categories_id" class="w-full p-3 border rounded-lg">
                            <option value="">بدون دسته</option>
                            @foreach(\App\Models\FoodCategory::all() as $cat)
                                <option value="{{ $cat->id }}" {{ old('food_categories_id', $food->food_categories_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-gray-700 font-medium mb-2">توضیحات</label>
                    <textarea name="description" rows="3" class="w-full p-3 border rounded-lg">{{ old('description', $food->description) }}</textarea>
                </div>

                <!-- بخش گزینه‌ها (دینامیک با JS) -->
                <div class="border-t pt-8">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">گزینه‌های غذا</h3>
                        <button type="button" id="addOptionBtn"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition shadow">
                            + افزودن گزینه
                        </button>
                    </div>

                    <div id="optionsContainer" class="space-y-4">
                        @foreach($food->options as $index => $option)
                            <div class="option-item bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <input type="hidden" name="options[{{ $index }}][id]" value="{{ $option->id }}">

                                    <input type="text" name="options[{{ $index }}][title]" value="{{ old("options.$index.title", $option->name) }}"
                                           class="p-2 border rounded-md text-sm" placeholder="عنوان" required>

                                    <input type="number" name="options[{{ $index }}][price]" value="{{ old("options.$index.price", $option->price) }}"
                                           class="p-2 border rounded-md text-sm" placeholder="قیمت" min="0" step="100" required>

                                    <input type="number" name="options[{{ $index }}][price_discount]" value="{{ old("options.$index.price_discount", $option->price_discount) }}"
                                           class="p-2 border rounded-md text-sm" placeholder="قیمت تخفیفی" min="0" step="100">

                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <input type="hidden" name="options[{{ $index }}][is_available]" value="0">
                                        <input type="checkbox" name="options[{{ $index }}][is_available]" value="1"
                                               class="w-4 h-4 text-green-600 rounded"
                                            {{ old("options.$index.is_available", $option->is_available) ? 'checked' : '' }}>
                                        <label class="text-sm text-gray-700">موجود</label>
                                    </div>

                                    <input type="number" name="options[{{ $index }}][dish_price]" value="{{ old("options.$index.dish_price", $option->dish_price) }}"
                                           class="p-2 border rounded-md text-sm" placeholder="قیمت ظرف" min="0"">
                                </div>
                                <button type="button" class="remove-option text-red-600 hover:text-red-800 text-sm mt-2">حذف</button>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-3 mt-8">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-lg transition font-medium">
                        بروزرسانی
                    </button>
                    <a href="{{ route('admin.foods.restaurant', $restaurant_id) }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg shadow-lg transition font-medium">
                        انصراف
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- JS دینامیک برای گزینه‌ها --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const container = document.getElementById('optionsContainer');
                const addBtn = document.getElementById('addOptionBtn');
                let optionIndex = {{ $food->options->count() }};

                addBtn.addEventListener('click', function () {
                    const div = document.createElement('div');
                    div.className = 'option-item bg-gray-50 p-4 rounded-lg border border-gray-200';

                    div.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="text" name="options[${optionIndex}][title]" placeholder="عنوان"
                       class="p-2 border rounded-md text-sm" required>
                <input type="number" name="options[${optionIndex}][price]" placeholder="قیمت"
                       class="p-2 border rounded-md text-sm" min="0" step="100" required>
                <input type="number" name="options[${optionIndex}][price_discount]" placeholder="قیمت تخفیفی"
                       class="p-2 border rounded-md text-sm" min="0" step="100">
                <div class="flex items-center space-x-2 space-x-reverse">
                    <input type="hidden" name="options[${optionIndex}][is_available]" value="0">
                    <input type="checkbox" name="options[${optionIndex}][is_available]" value="1"
                           class="w-4 h-4 text-green-600 rounded" checked>
                    <label class="text-sm text-gray-700">موجود</label>
                </div>
             <input type="number" name="options[${optionIndex}][dish]" placeholder="تعداد سفارش برای هر ظرف"
                                       class="p-2 border rounded-md text-sm">
                                <input type="number" name="options[${optionIndex}][dish_price]" placeholder="قیمت ظرف"
                                       class="p-2 border rounded-md text-sm">

            </div>
            <button type="button" class="remove-option text-red-600 hover:text-red-800 text-sm mt-2">حذف</button>
        `;

                    container.appendChild(div);
                    optionIndex++;
                });

                container.addEventListener('click', function (e) {
                    if (e.target.classList.contains('remove-option')) {
                        const items = container.querySelectorAll('.option-item');
                        if (items.length > 1) {
                            e.target.closest('.option-item').remove();
                        } else {
                            alert('حداقل یک گزینه باید وجود داشته باشد.');
                        }
                    }
                });
            });
        </script>
    @endpush

@endsection
