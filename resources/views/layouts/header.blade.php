<header class="bg-white border-b shadow-sm px-6 py-4 flex justify-between items-center sticky top-0 z-50">
    <div class="flex items-center gap-3">
        <button id="sidebarToggle" class="md:hidden text-gray-600 hover:text-blue-600 transition">
            <span class="material-icons text-2xl">menu</span>
        </button>
        <h1 class="text-xl font-bold text-gray-700">پنل مدیریت غذارسان</h1>
    </div>
    <div class="flex items-center gap-6">
        @php
            $Count = \App\Models\RequestDiscount::where('is_seen', 0)->count();
        @endphp

        <div class="relative">
            <a href="{{ route('admin.request-discounts.index') }}" class="relative text-gray-600 hover:text-blue-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
                </svg>

                @if($Count > 0)
                    <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs px-1.5 py-0.5 rounded-full shadow">
                {{ $Count }}
            </span>
                @endif
            </a>
        </div>

    <div class="flex items-center gap-6">
        @php
            $unseenCount = \App\Models\Notification::where('is_seen', 0)->count();
        @endphp

        <div class="relative">
            <a href="{{ route('admin.notifications.index') }}" class="relative text-gray-600 hover:text-blue-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14V11a6 6 0 10-12 0v3c0 .386-.149.735-.395 1.003L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>

                @if($unseenCount > 0)
                    <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs px-1.5 py-0.5 rounded-full shadow">
                {{ $unseenCount }}
            </span>
                @endif
            </a>
        </div>


        @php
            $targetStatus = \App\Models\SystemSetting::where('kay', 'system_status')->value('value');
        @endphp
        <form id="toggleAllForm" action="{{ route('admin.restaurants.toggleAll') }}" method="POST" class="ml-4 inline-flex items-center">
            @csrf
            <label class="inline-flex items-center cursor-pointer">
                <input type="checkbox" id="toggleSwitch" class="sr-only peer"
                    {{ $targetStatus ? 'checked' : '' }}>
                <div class="relative w-9 h-5
                    bg-gray-400
                    peer-focus:outline-none
                    peer-focus:ring-4
                    peer-focus:ring-opacity-50
                    rounded-full peer
                    peer-checked:after:translate-x-full
                    rtl:peer-checked:after:-translate-x-full
                    after:content-['']
                    after:absolute
                    after:top-[2px]
                    after:start-[2px]
                    after:bg-white
                    after:rounded-full
                    after:h-4 after:w-4
                    after:transition-all
                    {{ $targetStatus ? 'peer-checked:bg-green-600 bg-green-400':'peer-checked:bg-red-600 bg-red-400' }}">
                </div>

                <span class="select-none ms-3 text-sm font-medium text-heading">
            {{ $targetStatus ? 'همه ی رستوران ها فعال هستند' : 'همه ی رستوران ها غیر فعال هستند' }}
        </span>
            </label>
            <input type="hidden" name="status" value="{{ $targetStatus }}" id="statusInput">
        </form>

        <script>
            document.getElementById('toggleSwitch').addEventListener('change', function () {
                const form = document.getElementById('toggleAllForm');
                const statusInput = document.getElementById('statusInput');

                // همیشه مقدار مخالف وضعیت فعلی رو بفرست
                const allCurrentlyActive = {{ $targetStatus ? 'true' : 'false' }};
                const newStatus = this.checked ? 1 : 0;


                statusInput.value = newStatus;

                // آپدیت متن
                const label = this.closest('label').querySelector('span');
                label.textContent = this.checked ? 'همه ی رستوران ها غیر فعال هستند' : 'همه ی رستوران ها فعال هستند';


                // آپدیت رنگ
                const track = this.nextElementSibling;
                if (this.checked) {
                    track.classList.replace('bg-green-400', 'bg-red-400');
                    track.classList.replace('peer-checked:bg-green-600', 'peer-checked:bg-red-600');
                } else {
                    track.classList.replace('bg-red-400', 'bg-green-400');
                    track.classList.replace('peer-checked:bg-red-600', 'peer-checked:bg-green-600');
                }

                // ارسال فرم
                form.submit();
            });
        </script>

        <div class="relative group">
            <button
                class="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-full border hover:bg-gray-200 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5.121 17.804A13.937 13.937 0 0112 15c2.19 0 4.256.536 6.121 1.804M12 7a4 4 0 110 8 4 4 0 010-8zM6 21v-2a4 4 0 014-4h4a4 4 0 014 4v2"/>
                </svg>
            </button>

            <div class="absolute right-0 mt-2 w-40 bg-white border rounded-lg shadow-md hidden group-hover:block">

            </div>
        </div>
    </div>
</header>

