@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto bg-white shadow-lg rounded-2xl p-8 mt-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center border-b pb-3">✏️ ویرایش ساعت کاری رستوران: {{ $restaurant->name }}</h2>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.restaurants.service_times.update', $restaurant->id) }}" method="POST">
            @csrf
            @method('PUT')

            <table class="table-auto border-collapse border border-gray-300 w-full">
                <thead>
                <tr>
                    <th class="border px-4 py-2">روز هفته</th>
                    @foreach($mealTypes as $mealKey => $mealLabel)
                        <th class="border px-4 py-2">{{ $mealLabel }} شروع</th>
                        <th class="border px-4 py-2">{{ $mealLabel }} پایان</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach($daysOfWeek as $dayKey => $dayLabel)
                    <tr>
                        <td class="border px-4 py-2">{{ $dayLabel }}</td>
                        @foreach($mealTypes as $mealKey => $mealLabel)
                            @php
                                $time = $restaurant->serviceTimes
                                            ->where('day_of_week', $dayKey)
                                            ->where('meal_type', $mealKey)
                                            ->first();
                            @endphp
                            <td class="border px-2 py-1">
                                <input type="time" name="service_times[{{ $dayKey }}][{{ $mealKey }}][start_time]" value="{{ $time->start_time ?? '' }}" class="w-full border rounded px-2 py-1">
                            </td>
                            <td class="border px-2 py-1">
                                <input type="time" name="service_times[{{ $dayKey }}][{{ $mealKey }}][end_time]" value="{{ $time->end_time ?? '' }}" class="w-full border rounded px-2 py-1">
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>

            <button type="submit" class="mt-6 px-6 py-2 bg-blue-500 text-white font-semibold rounded hover:bg-blue-600">💾 ذخیره</button>
        </form>
    </div>
@endsection
