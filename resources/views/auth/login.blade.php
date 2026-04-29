
@extends('auth.main')

@section('title', __('auth.login'))

@section('page-styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<style>
    /* تأثيرات الدخول (Animations) */
    @keyframes fadeInUp {
        0% { opacity: 0; transform: translateY(30px) scale(0.95); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-8px); }
        100% { transform: translateY(0px); }
    }
    
    /* تأثير انتقال الصفحة عند الإرسال (Page Transition) */
    .page-transition {
        animation: fadeOutUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
    }
    @keyframes fadeOutUp {
        0% { opacity: 1; transform: translateY(0) scale(1); }
        100% { opacity: 0; transform: translateY(-20px) scale(0.95); }
    }
    
    /* تنسيق الكارت */
    .auth-card {
        border-radius: 24px;
        box-shadow: 0 10px 40px rgba(138, 12, 81, 0.08);
        border: 1px solid rgba(138, 12, 81, 0.05);
        background: #ffffff;
        overflow: hidden;
        animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    
    /* تنسيق الشعار */
    .auth-logo-wrapper {
        width: 110px;
        height: 110px;
        margin: 0 auto 1.5rem;
        padding: 12px;
        background: #f8fafc;
        border-radius: 50%;
        border: 2px solid rgba(138, 12, 81, 0.1);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
        animation: float 4s ease-in-out infinite;
    }
    .auth-logo {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 50%;
    }

    /* تنسيق الحقول */
    .form-control {
        border-radius: 12px;
        padding: 0.85rem 1.25rem;
        font-size: 0.95rem;
        border: 1px solid #e2e8f0;
        background-color: #f8fafc;
        transition: all 0.3s ease;
    }
    .form-control:focus {
        background-color: #ffffff;
        border-color: #8a0c51;
        box-shadow: 0 0 0 4px rgba(138, 12, 81, 0.15);
    }
    
    /* تنسيق الزر */
    .btn-login {
        background-color: #8a0c51;
        border-color: #8a0c51;
        border-radius: 12px;
        padding: 0.85rem;
        font-weight: 700;
        font-size: 1.05rem;
        transition: all 0.3s ease;
    }
    .btn-login:hover {
        background-color: #6a093e;
        border-color: #6a093e;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(138, 12, 81, 0.25);
    }

    /* تنسيق خيار تذكرني */
    .custom-checkbox .form-check-input {
        width: 1.15em;
        height: 1.15em;
        border-color: #cbd5e1;
        cursor: pointer;
    }
    .custom-checkbox .form-check-input:checked {
        background-color: #8a0c51;
        border-color: #8a0c51;
    }
    .custom-checkbox .form-check-input:focus {
        box-shadow: 0 0 0 0.25rem rgba(138, 12, 81, 0.25);
        border-color: #8a0c51;
    }
    .custom-checkbox .form-check-label {
        cursor: pointer;
        color: #475569;
        font-weight: 600;
        font-size: 0.95rem;
        margin-inline-start: 0.4rem;
        user-select: none;
    }

    /* دوران أيقونة التحميل */
    .ti-spin {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        100% { transform: rotate(360deg); }
    }
</style>
@endsection

@section('auth-content')
    <section class="content" style="background: linear-gradient(135deg, #f8fafc 0%, #fce7f3 100%);">
            <div class="main-wrapper">
                <div class="page-wrapper full-page">
                <div class="page-content d-flex align-items-center justify-content-center">
                    <div class="row w-100 mx-0 auth-page">
                        <div class="col-md-6 col-lg-5 col-xl-4 mx-auto">
                            <div class="card auth-card">
                                <div class="card-body px-4 py-5 px-md-5 text-center">
                                    
                                    <!-- الشعار والاسم -->
                                    <div class="auth-logo-wrapper">
                                        <img src="{{ $companyDetail && $companyDetail->logo ? asset(\App\Models\Company::UPLOAD_PATH.$companyDetail->logo) : asset('assets/images/img.png') }}"
                                             class="auth-logo"
                                             alt="{{ __('auth.company_logo_alt') }}">
                                    </div>
                                    <h3 class="fw-bolder mb-2" style="color: #8a0c51; letter-spacing: -0.5px;">
                                        {{ $companyDetail ? ucfirst($companyDetail->name) : 'Castle' }}
                                    </h3>
                                    <p class="text-muted fw-medium mb-4" style="font-size: 0.95rem;">{{ __('auth.welcome_back') }}</p>

                                    <!-- رسائل التنبيهات (Flash Messages) -->
                                    @include('admin.section.flash_message')

                                    <!-- نموذج الدخول -->
                                    <form class="forms-sample text-start" method="POST" action="{{ route('admin.login.process') }}">
                                                @csrf
                                                <div class="mb-4">
                                                    <label for="userEmail" class="form-label fw-bold" style="color: #334155;">{{ __('auth.email_username') }}</label>
                                                    <input
                                                        class="form-control @error('email') is-invalid @enderror"
                                                        name="email" value="{{ old('email') }}"
                                                        required
                                                        autocomplete="email"
                                                        autofocus
                                                        placeholder="{{ __('auth.email_username') }}"
                                                    >
                                                    @if ($errors->has('username'))
                                                        <span class="text-danger">
                                                        <strong>{{ $errors->first('username') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>

                                                <div class="mb-3">
                                                    <label for="userPassword" class="form-label fw-bold" style="color: #334155;">{{ __('auth.password') }}</label>
                                                    <div style="position: relative;">
                                                        <input id="password"
                                                               type="password"
                                                               class="form-control @error('password') is-invalid @enderror"
                                                               name="password"
                                                               required
                                                               autocomplete="current-password"
                                                               placeholder="••••••••"
                                                               style="padding-inline-end: 40px;"
                                                        >
                                                        <i class="ti ti-eye toggle-password" onclick="togglePasswordVisibility()" style="position: absolute; top: 50%; transform: translateY(-50%); inset-inline-end: 15px; cursor: pointer; color: #94a3b8; font-size: 1.2rem; transition: color 0.2s ease;"></i>
                                                    </div>
                                                    @if ($errors->has('password'))
                                                        <span class="text-danger">
                                                        <strong>{{ $errors->first('password') }}</strong>
                                                    </span>
                                                    @endif
                                                </div>

                                                <div class="d-flex justify-content-between align-items-center mb-4">
                                                    <div class="form-check custom-checkbox">
                                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="remember">{{ __('auth.remember_me') }}</label>
                                                    </div>
                                                </div>

                                                <div class="d-grid mt-4">
                                                    <button type="submit" class="btn btn-login text-white" id="loginBtn">
                                                        <span id="btnText">{{ __('auth.login') }}</span>
                                                        <i class="ti ti-loader ti-spin d-none" id="loginSpinner" style="margin-inline-start: 8px;"></i>
                                                    </button>
                                                </div>
                                                
                                                @if (Route::has('password.request'))
                                                    <div class="text-center mt-3">
                                                        <a class="text-muted text-decoration-none fw-medium" href="{{ route('password.request') }}" style="font-size: 0.9rem; transition: color 0.2s ease;">
                                                            {{ __('auth.forgot_password') }}
                                                        </a>
                                                    </div>
                                                @endif
                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            </div>
        </section>

@endsection

@section('page-scripts')
<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.querySelector('.toggle-password');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.replace('ti-eye', 'ti-eye-off');
            toggleIcon.style.color = '#8a0c51';
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.replace('ti-eye-off', 'ti-eye');
            toggleIcon.style.color = '#94a3b8';
        }
    }

    // تأثير انتقال الصفحة وتغيير حالة الزر عند إرسال النموذج
    document.querySelector('form.forms-sample').addEventListener('submit', function() {
        const spinner = document.getElementById('loginSpinner');
        const card = document.querySelector('.auth-card');
        
        // إظهار علامة التحميل (Spinner) داخل الزر
        if(spinner) spinner.classList.remove('d-none');
        
        // بدء تأثير الخروج للصفحة (Fade Out Up)
        if(card) card.classList.add('page-transition');
    });
</script>
@endsection
