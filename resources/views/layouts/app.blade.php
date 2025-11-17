<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'پنل مدیریت')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    @endpush

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @endpush
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css">
    <!-- Tom-Select CSS & JS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.default.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    <link rel="stylesheet" href="https://majidh1.github.io/JalaliDatePicker/assets/jalalidatepicker.min.css">
    <script src="https://majidh1.github.io/JalaliDatePicker/assets/jalalidatepicker.min.js"></script>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        customGray: '#E1E5EB',
                        customblue: '#0073E6',
                        colors_text_main: '#2E384D',
                        colors_text_help: '#68768A',
                        colors_error_600_base: '#DC3545',
                        primery_100: '#D6F3FF',
                        colors_success_600_base: '#28A745',
                        secondary_600main: '#FF8A00',
                    },
                    fontFamily: {
                        'iransans': ['IranianSans', 'system-ui']
                    }
                }
            }
        }
    </script>

    <style>
        /* وقتی موبایل یا تبلت است، محتوا مخفی می‌شود */
        body.mobile #mainContent {
            display: none;
        }
        @import "https://majidh1.github.io/JalaliDatePicker/assets/demo.css";
    </style>
</head>

<body class="bg-gray-100 text-gray-800 font-sans">

{{-- ✅ پیغام موبایل --}}
<div id="mobileOverlay" class="fixed inset-0 bg-white z-50 flex flex-col items-center justify-center p-6 text-center hidden">
    <h1 class="text-2xl font-bold mb-2 text-gray-800">⚠️ وب‌سایت فقط روی دسکتاپ قابل استفاده است</h1>
    <p class="text-gray-600">لطفاً با یک دستگاه دسکتاپ یا لپ‌تاپ وارد شوید.</p>
</div>

{{-- هدر --}}
@include('layouts.header')

<div class="flex">
    {{-- سایدبار --}}
    @include('layouts.sidebar')

    {{-- محتوای اصلی --}}
    <main id="mainContent" class="flex-1 p-6">
        @yield('content')
    </main>
</div>

{{-- فوتر --}}
@include('layouts.footer')

{{-- اسکریپت‌ها --}}
@vite('resources/js/app.js')
@stack('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

{{-- ✅ اسکریپت تشخیص موبایل و تبلت --}}
<script>
    function isMobileOrTablet() {
        return window.innerWidth < 1024; // زیر 1024 پیکسل موبایل یا تبلت
    }

    function toggleMobileOverlay() {
        const overlay = document.getElementById('mobileOverlay');
        if (isMobileOrTablet()) {
            overlay.classList.remove('hidden'); // نمایش پیغام
            document.body.classList.add('mobile'); // مخفی کردن محتوای اصلی
        } else {
            overlay.classList.add('hidden');    // مخفی کردن پیغام
            document.body.classList.remove('mobile'); // نمایش محتوا
        }
    }

    window.addEventListener('load', toggleMobileOverlay);
    window.addEventListener('resize', toggleMobileOverlay);
</script>

</body>
</html>
