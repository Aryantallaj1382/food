@extends('layouts.app')

@section('title', 'ویرایش غذا')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">ویرایش غذا</h2>

        <form action="{{ route('admin.foods.update', [$restaurant_id, $food->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- نام غذا -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">نام غذا</label>
                <input type="text" name="name" value="{{ old('name', $food->name) }}"
                       class="w-full p-3 border rounded-md @error('name') border-red-500 @enderror" required>
                @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- قیمت پایه -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">قیمت پایه (تومان)</label>
                <input type="number" name="price" value="{{ old('price', $food->price) }}"
                       class="w-full p-3 border rounded-md @error('price') border-red-500 @enderror" min="0" step="100" required>
                @error('price')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- توضیحات -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">توضیحات (اختیاری)</label>
                <textarea name="description" rows="3"
                          class="w-full p-3 border rounded-md @error('description') border-red-500 @enderror">{{ old('description', $food->description) }}</textarea>
                @error('description')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- تصویر فعلی -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">تصویر فعلی</label>
                @if($food->image)
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <img src="{{ $food->image }}" alt="{{ $food->name }}"
                             class="w-24 h-24 object-cover rounded-md border">
                        <span class="text-xs text-gray-500">({{ basename($food->image) }})</span>
                    </div>
                @else
                    <p class="text-gray-500 text-sm">تصویری انتخاب نشده</p>
                @endif
            </div>

            <!-- آپلود تصویر جدید -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">تصویر جدید (اختیاری)</label>
                <input type="file" name="image" accept="image/*"
                       class="w-full p-2 border rounded-md @error('image') border-red-500 @enderror">
                @error('image')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- گزینه‌های غذا -->
            <div class="border-t pt-8">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">گزینه‌های غذا</h3>
                    <button type="button" id="addOptionBtn"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition shadow">
                        + افزودن گزینه
                    </button>
                </div>

                <div id="optionsContainer" class="space-y-4">
                    <!-- گزینه‌های موجود از دیتابیس -->
                    @foreach($food->options as $index => $option)
                        <div class="option-item bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <!-- عنوان -->
                                <input type="text" name="options[{{ $index }}][title]"
                                       value="{{ old("options.$index.name", $option->name) }}"
                                       placeholder="عنوان (مثلاً: کوچک)"
                                       class="p-2 border rounded-md text-sm @error("options.$index.title") border-red-500 @enderror" required>

                                <!-- قیمت -->
                                <input type="number" name="options[{{ $index }}][price]"
                                       value="{{ old("options.$index.price", $option->price) }}"
                                       placeholder="قیمت"
                                       class="p-2 border rounded-md text-sm @error("options.$index.price") border-red-500 @enderror" min="0" step="100" required>

                                <!-- قیمت تخفیفی -->
                                <input type="number" name="options[{{ $index }}][price_discount]"
                                       value="{{ old("options.$index.price_discount", $option->price_discount) }}"
                                       placeholder="قیمت تخفیفی"
                                       class="p-2 border rounded-md text-sm @error("options.$index.price_discount") border-red-500 @enderror" min="0" step="100">

                                <!-- موجود بودن -->
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <input type="hidden" name="options[{{ $index }}][is_available]" value="0">
                                    <input type="checkbox" name="options[{{ $index }}][is_available]" value="1"
                                           {{ old("options.$index.is_available", $option->is_available) ? 'checked' : '' }}
                                           class="w-4 h-4 text-green-600 rounded">
                                    <label class="text-sm text-gray-700">موجود</label>
                                </div>

                                <!-- قیمت ظرف -->
                                <input type="number" name="options[{{ $index }}][dish_price]"
                                       value="{{ old("options.$index.dish_price", $option->dish_price) }}"
                                       placeholder="قیمت ظرف"
                                       class="p-2 border rounded-md text-sm @error("options.$index.dish_price") border-red-500 @enderror" min="0" step="100">
                            </div>

                            <button type="button" class="remove-option text-red-600 hover:text-red-800 text-sm mt-2">
                                حذف
                            </button>
                        </div>
                    @endforeach
                </div>

                <!-- نمایش خطاهای گزینه‌ها -->
                @foreach($errors->get('options.*.*') as $error)
                    <p class="text-red-500 text-xs mt-1">{{ $error[0] }}</p>
                @endforeach
            </div>

            <!-- دکمه‌ها -->
            <div class="flex justify-end space-x-2 space-x-reverse mt-8">
                <a href="{{ route('admin.foods.restaurant', $restaurant_id) }}"
                   class="px-5 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    انصراف
                </a>
                <button type="submit"
                        class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    ذخیره تغییرات
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('optionsContainer');
            const addBtn = document.getElementById('addOptionBtn');
            let optionIndex = {{ $food->options->count() }}; // از تعداد فعلی شروع کن

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

                    <input type="number" name="options[${optionIndex}][dish_price]" placeholder="قیمت ظرف"
                           class="p-2 border rounded-md text-sm" min="0" step="100">
                </div>

                <button type="button" class="remove-option text-red-600 hover:text-red-800 text-sm mt-2">
                    حذف
                </button>
            `;

                container.appendChild(div);
                optionIndex++;
            });

            // حذف گزینه
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
