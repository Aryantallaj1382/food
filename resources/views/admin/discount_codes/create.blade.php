@extends('layouts.app')
@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">ایجاد کد تخفیف</h1>
        @include('admin.discount_codes.form', ['discountCode' => null])
    </div>
@endsection
