@extends('layouts.app')

@section('title', 'مدیریت کاربران')

@section('content')
    <div class="container py-4" dir="rtl">
        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg shadow transition transform hover:scale-105">
ایجاد کاربر        </a>

        <h3 class="text-2xl font-bold mb-4">👥 لیست کاربران</h3>
        <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4 flex flex-wrap gap-3 items-end" dir="rtl">
            {{-- جست‌وجو --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">جست‌وجو (نام یا موبایل)</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="مثلاً: علی یا 0912..."
                       class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            {{-- فیلتر کاربران بلاک شده --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">وضعیت کاربر</label>
                <select name="is_blocked"
                        class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">همه کاربران</option>
                    <option value="0" {{ request('is_blocked') === '0' ? 'selected' : '' }}>فعال</option>
                    <option value="1" {{ request('is_blocked') === '1' ? 'selected' : '' }}>بلاک شده</option>
                </select>
            </div>


            {{-- فیلتر کیف پول --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">فیلتر کیف پول</label>
                <select name="wallet_balance"
                        class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">همه</option>
                    <option value="has_balance" {{ request('wallet_balance') == 'has_balance' ? 'selected' : '' }}>دارای موجودی</option>
                    <option value="zero_balance" {{ request('wallet_balance') == 'zero_balance' ? 'selected' : '' }}>موجودی صفر</option>
                </select>
            </div>

            {{-- فیلتر وفاداری --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">مرتب‌سازی بر اساس</label>
                <select name="sort"
                        class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">جدیدترین</option>
                    <option value="loyal" {{ request('sort') == 'loyal' ? 'selected' : '' }}>وفاداری (تعداد خرید)</option>
                </select>
            </div>

            <div>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    اعمال فیلتر 🔍
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition">
                    پاک کردن
                </a>
            </div>
        </form>

        <div class="bg-white rounded-xl shadow p-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 table-auto">
                <thead class="bg-gray-100">
                <tr>
                    <th class="text-center px-4 py-2  text-sm font-medium text-gray-700">#</th>
                    <th class="text-center px-4 py-2  text-sm font-medium text-gray-700">نام</th>
                    <th class="text-center px-4 py-2  text-sm font-medium text-gray-700">موبایل</th>
                    <th class="text-center px-4 py-2  text-sm font-medium text-gray-700">تاریخ ثبت‌نام</th>
                    <th class="text-center px-4 py-2 text-sm font-medium text-gray-700">موجودی کیف پول</th>
                    <th class="text-center px-4 py-2 text-sm font-medium text-gray-700">تعداد خرید</th>
                    <th class="text-center px-4 py-2 text-sm font-medium text-gray-700">دلیل بلاک شدن</th>

                    <th class="text-center px-4 py-2  text-sm font-medium text-gray-700">عملیات</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                @foreach($users as $user)
                    <tr class="h-12 {{ $user->is_blocked ? 'bg-gray-300 text-gray-600' : '' }}">
                        <td class="text-center px-4 py-2">{{ $user->id }}</td>
                        <td class="text-center px-4 py-2">{{ $user->name }}</td>
                        <td class="text-center px-4 py-2">{{ $user->mobile }}</td>
                        <td class="text-center px-4 py-2">{{ $user->created_at?->format('Y/m/d') }}</td>
                        <td class="text-center px-4 py-2">{{ number_format($user->wallet->balance ?? 0) }} تومان</td>
                        <td class="text-center px-4 py-2">{{ $user->orders_count ?? $user->orders()->count() }}</td>
                        <td class="text-center px-4 py-2">{{ $user->block_reason }}</td>
                        <td class="text-center px-4 py-2">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{route('admin.users.show',$user->id)}}"
                                   class="inline-flex items-center gap-1 text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200
                  px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200">
                                    👁 مشاهده
                                </a>
                                <a href="{{ route('admin.users.edit_user', $user->id) }}"
                                   class="inline-flex items-center gap-1 text-yellow-600 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200
          px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200">
                                    ✏️ ویرایش
                                </a>

                                <form action="{{route('admin.users.delete' , $user->id)}}" method="POST"
                                      onsubmit="return confirm('آیا از حذف این کاربر مطمئن هستید؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 text-red-600 bg-red-50 hover:bg-red-100 border border-red-200
                           px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200">
                                        🗑 حذف
                                    </button>
                                    <!-- دکمه بلاک / آن‌بلاک -->

                                </form>
                                <div class="flex items-center justify-center gap-2">
                                    <!-- دکمه بلاک / آن‌بلاک -->
                                    @if(!$user->is_blocked)
                                        <button class="block-btn px-4 py-2 text-white bg-red-600 rounded-lg text-sm font-medium hover:bg-red-700 transition"
                                                data-user-id="{{ $user->id }}">
                                            بلاک
                                        </button>
                                    @else
                                        <button class="unblock-btn px-4 py-2 text-white bg-green-600 rounded-lg text-sm font-medium hover:bg-green-700 transition"
                                                data-user-id="{{ $user->id }}">
                                            آن‌بلاک
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>

            <div class="mt-4">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
    <div id="block-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg w-96">
            <h3 class="text-lg font-bold mb-4">دلیل بلاک کردن کاربر</h3>
            <input type="text" id="block-reason" class="border p-2 w-full rounded mb-4" placeholder="دلیل را وارد کنید">
            <div class="flex justify-end gap-2">
                <button id="block-cancel" class="px-4 py-2 bg-gray-300 rounded">لغو</button>
                <button id="block-confirm" class="px-4 py-2 bg-red-600 text-white rounded">تایید</button>
            </div>
        </div>
    </div>
    <script>
        let selectedUserId = null;

        // استفاده از Event Delegation – کار می‌کنه حتی بعد از صفحه‌بندی
        document.addEventListener('click', function(e) {
            // دکمه بلاک
            if (e.target.matches('.block-btn') || e.target.closest('.block-btn')) {
                const btn = e.target.matches('.block-btn') ? e.target : e.target.closest('.block-btn');
                selectedUserId = btn.dataset.userId;
                document.getElementById('block-modal').classList.remove('hidden');
                document.getElementById('block-reason').focus();
            }

            // دکمه آن‌بلاک (اگر بعداً بخوای مدال برای آن‌بلاک هم داشته باشی)
            if (e.target.matches('.unblock-btn') || e.target.closest('.unblock-btn')) {
                selectedUserId = e.target.closest('.unblock-btn').dataset.userId;
                if (confirm('آیا از آن‌بلاک کردن این کاربر مطمئن هستید؟')) {
                    unblockUser(selectedUserId);
                }
            }
        });

        // لغو مدال
        document.getElementById('block-cancel').addEventListener('click', function () {
            document.getElementById('block-modal').classList.add('hidden');
            document.getElementById('block-reason').value = '';
            selectedUserId = null;
        });

        // بستن مدال با کلیک بیرون
        document.getElementById('block-modal').addEventListener('click', function (e) {
            if (e.target === this) {
                this.classList.add('hidden');
                document.getElementById('block-reason').value = '';
                selectedUserId = null;
            }
        });

        // تایید بلاک
        document.getElementById('block-confirm').addEventListener('click', function () {
            const reason = document.getElementById('block-reason').value.trim();
            if (!reason) {
                alert('لطفاً دلیل بلاک کردن را وارد کنید');
                return;
            }

            fetch(`/admin/users/${selectedUserId}/block`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ reason: reason })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // مدال بسته بشه
                        document.getElementById('block-modal').classList.add('hidden');
                        document.getElementById('block-reason').value = '';

                        // رفرش صفحه (یا فقط سطر رو آپدیت کن)
                        location.reload();
                    } else {
                        alert(data.message || 'خطا در بلاک کردن کاربر');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('خطا در ارتباط با سرور');
                });
        });

        // تابع آن‌بلاک (اختیاری)
        function unblockUser(userId) {
            if (!confirm('کاربر آن‌بلاک شود؟')) return;

            fetch(`/admin/users/${userId}/unblock`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
        }
    </script>
@endsection
