{{-- resources/views/admin/foods/inactive.blade.php --}}
@extends('layouts.app')
@section('title', 'غذاهای غیرفعال')

@section('content')
    <div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">

            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">غذاهای غیرفعال</h1>
                <p class="mt-2 text-gray-600">غذاهایی که حداقل یکی از زیر مجموعه غیرفعال است</p>
                <div class="mt-4 inline-flex items-center gap-2 bg-red-100 text-red-700 px-4 py-2 rounded-full text-sm font-medium">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/></svg>
                    <span>{{ $foods->total() }} غذا</span>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-xl flex items-center gap-3 shadow-sm">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-900 text-white">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-medium uppercase">#</th>
                            <th class="px-6 py-4 text-right text-xs font-medium uppercase">رستوران</th>
                            <th class="px-6 py-4 text-right text-xs font-medium uppercase">غذا</th>
                            <th class="px-6 py-4 text-right text-xs font-medium uppercase">زیر مجموعه ها</th>
                            <th class="px-6 py-4 text-center text-xs font-medium uppercase">عملیات</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($foods as $food)
                            <tr class="hover:bg-gray-50" id="food-row-{{ $food->id }}">
                                <td class="px-6 py-5 text-center text-sm font-medium text-gray-600">
                                    {{ $loop->iteration + ($foods->currentPage()-1)*$foods->perPage() }}
                                </td>

                                <td class="px-6 py-5">
                                    <div class="font-medium">{{ $food->restaurant?->name ?? '—' }}</div>
                                    <div class="text-xs text-gray-500">ID: {{ $food->restaurant_id }}</div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="font-bold text-gray-900">{{ $food->name }}</div>
                                    <div class="text-xs text-gray-500">ID: {{ $food->id }}</div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($food->inactiveOptions  as $option)
                                            <div class="flex items-center gap-2 bg-gray-100 rounded-lg px-3 py-2">
                                                <span class="text-sm font-medium">{{ $option->name }}</span>
                                                <span class="text-xs text-gray-500">
                                                    ({{ number_format($option->price) }}₺)
                                                </span>

                                                {{-- دکمه تکی فعال‌سازی --}}
                                                <button
                                                    onclick="toggleOption({{ $option->id }}, {{ $food->id }})"
                                                    class="ml-2 px-3 py-1 rounded text-xs font-medium transition
                                                        {{ $option->is_available ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700 hover:bg-red-200' }}"
                                                    id="option-btn-{{ $option->id }}">
                                                    {{ $option->is_available ? 'فعال' : 'غیرفعال' }}
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <button onclick="activateAll({{ $food->id }})"
                                            class="text-xs px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow hover:shadow-md transition">
                                        فعال کردن همه
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-16 text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-16 h-16 text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p class="font-medium">همه غذاها فعال هستند!</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-gray-50 px-6 py-4 border-t">
                    {{ $foods->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- اسکریپت Ajax --}}
    <script>
        function toggleOption(optionId, foodId) {
            axios.patch(`/admin/food-options/${optionId}/toggle`)
                .then(response => {
                    const btn = document.getElementById(`option-btn-${optionId}`);
                    if (response.data.is_active) {
                        btn.classList.remove('bg-red-100', 'text-red-700');
                        btn.classList.add('bg-emerald-100', 'text-emerald-700');
                        btn.textContent = 'فعال';
                    } else {
                        btn.classList.remove('bg-emerald-100', 'text-emerald-700');
                        btn.classList.add('bg-red-100', 'text-red-700');
                        btn.textContent = 'غیرفعال';
                    }

                    // اگر همه آپشن‌ها فعال شدن → ردیف غذا حذف بشه
                    if (response.data.all_active) {
                        document.getElementById(`food-row-${foodId}`).remove();
                        toastr.success('همه آپشن‌ها فعال شدند. غذا از لیست حذف شد.');
                    }
                })
                .catch(err => toastr.error('خطا در بروزرسانی'));
        }

        function activateAll(foodId) {
            if (!confirm('همه آپشن‌های این غذا فعال شوند؟')) return;

            axios.patch(`/admin/foods11/activate-all-options/${foodId}`)
                .then(() => {
                    document.getElementById(`food-row-${foodId}`)?.remove();

                    Swal.fire({
                        icon: 'success',
                        title: 'انجام شد!',
                        text: 'همه آپشن‌ها با موفقیت فعال شدند.',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                })
                .catch(() => {
                    location.reload();
                });
        }
    </script>
@endsection
