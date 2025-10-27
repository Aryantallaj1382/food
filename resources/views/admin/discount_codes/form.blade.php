@php
    $isEdit = isset($discountCode);
@endphp

<form action="{{ $isEdit ? route('admin.discount-codes.update', $discountCode->id) : route('admin.discount-codes.store') }}" method="POST">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="mb-4">
        <label>کد</label>
        <div class="flex gap-2">
            <input type="text" id="discountCodeInput" name="name" value="{{ old('name', $discountCode?->name ?? '') }}" class="border p-2 w-full">
            <button type="button" id="generateCodeBtn" class="px-4 py-2 bg-blue-600 text-white rounded  min-w-40 max-w-48">ایجاد کد تصادفی</button>
        </div>
        @error('name')<div class="text-red-600">{{ $message }}</div>@enderror
    </div>


    <div class="grid grid-cols-2 gap-4">
        <div class="mb-4 flex flex-col gap-2">
            <label>درصد تخفیف</label>
            <input type="number" name="percentage" value="{{ old('percentage', $discountCode?->percentage ?? '') }}" class="border p-2 w-full">
            @error('percentage')<div class="text-red-600">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4 flex flex-col gap-2">
            <label>سقف تخفیف</label>
            <input type="number" name="max_discount" value="{{ old('max_discount', $discountCode?->max_discount ?? '') }}" class="border p-2 w-full">
            @error('max_discount')<div class="text-red-600">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="grid grid-cols-2 items-center gap-4">
        <div class="mb-4">
            <label>اعتبار تا</label>
            <input type="date" name="valid_until" value="{{ old('valid_until', $discountCode?->valid_until?->format('Y-m-d') ?? '') }}" class="border p-2 w-full">
            @error('valid_until')<div class="text-red-600">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label><input type="checkbox" name="one_time_use" value="1" {{ old('one_time_use', $discountCode?->one_time_use ?? false) ? 'checked' : '' }}> یکبار مصرف</label>
        </div>
    </div>



    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">{{ $isEdit ? 'ویرایش' : 'ایجاد' }}</button>
</form>

<script>
    document.getElementById('generateCodeBtn').addEventListener('click', function() {
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let code = '';
        for (let i = 0; i < 8; i++) { // طول کد 8 کاراکتر
            code += characters.charAt(Math.floor(Math.random() * characters.length));
        }
        document.getElementById('discountCodeInput').value = code;
    });
</script>
