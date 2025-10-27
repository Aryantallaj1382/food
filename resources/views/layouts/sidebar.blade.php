<aside class="w-64 bg-white shadow-xl border-r min-h-screen flex flex-col">
    {{-- پروفایل / برند --}}
    <div class="p-6 border-b flex items-center gap-3">
        <img src="https://ui-avatars.com/api/?name=Admin+User&background=0D8ABC&color=fff&size=50"
             alt="Admin" class="w-12 h-12 rounded-full border">
        <div>
            <h2 class="font-bold text-gray-800 text-sm">ادمین</h2>
            <span class="text-xs text-gray-500">مدیر کل سیستم</span>
        </div>
    </div>

    {{-- منو --}}
    <nav class="flex-1 p-4">
        <ul class="space-y-2 text-sm">
            <li>
                <a href="                  class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">داشبورد</span>

                </a>
            </li>


        </ul>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="#"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">اساتید</span>

                </a>
            </li>


        </ul>

    </nav>

    {{-- دکمه خروج --}}
    <div class="p-4 border-t">
        <form action="#" method="POST">
            @csrf
            <button
                class="w-full flex items-center justify-center gap-2 bg-red-500 hover:bg-red-600
                       text-white py-2 rounded-lg transition">
                <span class="material-icons text-sm">logout</span>
                خروج
            </button>
        </form>
    </div>
</aside>
