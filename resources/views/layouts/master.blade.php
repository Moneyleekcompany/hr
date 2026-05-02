@php
    $locale = \Illuminate\Support\Facades\App::getLocale();
@endphp
<!DOCTYPE html>
<html lang="{{ $locale ?? 'en' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Digital HR Complete HR Attendance System">
    <meta name="author" content="Digital HR">
    <meta name="keywords" content="Digital HR">

    <title>@yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Modern Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    @include('admin.section.head_links')
    @yield('styles')
    <style>
        /* تطبيق خط Cairo على كافة العناصر */
        * {
            font-family: 'Cairo', sans-serif !important;
        }
        
        /* تأثير ظهور الكروت (Fade In Up Animation) */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* تحسينات عصرية للوحة التحكم (Modern UI) */
        @if(\App\Helpers\AppHelper::getTheme() == 'dark')
        body {
            background-color: #0f172a;
            color: #f1f5f9 !important;
        }
        .card {
            background: #1e293b !important;
            border: 1px solid rgba(51, 65, 85, 0.8) !important;
            border-radius: 20px !important;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.2) !important;
            transition: all 0.3s ease-in-out;
            animation: fadeInUp 0.6s ease-out forwards;
        }
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 25px -4px rgba(0, 0, 0, 0.3) !important;
            border-color: rgba(138, 12, 81, 0.4) !important; /* إطار خفيف عند التحويم */
        }
        .card-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
        }
        @else
        body {
            background-color: #f8fafc;
            color: #334155;
        }
        .card {
            background: #ffffff !important;
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
            border-radius: 20px !important;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03) !important; /* ظل ناعم جداً ومنتشر */
            transition: all 0.3s ease-in-out;
            animation: fadeInUp 0.6s ease-out forwards;
        }
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 25px -4px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01) !important;
            border-color: rgba(138, 12, 81, 0.3) !important; /* إطار خفيف عند التحويم */
        }
        .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.03) !important;
        }
        @endif
        .btn {
            border-radius: 10px !important;
            font-weight: 600 !important;
            padding: 0.5rem 1.25rem !important;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .page-content {
            padding-top: 1.5rem;
        }
        .card-body {
            padding: 1.5rem !important; /* مساحة داخلية مريحة للمحتوى */
        }
        .card-header {
            background-color: transparent !important;
            padding: 1.25rem 1.5rem !important;
        }

        /* تنسيقات أوزان الخطوط العصرية (Typography Hierarchy) */
        h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {
            font-weight: 700 !important;
        }
        .card-title, .card-header {
            font-weight: 700 !important;
            font-size: 1.1rem;
        }
        table th {
            font-weight: 600 !important;
            font-size: 0.85rem !important;
        }
        .sidebar-menu .nav-link, .navbar .nav-link {
            font-weight: 600 !important;
        }
        label {
            font-weight: 600 !important;
            margin-bottom: 0.4rem;
        }
        .text-muted, small, .dropdown-item {
            font-weight: 500 !important;
        }
        p {
            font-weight: 500;
        }
        
        /* ألوان النصوص بناءً على الوضع (فاتح/داكن) */
        @if(\App\Helpers\AppHelper::getTheme() == 'dark')
        h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6, .card-title, .card-header, .text-dark { color: #f8fafc !important; }
        table th, label { color: #e2e8f0 !important; }
        .text-muted, p, .text-body { color: #cbd5e1 !important; }
        .dropdown-item { color: #f8fafc !important; }
        .dropdown-item:hover { background-color: #334155 !important; color: #ffffff !important; }
        .border-bottom { border-color: #334155 !important; }
        @else
        h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 { color: #1e293b !important; }
        .card-title, .card-header { color: #0f172a; }
        table th { color: #475569 !important; }
        label { color: #334155; }
        @endif

        /* 1. تخصيص شريط التمرير (Custom Scrollbar) */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        @if(\App\Helpers\AppHelper::getTheme() == 'dark')
        ::-webkit-scrollbar-thumb { background: #334155; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
        @endif

        /* --- تحسينات القائمة الجانبية (Sidebar) --- */
        .sidebar {
            border-right: none !important;
            box-shadow: 4px 0 25px rgba(0, 0, 0, 0.03) !important;
        }
        
        /* 2. تحسين القائمة الجانبية (Modern Sidebar Links) */
        .sidebar .nav-link {
            border-radius: 12px !important;
            margin: 0.25rem 1rem !important;
            padding: 0.65rem 1rem !important;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(138, 12, 81, 0.08) !important;
            color: #8a0c51 !important;
        }
        .sidebar .nav-item.active .nav-link {
            background-color: #8a0c51 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(138, 12, 81, 0.3) !important;
        }
        
        /* --- تحسينات الشريط العلوي (Navbar) --- */
        .navbar {
            background: @if(\App\Helpers\AppHelper::getTheme() == 'dark') rgba(30, 41, 59, 0.85) @else rgba(255, 255, 255, 0.85) @endif !important;
            backdrop-filter: blur(12px) !important;
            -webkit-backdrop-filter: blur(12px) !important;
            border-bottom: none !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02) !important;
            margin: 15px 25px;
            border-radius: 15px;
            transition: all 0.3s ease;
            width: calc(100% - 330px) !important; /* 280px sidebar + 50px margins */
        }
        @media (max-width: 991px) {
            .navbar {
                width: calc(100% - 50px) !important;
                left: 25px !important;
                margin: 15px auto;
            }
        }

        /* الجداول العائمة (Floating Tables) - عام لكل النظام */
        .table.custom-table { border-collapse: separate; border-spacing: 0 8px; }
        .table.custom-table thead th { border: none; background: transparent; color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; padding-bottom: 0.5rem; }
        .table.custom-table tbody tr { background: @if(\App\Helpers\AppHelper::getTheme() == 'dark') #1e293b @else #ffffff @endif; box-shadow: 0 2px 10px rgba(0,0,0,0.02); transition: all 0.2s ease-in-out; border-radius: 10px; }
        .table.custom-table tbody tr:hover { transform: translateY(-2px) scale(1.005); box-shadow: 0 10px 25px rgba(138, 12, 81, 0.1); z-index: 2; position: relative; }
        .table.custom-table tbody td { border: none; padding: 15px !important; vertical-align: middle; }
        .table.custom-table tbody td:first-child { border-top-right-radius: 10px; border-bottom-right-radius: 10px; }
        .table.custom-table tbody td:last-child { border-top-left-radius: 10px; border-bottom-left-radius: 10px; }

        /* شريط البحث الذكي (Omnibar Ctrl+K) */
        .card-admin-search {
            position: fixed !important;
            top: 15% !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            width: 90% !important;
            max-width: 600px !important;
            background: @if(\App\Helpers\AppHelper::getTheme() == 'dark') rgba(30, 41, 59, 0.95) @else rgba(255, 255, 255, 0.95) @endif !important;
            backdrop-filter: blur(15px) !important;
            border-radius: 15px !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
            z-index: 9999 !important;
            border: 1px solid rgba(0, 0, 0, 0.1) !important;
        }
        .card-admin-search .list-group-item { border: none !important; background: transparent !important; padding: 10px 20px !important; font-size: 1.1rem !important; transition: all 0.2s; border-radius: 8px !important; margin: 2px 10px !important; }
        .card-admin-search .list-group-item.highlight, .card-admin-search .list-group-item:hover { background: rgba(138, 12, 81, 0.1) !important; color: #8a0c51 !important; padding-left: 25px !important; }
        
        /* تظليل خلفية الشاشة عند فتح البحث */
        body.search-active::after { content: ''; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.4); backdrop-filter: blur(4px); z-index: 9998; }

        /* 3. تحسين شكل الحالات (Soft Badges) */
        .badge { padding: 0.45em 0.85em; border-radius: 8px; font-weight: 700; letter-spacing: 0.3px; }
        .badge.bg-success { background-color: rgba(16, 185, 129, 0.15) !important; color: #10b981 !important; }
        .badge.bg-danger { background-color: rgba(239, 68, 68, 0.15) !important; color: #ef4444 !important; }
        .badge.bg-warning { background-color: rgba(245, 158, 11, 0.15) !important; color: #f59e0b !important; }
        .badge.bg-info { background-color: rgba(14, 165, 233, 0.15) !important; color: #0ea5e9 !important; }
        @if(\App\Helpers\AppHelper::getTheme() == 'dark')
        .badge.bg-success { color: #34d399 !important; }
        .badge.bg-danger { color: #f87171 !important; }
        .badge.bg-warning { color: #fbbf24 !important; }
        .badge.bg-info { color: #38bdf8 !important; }
        @endif

        /* 4. تحسين مسافات وفواصل الجداول */
        .table th, .table td {
            vertical-align: middle;
            padding: 1rem !important;
            border-color: @if(\App\Helpers\AppHelper::getTheme() == 'dark') rgba(255,255,255,0.05) @else rgba(0,0,0,0.04) @endif !important;
        }

        /* 5. تحسين حقول الإدخال (Modern Inputs) */
        .form-control, .form-select {
            border-radius: 12px !important;
            padding: 0.75rem 1.25rem !important;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.02) !important;
            border: 1px solid @if(\App\Helpers\AppHelper::getTheme() == 'dark') #334155 @else #e2e8f0 @endif !important;
            background-color: @if(\App\Helpers\AppHelper::getTheme() == 'dark') #1e293b @else #f8fafc @endif !important;
            color: @if(\App\Helpers\AppHelper::getTheme() == 'dark') #f1f5f9 @else #334155 @endif !important;
        }
        .form-control:focus, .form-select:focus {
            background-color: @if(\App\Helpers\AppHelper::getTheme() == 'dark') #0f172a @else #ffffff @endif !important;
            border-color: #8a0c51 !important;
            box-shadow: 0 0 0 4px rgba(138, 12, 81, 0.15), inset 0 1px 2px rgba(0, 0, 0, 0.02) !important;
        }
        .form-check-input:checked {
            background-color: #8a0c51 !important;
            border-color: #8a0c51 !important;
        }
        .form-check-input:focus {
            box-shadow: 0 0 0 4px rgba(138, 12, 81, 0.15) !important;
        }

        /* تحويل اللون الأساسي (البينك) إلى الموف في جميع أنحاء النظام */
        .text-primary {
            color: #8a0c51 !important;
        }
        .bg-primary {
            background-color: #8a0c51 !important;
        }
        .btn-primary {
            background-color: #8a0c51 !important;
            border-color: #8a0c51 !important;
            color: #ffffff !important;
        }
        .btn-primary:hover {
            background-color: #8a0c51 !important; /* درجة أغمق قليلاً عند تمرير الماوس */
            border-color: #8a0c51 !important;
        }
        svg.feather { stroke: currentColor; } /* لضمان تطابق ألوان بعض الأيقونات */
    </style>
</head>

<body>
<div id="preloader" >
    @include('admin.section.preloader')
</div>

<div class="main-wrapper">
    @include('admin.section.sidebar')
    <div class="page-wrapper">
        @include('admin.section.nav')

        <div class="page-content">
            @include('admin.section.page_header')
            @yield('main-content')
        </div>

        <!-- partial -->
        @include('admin.section.footer')
    </div>
</div>

@include('admin.section.body_links')

@include('layouts.nav_notification_scripts')
@include('layouts.nav_search_scripts')
@include('layouts.theme_scripts')

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

@yield('scripts')
<script type="text/javascript">
    let url = "{{ route('admin.language.change') }}";

    $(".changeLang").click(function() {
        let lang = $(this).data('lang');
        window.location.href = url + "?lang=" + lang;
    });
</script>
<script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>

</body>

</html>
