@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto mt-10">
        <div class="bg-white shadow rounded-xl p-6 space-y-4">

            <h1 class="text-2xl font-bold text-gray-800">
                ✍️ پیام ادمین به کاربران
            </h1>

            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('admin.message.update') }}" method="POST">
                @csrf
                @method('PUT')

                <textarea
                    name="message"
                    rows="6"
                    class="w-full border rounded-lg p-4 focus:outline-none focus:ring"
                    placeholder="پیام مورد نظر را وارد کنید..."
                >{{ old('message', $message) }}</textarea>

                <div class="flex justify-end mt-4">
                    <button
                        type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition"
                    >
                        ذخیره پیام
                    </button>
                </div>
            </form>

        </div>
    </div>
@endsection
