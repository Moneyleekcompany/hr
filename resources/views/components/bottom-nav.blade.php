<nav class="mobile-bottom-nav">
    <a href="{{ route('admin.dashboard') }}" class="text-center text-decoration-none text-muted">
        <i class="fa fa-home fs-4 d-block mb-1"></i>
        <span style="font-size: 11px;">{{ __('index.dashboard') }}</span>
    </a>
    
    <a href="{{ route('admin.attendances.index') }}" class="text-center text-decoration-none text-muted">
        <i class="fa fa-clock fs-4 d-block mb-1"></i>
        <span style="font-size: 11px;">{{ __('index.attendance') }}</span>
    </a>
    
    <!-- الزر المركزي الكبير اللحظي (Punch In/Out) -->
    <a href="#" class="text-center text-decoration-none" data-bs-toggle="modal" data-bs-target="#quickPunchModal">
        <div class="text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="background: var(--primary-gradient); width: 56px; height: 56px; margin-top: -24px; box-shadow: 0 6px 15px rgba(138, 12, 81, 0.3);">
            <i class="fa fa-fingerprint fs-3"></i>
        </div>
        <span style="font-size: 11px; color: var(--primary-color); font-weight: bold;">البصمة</span>
    </a>

    <a href="{{ route('admin.leave-request.index') }}" class="text-center text-decoration-none text-muted">
        <i class="fa fa-calendar-alt fs-4 d-block mb-1"></i>
        <span style="font-size: 11px;">{{ __('index.leave') }}</span>
    </a>
    
    <a href="#" class="text-center text-decoration-none text-muted">
        <i class="fa fa-user fs-4 d-block mb-1"></i>
        <span style="font-size: 11px;">{{ __('index.profile') }}</span>
    </a>
</nav>