<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Modern Tabler Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <style>
            /* تطبيق خط Cairo */
            * {
                font-family: 'Cairo', sans-serif !important;
            }

            /* تأثير الانيميشن لصفحة الدخول */
            @keyframes fadeInUp {
                0% { opacity: 0; transform: translateY(40px) scale(0.98); }
                100% { opacity: 1; transform: translateY(0) scale(1); }
            }

            /* تحسينات الواجهة العصرية */
            body {
                background: linear-gradient(135deg, #f8fafc 0%, #fce7f3 100%); /* تدرج لوني هادئ يتماشى مع لون النظام */
                min-height: 100vh;
                color: #334155;
            }
            .navbar {
                background: rgba(255, 255, 255, 0.8) !important;
                backdrop-filter: blur(12px);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
                border-bottom: none !important;
                padding: 1rem 0;
            }
            .navbar-brand {
                font-weight: 700 !important;
                color: #0f172a !important;
            }
            .card {
                background: #ffffff !important;
                border: 1px solid rgba(226, 232, 240, 0.8) !important;
                border-radius: 20px !important;
                box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03) !important;
                transition: all 0.3s ease-in-out;
                animation: fadeInUp 0.7s ease-out forwards;
            }
            .card:hover {
                transform: translateY(-4px);
                box-shadow: 0 12px 25px -4px rgba(0, 0, 0, 0.05) !important;
                border-color: rgba(138, 12, 81, 0.3) !important;
            }
            .card-body {
                padding: 2.5rem 2rem !important; /* مساحة أكبر لراحة العين */
            }
            .card-header {
                background-color: transparent !important;
                border-bottom: 1px solid rgba(0, 0, 0, 0.03) !important;
                padding: 1.5rem 2rem !important;
            }
            .btn {
                border-radius: 10px !important;
                font-weight: 600 !important;
                padding: 0.6rem 1.25rem !important;
            }
            
            /* تحسين أزرار تسجيل الدخول (Primary Buttons) */
            .btn-primary {
                background-color: #8a0c51 !important;
                border-color: #8a0c51 !important;
                color: #ffffff !important;
                transition: all 0.3s ease;
            }
            .btn-primary:hover {
                background-color: #6a093e !important;
                border-color: #6a093e !important;
                transform: translateY(-2px);
                box-shadow: 0 6px 15px rgba(138, 12, 81, 0.25);
            }
            
            /* تنسيقات أوزان الخطوط العصرية (Typography) */
            h1, h2, h3, h4, h5, h6 {
                font-weight: 700 !important;
                color: #1e293b !important;
            }
            .card-header {
                font-weight: 700 !important;
                font-size: 1.15rem;
                color: #0f172a;
            }
            label {
                font-weight: 600 !important;
                color: #334155;
                margin-bottom: 0.4rem;
            }
            .nav-link, .dropdown-item {
                font-weight: 600 !important;
            }

            /* تحسين حقول الإدخال (Modern Inputs) */
            .form-control, .form-select {
                border-radius: 12px !important;
                border: 1px solid rgba(226, 232, 240, 0.8) !important;
                padding: 0.8rem 1.25rem !important;
                font-size: 0.95rem;
                background-color: #f8fafc;
                transition: all 0.3s ease;
                box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.02) !important;
            }
            .form-control:focus, .form-select:focus {
                background-color: #ffffff;
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
        </style>
    </head>
<body>
    <div id="app">
        @auth
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

{{--                            @if (Route::has('register'))--}}
{{--                                <li class="nav-item">--}}
{{--                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>--}}
{{--                                </li>--}}
{{--                            @endif--}}
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        @endauth

        <main class="py-4 @guest d-flex flex-column justify-content-center align-items-center min-vh-100 position-relative overflow-hidden @endguest">
            @guest
            <!-- زخارف خلفية عصرية (Glassmorphism) -->
            <div style="position: absolute; top: -100px; right: -100px; width: 350px; height: 350px; background: rgba(138, 12, 81, 0.12); border-radius: 50%; filter: blur(70px); z-index: 0;"></div>
            <div style="position: absolute; bottom: -100px; left: -100px; width: 350px; height: 350px; background: rgba(138, 12, 81, 0.12); border-radius: 50%; filter: blur(70px); z-index: 0;"></div>
            
            <!-- شعار يتوسط الصفحة -->
            <div class="text-center mb-4" style="animation: fadeInUp 0.5s ease-out forwards; z-index: 10;">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 85px; height: 85px; background-color: rgba(138, 12, 81, 0.1); border: 1px solid rgba(138, 12, 81, 0.2); box-shadow: 0 4px 20px rgba(138, 12, 81, 0.15);">
                    <!-- يمكنك استبدال هذه الأيقونة بوسم img لوضع صورتك الخاصة -->
                    <i class="ti ti-building-castle fs-1" style="color: #8a0c51;"></i>
                </div>
                <h2 class="fw-bold mb-1" style="color: #8a0c51; letter-spacing: -0.5px;">{{ config('app.name', 'Castle HR') }}</h2>
                <p class="text-muted" style="font-weight: 600; font-size: 0.95rem;">مرحباً بك في لوحة تحكم الموظفين</p>
            </div>
            @endguest
            
            <div class="w-100" style="z-index: 10;">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- سكربت إضافة زر إظهار/إخفاء كلمة المرور -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInputs = document.querySelectorAll('input[type="password"]');
            passwordInputs.forEach(function(input) {
                const wrapper = document.createElement('div');
                wrapper.style.position = 'relative';
                wrapper.style.width = '100%';
                
                input.parentNode.insertBefore(wrapper, input);
                wrapper.appendChild(input);
                
                const icon = document.createElement('i');
                icon.className = 'ti ti-eye';
                icon.style.position = 'absolute';
                icon.style.top = '50%';
                icon.style.insetInlineEnd = '15px'; /* يتجاوب مع الـ LTR والـ RTL تلقائياً */
                icon.style.transform = 'translateY(-50%)';
                icon.style.cursor = 'pointer';
                icon.style.color = '#64748b';
                icon.style.fontSize = '1.25rem';
                icon.style.zIndex = '10';
                icon.style.transition = 'color 0.2s ease';
                
                wrapper.appendChild(icon);
                
                icon.addEventListener('click', function() {
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('ti-eye');
                        icon.classList.add('ti-eye-off');
                        icon.style.color = '#8a0c51';
                    } else {
                        input.type = 'password';
                        icon.classList.remove('ti-eye-off');
                        icon.classList.add('ti-eye');
                        icon.style.color = '#64748b';
                    }
                });
            });
        });
    </script>
</body>
</html>
