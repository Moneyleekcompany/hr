@php
    $locale = \Illuminate\Support\Facades\App::getLocale();
    $currentTheme = \App\Helpers\AppHelper::getTheme();
    $rtl = in_array($locale, ['ar', 'ur', 'he', 'fa']);
@endphp
<!DOCTYPE html>
<html lang="{{ $locale ?? 'en' }}"
      class="theme-loading"
      data-theme="{{ $currentTheme }}"
      @if($rtl) dir="rtl" @endif>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Digital HR Complete HR Attendance System">
    <meta name="author" content="Digital HR">
    <meta name="keywords" content="Digital HR">
    <meta name="color-scheme" content="{{ $currentTheme === 'dark' ? 'dark light' : 'light dark' }}">

    <title>@yield('title')</title>

    <!-- منع flash من الثيم: نُزيل theme-loading بعد تطبيق الـ initial styles -->
    <script>
        (function () {
            // اقرأ ثيم محفوظ محليًا لتفادي وميض السيرفر/العميل
            try {
                var saved = localStorage.getItem('hr-theme');
                if (saved === 'light' || saved === 'dark') {
                    document.documentElement.setAttribute('data-theme', saved);
                }
            } catch (e) {}
            // يُزال بعد التحميل لإعادة تفعيل الـ transitions
            window.addEventListener('load', function () {
                document.documentElement.classList.remove('theme-loading');
            });
        })();
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Modern Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    @include('admin.section.head_links')
    @yield('styles')

    <!-- Smart Workspace (legacy components) ثم Modern theme (يطغى على المتغيرات) -->
    <link rel="stylesheet" href="{{ asset('css/smart-workspace.css') }}?v={{ filemtime(public_path('css/smart-workspace.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/modern-theme.css') }}?v={{ filemtime(public_path('css/modern-theme.css')) }}">
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
        <h5 class="modal-title fw-bold" style="color: var(--color-brand-500);">تسجيل الحضور اللحظي</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center pt-2">
        <p class="text-muted small mb-3">يرجى السماح بصلاحيات الكاميرا والموقع لتسجيل بصمتك بنجاح.</p>
        
        <!-- إطار الكاميرا الدائري -->
        <div class="position-relative mx-auto bg-light d-flex align-items-center justify-content-center" style="width: 240px; height: 240px; border-radius: 50%; overflow: hidden; border: 4px solid var(--color-brand-500); box-shadow: 0 10px 20px rgba(138, 12, 81, 0.15);">
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

