@extends('layouts.app')

@section('content')
    <div class="w-full px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">اعلان ها</h2>

            <form action="{{ route('admin.notifications.clear') }}" method="POST"
                  onsubmit="return confirm('همه نوتیف‌ها حذف شوند؟');">
                @csrf
                @method('DELETE')

                <button type="submit"
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                    حذف همه
                </button>
            </form>
        </div>


        @foreach ($notifications as $item)
            <div class="bg-white shadow-md border border-gray-200 rounded-lg p-4 mb-3 w-full
                        transition transform duration-200 ease-in-out
                        hover:shadow-lg hover:scale-[1.02] cursor-pointer">

                <p class="text-gray-800 text-sm">
                    {{ $item->text }}
                </p>

                <span class="text-xs text-gray-500 block mt-2">
                    {{ \Morilog\Jalali\Jalalian::fromDateTime($item->created_at)->format('Y/m/d - H:i') }}
                </span>

            </div>
        @endforeach

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>

    </div>
@endsection
