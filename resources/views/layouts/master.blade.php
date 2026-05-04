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
    
    <!-- ملف تنسيقات مساحة العمل الذكية (Smart Workspace) -->
    <link rel="stylesheet" href="{{ asset('css/smart-workspace.css') }}?v={{ time() }}">
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

        /* المتغيرات العامة للألوان المتدرجة (Gradients) */
        :root {
            --primary-gradient: linear-gradient(135deg, #8a0c51 0%, #d81b85 100%);
        }

        /* ==============================================================
           1. التنسيقات الهيكلية المشتركة (للفاتح والداكن معاً نفس الشكل)
           ============================================================== */
        .card {
            border-radius: 20px !important;
            transition: all 0.3s ease-in-out !important;
            animation: fadeInUp 0.6s ease-out forwards;
            border: 1px solid rgba(138, 12, 81, 0.08) !important;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.04) !important;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px -5px rgba(138, 12, 81, 0.15) !important;
            border-color: rgba(138, 12, 81, 0.4) !important;
        }
        .card-body { padding: 1.5rem !important; }
        .card-header {
            background-color: transparent !important;
            padding: 1.25rem 1.5rem !important;
            border-bottom: 1px solid rgba(138, 12, 81, 0.05) !important;
        }
        .btn {
            border-radius: 10px !important;
            font-weight: 600 !important;
            padding: 0.5rem 1.25rem !important;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .page-content { padding-top: 1.5rem; }

        /* تنسيقات الخطوط (Typography) */
        h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6, .card-title, .card-header { font-weight: 700 !important; }
        table th { font-weight: 600 !important; font-size: 0.85rem !important; }
        .sidebar-menu .nav-link, .navbar .nav-link { font-weight: 600 !important; }
        label { font-weight: 600 !important; margin-bottom: 0.4rem; }
        .text-muted, small, .dropdown-item, p { font-weight: 500 !important; }
        
        /* شريط التمرير المشترك (Scrollbar) */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* القائمة الجانبية (Sidebar) والشريط العلوي (Navbar) */
        .sidebar {
            border-right: none !important;
            margin: 15px !important;
            border-radius: 20px !important;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04) !important;
            height: calc(100vh - 30px) !important;
        }
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
            background: var(--primary-gradient) !important;
            color: #ffffff !important;
            border: none !important;
            box-shadow: 0 6px 15px rgba(138, 12, 81, 0.25) !important;
        }
        
        .navbar {
            backdrop-filter: blur(12px) !important;
            -webkit-backdrop-filter: blur(12px) !important;
            border-bottom: none !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03) !important;
            margin: 15px 25px;
            border-radius: 15px;
            transition: all 0.3s ease;
            width: calc(100% - 330px) !important;
        }
        @media (max-width: 991px) {
            .navbar {
                width: calc(100% - 50px) !important;
                left: 25px !important;
                margin: 15px auto;
            }
            .sidebar { margin: 0 !important; border-radius: 0 !important; height: 100vh !important; }
        }

        /* الجداول العائمة (Floating Tables) المشتركة */
        .table.custom-table { border-collapse: separate; border-spacing: 0 8px; }
        .table.custom-table thead th { border: none; background: transparent; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; padding-bottom: 0.5rem; }
        .table.custom-table tbody tr { box-shadow: 0 2px 10px rgba(0,0,0,0.02); transition: all 0.2s ease-in-out; border-radius: 10px; }
        .table.custom-table tbody tr:hover { transform: translateY(-2px) scale(1.005); box-shadow: 0 10px 25px rgba(138, 12, 81, 0.1); z-index: 2; position: relative; }
        .table.custom-table tbody td { border: none; padding: 15px !important; vertical-align: middle; }
        .table.custom-table tbody td:first-child { border-top-right-radius: 10px; border-bottom-right-radius: 10px; }
        .table.custom-table tbody td:last-child { border-top-left-radius: 10px; border-bottom-left-radius: 10px; }
        .table th, .table td { vertical-align: middle; padding: 1rem !important; }

        /* شريط البحث الذكي (Omnibar) */
        .card-admin-search {
            position: fixed !important;
            top: 15% !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            width: 90% !important;
            max-width: 600px !important;
            backdrop-filter: blur(15px) !important;
            border-radius: 15px !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
            z-index: 9999 !important;
        }
        .card-admin-search .list-group-item { border: none !important; background: transparent !important; padding: 10px 20px !important; font-size: 1.1rem !important; transition: all 0.2s; border-radius: 8px !important; margin: 2px 10px !important; }
        .card-admin-search .list-group-item.highlight, .card-admin-search .list-group-item:hover { background: rgba(138, 12, 81, 0.1) !important; color: #8a0c51 !important; padding-left: 25px !important; }
        
        /* تظليل خلفية الشاشة عند فتح البحث */
        body.search-active::after { content: ''; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.4); backdrop-filter: blur(4px); z-index: 9998; }

        /* تحسينات الحالات (Badges) */
        .badge { padding: 0.45em 0.85em; border-radius: 8px; font-weight: 700; letter-spacing: 0.3px; }

        /* حقول الإدخال (Inputs) */
        .form-control, .form-select {
            border-radius: 12px !important;
            padding: 0.75rem 1.25rem !important;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.02) !important;
        }
        .form-control:focus, .form-select:focus {
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

        /* اللون الأساسي (الموف) يطبق للجميع */
        .text-primary {
            color: #8a0c51 !important;
        }
        .bg-primary {
            background: var(--primary-gradient) !important;
        }
        .btn-primary {
            background: var(--primary-gradient) !important;
            border: none !important;
            color: #ffffff !important;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #6d0a40 0%, #b3166d 100%) !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(138, 12, 81, 0.25) !important;
        }
        svg.feather { stroke: currentColor; }

        /* ==============================================================
           2. ألوان الخلفيات والنصوص فقط حسب الوضع (فاتح / داكن)
           ============================================================== */
        @if(\App\Helpers\AppHelper::getTheme() == 'dark')
            body, .page-wrapper { background-color: #0f172a !important; color: #f1f5f9 !important; }
            .card, .sidebar, .navbar { background-color: #1e293b !important; border-color: rgba(255,255,255,0.05) !important; }
            h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6, .card-title, .card-header, .text-dark { color: #f8fafc !important; }
            table th, label { color: #e2e8f0 !important; }
            .text-muted, p, .text-body { color: #cbd5e1 !important; }
            .dropdown-item { color: #f8fafc !important; }
            .dropdown-item:hover { background-color: #334155 !important; color: #ffffff !important; }
            .border-bottom, .table th, .table td { border-color: rgba(255,255,255,0.05) !important; }
            ::-webkit-scrollbar-thumb { background: #334155; }
            ::-webkit-scrollbar-thumb:hover { background: #475569; }
            .table.custom-table thead th { color: #94a3b8; }
            .table.custom-table tbody tr { background: #1e293b !important; }
            .card-admin-search { background: rgba(30, 41, 59, 0.95) !important; border: 1px solid rgba(255, 255, 255, 0.1) !important; }
            .badge.bg-success { background-color: rgba(16, 185, 129, 0.15) !important; color: #34d399 !important; }
            .badge.bg-danger { background-color: rgba(239, 68, 68, 0.15) !important; color: #f87171 !important; }
            .badge.bg-warning { background-color: rgba(245, 158, 11, 0.15) !important; color: #fbbf24 !important; }
            .badge.bg-info { background-color: rgba(14, 165, 233, 0.15) !important; color: #38bdf8 !important; }
            .form-control, .form-select { border-color: #334155 !important; background-color: #1e293b !important; color: #f1f5f9 !important; }
            .form-control:focus, .form-select:focus { background-color: #0f172a !important; }
        @else
            body, .page-wrapper { background-color: #f4f7fe !important; color: #334155 !important; }
            .card, .sidebar, .navbar { background-color: #ffffff !important; border-color: rgba(138, 12, 81, 0.08) !important; }
            h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6, .text-dark { color: #1e293b !important; }
            .card-title, .card-header { color: #0f172a !important; }
            table th { color: #475569 !important; }
            label { color: #334155 !important; }
            .border-bottom, .table th, .table td { border-color: rgba(0,0,0,0.04) !important; }
            ::-webkit-scrollbar-thumb { background: #cbd5e1; }
            ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
            .table.custom-table thead th { color: #64748b; }
            .table.custom-table tbody tr { background: #ffffff !important; }
            .card-admin-search { background: rgba(255, 255, 255, 0.95) !important; border: 1px solid rgba(0, 0, 0, 0.1) !important; }
            .badge.bg-success { background-color: rgba(16, 185, 129, 0.15) !important; color: #10b981 !important; }
            .badge.bg-danger { background-color: rgba(239, 68, 68, 0.15) !important; color: #ef4444 !important; }
            .badge.bg-warning { background-color: rgba(245, 158, 11, 0.15) !important; color: #f59e0b !important; }
            .badge.bg-info { background-color: rgba(14, 165, 233, 0.15) !important; color: #0ea5e9 !important; }
            .form-control, .form-select { border-color: #e2e8f0 !important; background-color: #f8fafc !important; color: #334155 !important; }
            .form-control:focus, .form-select:focus { background-color: #ffffff !important; }
        @endif
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

<!-- مودال الكاميرا والبصمة اللحظية (Quick Punch Modal) -->
<div class="modal fade" id="quickPunchModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" style="color: var(--primary-color);">تسجيل الحضور اللحظي</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center pt-2">
        <p class="text-muted small mb-3">يرجى السماح بصلاحيات الكاميرا والموقع لتسجيل بصمتك بنجاح.</p>
        
        <!-- إطار الكاميرا الدائري -->
        <div class="position-relative mx-auto bg-light d-flex align-items-center justify-content-center" style="width: 240px; height: 240px; border-radius: 50%; overflow: hidden; border: 4px solid var(--primary-color); box-shadow: 0 10px 20px rgba(138, 12, 81, 0.15);">
            <video id="quickCameraVideo" width="100%" height="100%" autoplay playsinline style="object-fit: cover; transform: scaleX(-1);"></video>
            <i class="fa fa-camera text-muted" id="quickCameraIcon" style="position: absolute; font-size: 3rem; z-index: 0;"></i>
        </div>
        <canvas id="quickCameraCanvas" style="display:none;"></canvas>
        
        <!-- حالة الموقع الجغرافي -->
        <div id="quickPunchLocationStatus" class="mt-4 text-warning fw-bold" style="font-size: 14px;">
            <i class="fa fa-spinner fa-spin me-1"></i> جاري تحديد موقعك الجغرافي...
        </div>
      </div>
      <div class="modal-footer border-0 d-flex justify-content-center pb-4 pt-0">
        <button type="button" class="btn btn-success px-4 py-2 me-2 shadow-sm" id="quickBtnCheckIn" disabled style="border-radius: 12px; font-weight: bold; background: #10B981; border: none;">
            <i class="fa fa-sign-in-alt me-2"></i> حضور
        </button>
        <button type="button" class="btn btn-danger px-4 py-2 shadow-sm" id="quickBtnCheckOut" disabled style="border-radius: 12px; font-weight: bold; background: #EF4444; border: none;">
            <i class="fa fa-sign-out-alt me-2"></i> انصراف
        </button>
      </div>
    </div>
  </div>
</div>

<!-- القائمة السفلية الذكية للموبايل -->
<x-bottom-nav />

<!-- السكريبت السحري لتحويل الواجهة بالكامل (Global UI Transformer) -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. تحويل جميع الجداول إلى تصميم عصري بدون حواف
        const tables = document.querySelectorAll('.table');
        tables.forEach(table => {
            table.classList.add('custom-table', 'border-0');
            table.classList.remove('table-bordered', 'table-striped');
            table.style.backgroundColor = 'transparent';
            if(table.parentElement && table.parentElement.classList.contains('table-responsive')){
                table.parentElement.style.border = 'none';
            }
        });

        // 2. تحويل النصوص العادية للحالات إلى شارات (Badges) ملونة ذكية
        const statusWords = {
            'success': ['active', 'approved', 'paid', 'present', 'مقبول', 'مكتمل', 'نشط', 'حاضر'],
            'danger': ['inactive', 'rejected', 'unpaid', 'absent', 'مرفوض', 'ملغى', 'غير نشط', 'غائب'],
            'warning': ['pending', 'on_hold', 'late', 'قيد الانتظار', 'متأخر', 'معلق']
        };

        const tds = document.querySelectorAll('td');
        tds.forEach(td => {
            const text = td.innerText.trim().toLowerCase();
            if (td.children.length === 0 && text.length > 1 && text.length < 20) {
                if(statusWords.success.includes(text)) {
                    td.innerHTML = `<span class="badge border-0 px-3 py-2 shadow-sm" style="background-color: #10B981!important; color: #fff; font-size:13px;">${td.innerText}</span>`;
                } else if(statusWords.danger.includes(text)) {
                    td.innerHTML = `<span class="badge border-0 px-3 py-2 shadow-sm" style="background-color: #EF4444!important; color: #fff; font-size:13px;">${td.innerText}</span>`;
                } else if(statusWords.warning.includes(text)) {
                    td.innerHTML = `<span class="badge border-0 px-3 py-2 shadow-sm" style="background-color: #F59E0B!important; color: #fff; font-size:13px;">${td.innerText}</span>`;
                }
            }
        });

        // 3. تحسين لوحة القيادة (Dashboard) تلقائياً
        if(window.location.href.includes('dashboard') || document.title.includes('لوحة')) {
            const pageContent = document.querySelector('.page-content');
            if(pageContent) {
                // حقن بانر الترحيب (Welcome Hero Banner) في أعلى الداشبورد
                const userName = "{{ Auth::user()->name ?? 'أهلاً بك' }}";
                const welcomeBanner = `
                <div class="card border-0 shadow-sm mb-4" style="background: var(--primary-gradient); overflow: hidden; position: relative;">
                    <!-- تأثير دوائر تجميلية في الخلفية -->
                    <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                    <div style="position: absolute; bottom: -30px; left: 10%; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                    
                    <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap" style="position: relative; z-index: 1;">
                        <div>
                            <h3 class="fw-bold mb-1 text-white">مرحباً بك مجدداً، ${userName}! 👋</h3>
                            <p class="mb-0 text-white-50" style="font-size: 15px;">إليك نظرة سريعة ومحدثة على أداء المنشأة والمهام اليوم.</p>
                        </div>
                        <div class="mt-3 mt-md-0">
                            <button class="btn bg-white text-primary shadow-sm px-4 py-2" style="border-radius: 12px; font-weight: bold;" data-bs-toggle="modal" data-bs-target="#quickPunchModal">
                                <i class="fa fa-fingerprint fs-5 me-2" style="vertical-align: middle;"></i> تسجيل الحضور اللحظي
                            </button>
                        </div>
                    </div>
                </div>
                `;
                
                // وضع البانر مباشرة بعد الـ Header (الـ Breadcrumb)
                const pageHeader = document.querySelector('.page-header') || pageContent.firstElementChild;
                if(pageHeader) pageHeader.insertAdjacentHTML('afterend', welcomeBanner);
            }
        }

        // 4. تحسين واجهات صفحات الإعدادات والرواتب (Payroll & Settings UI)
        if(window.location.href.includes('setting') || window.location.href.includes('payroll') || window.location.href.includes('bonus') || window.location.href.includes('overtime')) {
            
            // إضافة لمسة جمالية (Top Border) للبطاقات لتبدو كوحدات تحكم مستقلة
            const formCards = document.querySelectorAll('.card');
            formCards.forEach(card => {
                card.style.borderTop = '5px solid var(--primary-color)';
            });
            
            // إنشاء "زر حفظ عائم (Sticky Action Bar)" يظهر دائماً في أسفل الشاشة
            const submitBtns = document.querySelectorAll('button[type="submit"]');
            submitBtns.forEach(btn => {
                const actionWrapper = document.createElement('div');
                actionWrapper.style.position = 'fixed';
                actionWrapper.style.bottom = '80px'; // يرتفع قليلاً فوق قائمة الموبايل السفلية
                actionWrapper.style.left = '50%';
                actionWrapper.style.transform = 'translateX(-50%)';
                actionWrapper.style.zIndex = '999';
                actionWrapper.style.background = 'rgba(255,255,255,0.9)';
                actionWrapper.style.backdropFilter = 'blur(10px)';
                actionWrapper.style.padding = '10px 25px';
                actionWrapper.style.borderRadius = '50px';
                actionWrapper.style.boxShadow = '0 10px 25px rgba(138, 12, 81, 0.2)';
                actionWrapper.style.border = '1px solid rgba(138, 12, 81, 0.1)';
                
                const clonedBtn = btn.cloneNode(true);
                clonedBtn.style.margin = '0';
                clonedBtn.style.padding = '10px 35px';
                clonedBtn.style.fontSize = '16px';
                clonedBtn.style.borderRadius = '25px';
                
                actionWrapper.appendChild(clonedBtn);
                document.body.appendChild(actionWrapper);
                
                clonedBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    btn.click(); // تفعيل الزر الأصلي لإرسال الفورم
                    clonedBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> جاري الحفظ...';
                });
            });
        }

        // 5. سكربت الكاميرا والموقع لمودال البصمة اللحظية (Quick Punch)
        const quickPunchModal = document.getElementById('quickPunchModal');
        if (quickPunchModal) {
            const video = document.getElementById('quickCameraVideo');
            const icon = document.getElementById('quickCameraIcon');
            const canvas = document.getElementById('quickCameraCanvas');
            const btnCheckIn = document.getElementById('quickBtnCheckIn');
            const btnCheckOut = document.getElementById('quickBtnCheckOut');
            const locationStatus = document.getElementById('quickPunchLocationStatus');
            
            let stream = null; let userLat = null; let userLng = null; let isMockLocation = false;

            quickPunchModal.addEventListener('show.bs.modal', function () {
                if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                    navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } }).then(function(s) {
                        stream = s; video.srcObject = stream; icon.style.display = 'none';
                    }).catch(function(err) {
                        locationStatus.innerHTML = '<span class="text-danger"><i class="fa fa-exclamation-triangle me-1"></i> تعذر الوصول للكاميرا!</span>';
                    });
                }
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            userLat = position.coords.latitude; userLng = position.coords.longitude;
                            isMockLocation = position.coords.accuracy > 1000; 
                            locationStatus.innerHTML = '<span class="text-success"><i class="fa fa-check-circle me-1"></i> تم تحديد الموقع بنجاح ✓</span>';
                            btnCheckIn.disabled = false; btnCheckOut.disabled = false;
                        },
                        function(error) { locationStatus.innerHTML = '<span class="text-danger"><i class="fa fa-exclamation-triangle me-1"></i> يرجى تفعيل الـ GPS لتسجيل الحضور.</span>'; },
                        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                    );
                }
            });

            quickPunchModal.addEventListener('hidden.bs.modal', function () {
                if(stream) stream.getTracks().forEach(track => track.stop());
                btnCheckIn.disabled = true; btnCheckOut.disabled = true; icon.style.display = 'block';
                locationStatus.innerHTML = '<span class="text-warning"><i class="fa fa-spinner fa-spin me-1"></i> جاري تحديد موقعك الجغرافي...</span>';
            });

            function submitPunch(type) {
                btnCheckIn.disabled = true; btnCheckOut.disabled = true;
                locationStatus.innerHTML = '<span class="text-primary"><i class="fa fa-spinner fa-spin me-1"></i> جاري مطابقة البصمة والموقع...</span>';
                const context = canvas.getContext('2d');
                canvas.width = video.videoWidth || 300; canvas.height = video.videoHeight || 300;
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                
                $.ajax({
                    url: "{{ url('admin/dashboard/attendance') }}/" + type,
                    type: "POST",
                    data: { _token: "{{ csrf_token() }}", lat: userLat, long: userLng, is_mock: isMockLocation, image: canvas.toDataURL('image/jpeg') },
                    success: function(response) {
                        locationStatus.innerHTML = '<span class="text-success fw-bold"><i class="fa fa-check-circle fs-5 me-1"></i> ' + (response.message || 'تم التسجيل بنجاح') + '</span>';
                        setTimeout(() => location.reload(), 2000);
                    },
                    error: function(xhr) {
                        let errorMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'حدث خطأ غير متوقع';
                        locationStatus.innerHTML = '<span class="text-danger fw-bold"><i class="fa fa-times-circle fs-5 me-1"></i> ' + errorMsg + '</span>';
                        setTimeout(() => { btnCheckIn.disabled = false; btnCheckOut.disabled = false; }, 3000);
                    }
                });
            }

            btnCheckIn.addEventListener('click', () => submitPunch('checkIn'));
            btnCheckOut.addEventListener('click', () => submitPunch('checkOut'));
        }
    });
</script>
</body>

</html>