<!-- Global UI Transformer: animations, table modernizing, badges, dashboard hero, micro-interactions -->
<script>
(function () {
    'use strict';

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const isRtl = document.documentElement.getAttribute('dir') === 'rtl';
    const STATUS_WORDS = {
        success: ['active', 'approved', 'paid', 'present', 'مقبول', 'مكتمل', 'نشط', 'حاضر'],
        danger:  ['inactive', 'rejected', 'unpaid', 'absent', 'مرفوض', 'ملغى', 'ملغي', 'غير نشط', 'غائب'],
        warning: ['pending', 'on_hold', 'late', 'قيد الانتظار', 'متأخر', 'معلق']
    };

    function modernizeTables() {
        document.querySelectorAll('.table').forEach(t => {
            t.classList.add('custom-table', 'border-0');
            t.classList.remove('table-bordered', 'table-striped');
            t.style.backgroundColor = 'transparent';
            const wrap = t.parentElement;
            if (wrap && wrap.classList.contains('table-responsive')) {
                wrap.style.border = 'none';
            }
        });
    }

    function autoBadgeStatusCells() {
        document.querySelectorAll('td').forEach(td => {
            if (td.children.length !== 0) return;
            const raw = td.innerText.trim();
            if (raw.length < 2 || raw.length > 20) return;
            const text = raw.toLowerCase();
            let kind = null;
            if (STATUS_WORDS.success.includes(text)) kind = 'bg-success';
            else if (STATUS_WORDS.danger.includes(text)) kind = 'bg-danger';
            else if (STATUS_WORDS.warning.includes(text)) kind = 'bg-warning';
            if (kind) {
                td.innerHTML = `<span class="badge ${kind}">${raw}</span>`;
            }
        });
    }

    function injectDashboardHero() {
        const onDashboard = window.location.href.includes('dashboard') || document.title.includes('لوحة');
        if (!onDashboard) return;
        const pageContent = document.querySelector('.page-content');
        if (!pageContent) return;

        const userName = @json(Auth::user()->name ?? 'أهلاً بك');
        const safeName = String(userName).replace(/[<>&"]/g, '');
        const banner = document.createElement('div');
        banner.className = 'card mt-hero mb-4 mt-reveal';
        banner.innerHTML = `
            <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap" style="position: relative; z-index: 1;">
                <div>
                    <h3 class="fw-bold mb-1 text-white">مرحباً بك مجدداً، ${safeName}! 👋</h3>
                    <p class="mb-0" style="color: rgba(255,255,255,0.85); font-size: 15px;">
                        إليك نظرة سريعة ومحدثة على أداء المنشأة والمهام اليوم.
                    </p>
                </div>
                <div class="mt-3 mt-md-0">
                    <button class="btn px-4 py-2 mt-pulse-ring" style="background:#fff; color: var(--color-brand-500); border-radius: 12px; font-weight: 700;" data-bs-toggle="modal" data-bs-target="#quickPunchModal">
                        <i class="fa fa-fingerprint fs-5 me-2"></i> تسجيل الحضور اللحظي
                    </button>
                </div>
            </div>`;
        const header = document.querySelector('.page-header') || pageContent.firstElementChild;
        if (header) header.insertAdjacentElement('afterend', banner);
    }

    function injectStickyActionBar() {
        const isFormPage = ['setting', 'payroll', 'bonus', 'overtime'].some(k => window.location.href.includes(k));
        if (!isFormPage) return;
        const submits = document.querySelectorAll('button[type="submit"]');
        submits.forEach(btn => {
            const wrap = document.createElement('div');
            wrap.className = 'mt-sticky-action';
            const clone = btn.cloneNode(true);
            clone.classList.add('btn-primary');
            clone.style.padding = '10px 32px';
            clone.style.fontSize = '15px';
            clone.style.borderRadius = '999px';
            clone.style.margin = '0';
            wrap.appendChild(clone);
            document.body.appendChild(wrap);
            clone.addEventListener('click', e => {
                e.preventDefault();
                btn.click();
                clone.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> جاري الحفظ...';
            });
        });
    }

    // Scroll-reveal للكروت والصفوف باستخدام IntersectionObserver
    function setupScrollReveal() {
        if (reduceMotion || !('IntersectionObserver' in window)) {
            document.querySelectorAll('.mt-reveal').forEach(el => el.classList.add('is-visible'));
            return;
        }
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const delay = parseInt(entry.target.dataset.revealDelay || '0', 10);
                    setTimeout(() => entry.target.classList.add('is-visible'), delay);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });

        // أضف الـ class تلقائيًا على كل كارت لو مش متضاف، ثم راقب
        document.querySelectorAll('.card, .mt-reveal').forEach((el, i) => {
            if (!el.classList.contains('mt-reveal')) el.classList.add('mt-reveal');
            if (!el.dataset.revealDelay) el.dataset.revealDelay = String(Math.min(i * 60, 360));
            observer.observe(el);
        });
    }

    // Ripple على الأزرار
    function setupButtonRipples() {
        if (reduceMotion) return;
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn');
            if (!btn || btn.disabled) return;
            const rect = btn.getBoundingClientRect();
            const ripple = document.createElement('span');
            ripple.className = 'mt-ripple';
            const size = Math.max(rect.width, rect.height);
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
            ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
            btn.appendChild(ripple);
            ripple.addEventListener('animationend', () => ripple.remove(), { once: true });
        }, { passive: true });
    }

    // Theme toggle: نسجّل اختيار المستخدم في localStorage عشان نمنع flash بعد التوجيه
    function setupThemeMemo() {
        const initial = document.documentElement.getAttribute('data-theme');
        try { localStorage.setItem('hr-theme', initial); } catch (e) {}
        document.addEventListener('click', (e) => {
            const tgt = e.target.closest('#moon, #sun, [data-theme-toggle]');
            if (!tgt) return;
            const next = (document.documentElement.getAttribute('data-theme') === 'dark') ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            try { localStorage.setItem('hr-theme', next); } catch (err) {}
        });
    }

    function setupQuickPunch() {
        const quickPunchModal = document.getElementById('quickPunchModal');
        if (!quickPunchModal) return;

        const video = document.getElementById('quickCameraVideo');
        const icon = document.getElementById('quickCameraIcon');
        const canvas = document.getElementById('quickCameraCanvas');
        const btnCheckIn = document.getElementById('quickBtnCheckIn');
        const btnCheckOut = document.getElementById('quickBtnCheckOut');
        const locationStatus = document.getElementById('quickPunchLocationStatus');

        let stream = null, userLat = null, userLng = null, isMockLocation = false;

        quickPunchModal.addEventListener('show.bs.modal', function () {
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } }).then(function (s) {
                    stream = s; video.srcObject = stream; icon.style.display = 'none';
                }).catch(function () {
                    locationStatus.innerHTML = '<span class="text-danger"><i class="fa fa-exclamation-triangle me-1"></i> تعذر الوصول للكاميرا!</span>';
                });
            }
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        userLat = position.coords.latitude; userLng = position.coords.longitude;
                        isMockLocation = position.coords.accuracy > 1000;
                        locationStatus.innerHTML = '<span class="text-success"><i class="fa fa-check-circle me-1"></i> تم تحديد الموقع بنجاح ✓</span>';
                        btnCheckIn.disabled = false; btnCheckOut.disabled = false;
                    },
                    function () { locationStatus.innerHTML = '<span class="text-danger"><i class="fa fa-exclamation-triangle me-1"></i> يرجى تفعيل الـ GPS لتسجيل الحضور.</span>'; },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            }
        });

        quickPunchModal.addEventListener('hidden.bs.modal', function () {
            if (stream) stream.getTracks().forEach(track => track.stop());
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
                type: 'POST',
                data: { _token: "{{ csrf_token() }}", lat: userLat, long: userLng, is_mock: isMockLocation, image: canvas.toDataURL('image/jpeg') },
                success: function (response) {
                    locationStatus.innerHTML = '<span class="text-success fw-bold"><i class="fa fa-check-circle fs-5 me-1"></i> ' + (response.message || 'تم التسجيل بنجاح') + '</span>';
                    setTimeout(() => location.reload(), 2000);
                },
                error: function (xhr) {
                    const errorMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'حدث خطأ غير متوقع';
                    locationStatus.innerHTML = '<span class="text-danger fw-bold"><i class="fa fa-times-circle fs-5 me-1"></i> ' + errorMsg + '</span>';
                    setTimeout(() => { btnCheckIn.disabled = false; btnCheckOut.disabled = false; }, 3000);
                }
            });
        }

        btnCheckIn.addEventListener('click', () => submitPunch('checkIn'));
        btnCheckOut.addEventListener('click', () => submitPunch('checkOut'));
    }

    document.addEventListener('DOMContentLoaded', function () {
        modernizeTables();
        autoBadgeStatusCells();
        injectDashboardHero();
        injectStickyActionBar();
        setupScrollReveal();
        setupButtonRipples();
        setupThemeMemo();
        setupQuickPunch();
    });
})();
</script>
</body>

</html>
