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
                <a href="{{route('admin.restaurants.index')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">رستوران ها</span>

                </a>
            </li>


        </ul>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.restaurants.reports.index')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">گزارش رستوران ها</span>

                </a>
            </li>



        </ul>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.message.show')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">نمایش پیام برای کاربران</span>

                </a>
            </li>



        </ul>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.support.index')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">شماره های پشتیبانی</span>

                </a>
            </li>



        </ul>

        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.restaurant-discounts.index')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">درخواست تخفیف رستوران</span>

                </a>
            </li>


        </ul>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.feedback.index')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">دیدگاه ها</span>

                </a>
            </li>


        </ul>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.foods.activate')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">غذا های غیر فعال</span>

                </a>
            </li>


        </ul>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.orders.telephone.create')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">ثبت سفارش دستی</span>

                </a>
            </li>


        </ul>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.discount-codes.index')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">کد تخفیف</span>

                </a>
            </li>


        </ul>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.restaurants.balance')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">تراکنش ها</span>

                </a>
            </li>


        </ul>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.users.index')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">کاربران</span>

                </a>
            </li>


        </ul>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.orders.index')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">لیست سفارشات</span>

                </a>
            </li>


        </ul>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.comments.index')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">کامنت ها</span>

                </a>
            </li>


        </ul>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.sliders.index')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">اسلایدر ها</span>

                </a>
            </li>


        </ul>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.restaurant_introductions.index')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600"> رستوران های معرفی شده</span>

                </a>
            </li>


        </ul>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.categories.index')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">گروه ها</span>

                </a>
            </li>


        </ul>
        <ul class="space-y-2 text-sm">
            <li>
                <a href="{{route('admin.categories.show')}}"
                   class="flex items-center gap-3 px-4 py-3 rounded-lg transition
                          hover:bg-blue-50 hover:text-blue-600 font-medium group">
                    <span class="material-icons text-gray-500 group-hover:text-blue-600">دسته بندی ها</span>

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
