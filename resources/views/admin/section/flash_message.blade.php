<style>
    /* تصميم عصري لرسائل النظام */
    .alert-modern {
        border: none !important;
        border-radius: 12px !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05) !important;
        animation: slideInDown 0.4s ease-out forwards;
        padding: 1rem 1.25rem !important;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 12px;
        border-inline-start: 5px solid !important; /* يتجاوب مع الـ LTR والـ RTL تلقائياً */
        margin-bottom: 1.5rem !important;
        text-align: start;
    }
    .alert-modern.alert-success { background-color: #ecfdf5 !important; color: #059669 !important; border-inline-start-color: #10b981 !important; }
    .alert-modern.alert-danger { background-color: #fef2f2 !important; color: #dc2626 !important; border-inline-start-color: #ef4444 !important; }
    .alert-modern.alert-info { background-color: #eff6ff !important; color: #2563eb !important; border-inline-start-color: #3b82f6 !important; }
    .alert-modern.alert-warning { background-color: #fffbeb !important; color: #d97706 !important; border-inline-start-color: #f59e0b !important; }
    
    .alert-modern .close {
        margin-inline-start: auto;
        background: transparent;
        border: none;
        font-size: 1.5rem;
        opacity: 0.5 !important;
        color: inherit !important;
        transition: opacity 0.2s;
        cursor: pointer;
    }
    .alert-modern .close:hover { opacity: 1 !important; }
    
    @keyframes slideInDown {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>

@if (isset($errors) && count($errors) > 0)
    <div class="alert alert-danger alert-modern">
        <i class="ti ti-alert-circle fs-4"></i>
        <div>
            @foreach ($errors->all() as $error)
                <p class="mb-0">{{ $error }}</p>
            @endforeach
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    </div>
@endif

@if (Session::has('success'))
    <div id="message" class="alert alert-success alert-modern {{Session::has('success_important') ? 'alert-important': ''}}">
        <i class="ti ti-circle-check fs-4"></i>
        <div>{{session('success')}}</div>
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    </div>
@endif

@if (Session::has('danger'))
    <div id="message" class="alert alert-danger alert-modern {{Session::has('danger_important') ? 'alert-important': ''}}">
        <i class="ti ti-alert-triangle fs-4"></i>
        <div>{{session('danger')}}</div>
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    </div>
@endif

@if (Session::has('info'))
    <div id="message" class="alert alert-info alert-modern {{Session::has('info_important') ? 'alert-important': ''}}">
        <i class="ti ti-info-circle fs-4"></i>
        <div>{{session('info')}}</div>
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    </div>
@endif

@if (Session::has('warning'))
    <div id="message" class="alert alert-warning alert-modern {{Session::has('warning_important') ? 'alert-important': ''}}">
        <i class="ti ti-alert-triangle fs-4"></i>
        <div>{{session('warning')}}</div>
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    </div>
@endif
