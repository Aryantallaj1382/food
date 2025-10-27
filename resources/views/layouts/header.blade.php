<header class="bg-white border-b shadow-sm px-6 py-4 flex justify-between items-center sticky top-0 z-50">
    <div class="flex items-center gap-3">
        <button id="sidebarToggle" class="md:hidden text-gray-600 hover:text-blue-600 transition">
            <span class="material-icons text-2xl">menu</span>
        </button>
        <h1 class="text-xl font-bold text-gray-700">پنل مدیریت غذارسان</h1>
    </div>

    <div class="flex items-center gap-6">
        <div class="relative">
            <a href="#" class="relative text-gray-600 hover:text-blue-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14V11a6 6 0 10-12 0v3c0 .386-.149.735-.395 1.003L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
{{--                @if (Ticket::where('status', 'pending')->count() > 0)--}}
{{--                    <span--}}
{{--                        class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1">{{ Ticket::where('status', 'pending')->count() }}</span>--}}
{{--                @endif--}}
            </a>
        </div>



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

