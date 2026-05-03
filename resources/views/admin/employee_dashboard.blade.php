@extends('layouts.master')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --primary-color: #6366f1;
        --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        --hover-shadow: 0 10px 25px rgba(99, 102, 241, 0.1);
    }

    body, * { font-family: 'Cairo', sans-serif !important; }
    body { background-color: #f8fafc !important; }
    
    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease-in-out;
    }
    .glass-card:hover { transform: translateY(-5px); box-shadow: var(--hover-shadow); }

    /* انسيابية الظهور */
    @keyframes slideUpFade { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    .animate-slide-up { animation: slideUpFade 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }

    /* جداول عصرية للموظف */
    .table.custom-table { border-collapse: separate; border-spacing: 0 6px; }
    .table.custom-table thead th { border: none; color: #64748b; font-size: 0.85rem; font-weight: 600; padding-bottom: 0.5rem; }
    .table.custom-table tbody tr { background: #ffffff; box-shadow: 0 2px 5px rgba(0,0,0,0.02); transition: all 0.2s; border-radius: 8px; }
    .table.custom-table tbody tr:hover { transform: scale(1.01); box-shadow: var(--hover-shadow); z-index: 2; position: relative; }
    .table.custom-table tbody td { border: none; padding: 12px 16px; vertical-align: middle; }
    .table.custom-table tbody td:first-child { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }
    .table.custom-table tbody td:last-child { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }

    /* Mobile Floating Action Button (FAB) */
    .mobile-fab {
        position: fixed;
        bottom: 30px;
        left: 30px; /* Arabic layout */
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        z-index: 1050;
    }
    @media (min-width: 768px) {
        .mobile-fab { display: none !important; }
    }
</style>
@endsection

@section('content')
<div class="page-content animate-slide-up">
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-4">
        <div>
            <h4 class="mb-2 mb-md-0">مرحباً بك، {{ auth()->user()->name }} 👋</h4>
            <p class="text-muted">إليك ملخص سريع لمهامك وحضورك اليوم.</p>
        </div>
    </div>

    <!-- تجميعه المهام (Gamification) -->
    <div class="row mb-4 animate-slide-up delay-1">
        <div class="col-12 stretch-card">
            <div class="card glass-card border-0" style="border-radius: 15px;">
                <div class="card-body">
                    @php
                        $kpiScore = isset($currentMonthKpi) ? $currentMonthKpi->total_score : 100; // 100 كافتراضي في حال لم يتم التقييم بعد
                        $kpiLabel = 'ممتاز';
                        $kpiColor = 'bg-primary';
                        if($kpiScore < 50) { $kpiLabel = 'تحتاج لتطوير'; $kpiColor = 'bg-danger'; }
                        elseif($kpiScore < 75) { $kpiLabel = 'جيد'; $kpiColor = 'bg-warning'; }
                        elseif($kpiScore < 90) { $kpiLabel = 'جيد جداً'; $kpiColor = 'bg-info'; }
                    @endphp
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-title mb-0">مستوى أدائك العام (KPI) هذا الشهر 🌟</h6>
                        <span class="text-primary fw-bold">{{ $kpiLabel }} ({{ $kpiScore }}%)</span>
                    </div>
                    <div class="progress mb-2" style="height: 10px; border-radius: 5px; background-color: #f1f5f9;">
                        <div class="progress-bar {{ $kpiColor }}" role="progressbar" style="width: {{ $kpiScore }}%;" aria-valuenow="{{ $kpiScore }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="text-muted small mb-0">
                        {{ isset($currentMonthKpi->feedback) ? $currentMonthKpi->feedback : 'استمر في هذا الأداء الرائع لفتح المكافآت وتحقيق أهدافك.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- بطاقات الإحصائيات الملونة -->
    <div class="row animate-slide-up delay-2">
        <!-- رصيد الإجازات المتبقي -->
        <div class="col-12 col-xl-4 stretch-card mb-4">
            <div class="card bg-primary text-white shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <h6 class="card-title mb-0 text-white">رصيد الإجازات السنوي</h6>
                    </div>
                    <div class="row mt-3">
                        <div class="col-8">
                            <h3 class="mb-2 fw-bold">{{ auth()->user()->leave_allocated ?? 0 }} <span class="fs-6 fw-normal">يوم</span></h3>
                            <a href="{{ route('admin.leave-request.index') }}" class="text-white text-decoration-underline" style="opacity: 0.9;">تقديم طلب إجازة</a>
                        </div>
                        <div class="col-4 d-flex justify-content-end align-items-center opacity-50">
                            <i data-feather="calendar" style="width: 48px; height: 48px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- المهام المطلوبة اليوم -->
        <div class="col-12 col-xl-4 stretch-card mb-4">
            <div class="card bg-warning text-white shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <h6 class="card-title mb-0 text-white">المهام قيد التنفيذ</h6>
                    </div>
                    <div class="row mt-3">
                        <div class="col-8">
                            <h3 class="mb-2 fw-bold">{{ $todayTasksCount ?? 0 }} <span class="fs-6 fw-normal">مهمة</span></h3>
                            <a href="{{ route('admin.tasks.index') }}" class="text-white text-decoration-underline" style="opacity: 0.9;">عرض المهام</a>
                        </div>
                        <div class="col-4 d-flex justify-content-end align-items-center opacity-50">
                            <i data-feather="check-square" style="width: 48px; height: 48px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- ساعات العمل الشهرية -->
        <div class="col-12 col-xl-4 stretch-card mb-4">
            <div class="card bg-success text-white shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <h6 class="card-title mb-0 text-white">ساعات العمل (الشهر الحالي)</h6>
                    </div>
                    <div class="row mt-3">
                        <div class="col-8">
                            <h3 class="mb-2 fw-bold">{{ number_format($workedHoursThisMonth, 1) }} <span class="fs-6 fw-normal">ساعة</span></h3>
                            <a href="{{ route('admin.attendances.index') }}" class="text-white text-decoration-underline" style="opacity: 0.9;">سجل الحضور</a>
                        </div>
                        <div class="col-4 d-flex justify-content-end align-items-center opacity-50">
                            <i data-feather="clock" style="width: 48px; height: 48px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- زر تسجيل الحضور والانصراف (الخدمة الذاتية للديسكتوب) -->
    <div class="row justify-content-center mt-3 d-none d-md-flex animate-slide-up delay-3">
        <div class="col-md-8 col-lg-6 text-center stretch-card">
            <div class="card glass-card shadow-sm border-0" style="border-radius: 20px;">
                <div class="card-body py-5">
                    <h5 class="card-title text-muted mb-4">بوابة تسجيل الدخول السريع</h5>
                    
                    @if(isset($todayAttendance) && !$todayAttendance->check_out_at)
                        <div class="mb-4">
                            <p class="text-primary fw-bold mb-0">
                                <i data-feather="check-circle" class="me-1"></i>
                                مسجل دخول منذ الساعة: {{ \Carbon\Carbon::parse($todayAttendance->check_in_at)->format('h:i A') }}
                            </p>
                        </div>
                        <!-- زر الانصراف -->
                        <a href="{{ route('admin.employees.check-out', ['companyId' => auth()->user()->company_id ?? 1, 'userId' => auth()->id()]) }}" 
                           class="btn btn-danger btn-lg rounded-pill w-100 py-3 fs-5 shadow d-flex justify-content-center align-items-center"
                           onclick="return confirm('هل أنت متأكد من تسجيل الانصراف الآن؟')">
                            <i data-feather="log-out" class="me-2"></i> تسجيل انصراف
                        </a>
                    @elseif(isset($todayAttendance) && $todayAttendance->check_out_at)
                        <!-- حالة اكتمال الحضور والانصراف -->
                        <div class="alert alert-success rounded-pill py-3 fw-bold">
                            <i data-feather="smile" class="me-2"></i> لقد قمت بتسجيل الحضور والانصراف اليوم. شكراً لجهودك!
                        </div>
                    @else
                        <!-- زر الحضور -->
                        <a href="{{ route('admin.employees.check-in', ['companyId' => auth()->user()->company_id ?? 1, 'userId' => auth()->id()]) }}" 
                           class="btn btn-primary btn-lg rounded-pill w-100 py-3 fs-5 shadow d-flex justify-content-center align-items-center"
                           onclick="return confirm('هل أنت متأكد من تسجيل الحضور الآن؟')">
                            <i data-feather="log-in" class="me-2"></i> تسجيل حضور
                        </a>
                    @endif
                    
                </div>
            </div>
        </div>
    </div>

    <!-- زر تسجيل الحضور العائم (موبايل فقط) -->
    @if(isset($todayAttendance) && !$todayAttendance->check_out_at)
        <a href="{{ route('admin.employees.check-out', ['companyId' => auth()->user()->company_id ?? 1, 'userId' => auth()->id()]) }}" 
           class="btn btn-danger mobile-fab" onclick="return confirm('هل أنت متأكد من تسجيل الانصراف الآن؟')">
            <i data-feather="log-out" style="width: 30px; height: 30px;"></i>
        </a>
    @elseif(!isset($todayAttendance) || (isset($todayAttendance) && $todayAttendance->check_out_at))
        <a href="{{ route('admin.employees.check-in', ['companyId' => auth()->user()->company_id ?? 1, 'userId' => auth()->id()]) }}" 
           class="btn btn-primary mobile-fab shadow-lg" onclick="return confirm('هل أنت متأكد من تسجيل الحضور الآن؟')">
            <i data-feather="log-in" style="width: 30px; height: 30px;"></i>
        </a>
    @endif

    <!-- الجداول السفلية (المهام والحضور) -->
    <div class="row mt-4 animate-slide-up delay-3">
        <!-- المهام الحالية -->
        <div class="col-md-6 grid-margin stretch-card mb-4">
            <div class="card glass-card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title mb-0">مهامي الحالية (قيد التنفيذ)</h6>
                        <a href="{{ route('admin.tasks.index') }}" class="text-primary text-decoration-underline" style="font-size: 14px;">عرض الكل</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table custom-table mb-0">
                            <thead>
                                <tr>
                                    <th>المهمة</th>
                                    <th>الموعد النهائي</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTasks ?? [] as $task)
                                <tr>
                                    <td class="fw-bold">{{ $task->name }}</td>
                                    <td class="text-danger"><i data-feather="calendar" class="icon-sm me-1"></i> {{ \Carbon\Carbon::parse($task->end_date)->format('d M') }}</td>
                                    <td><span class="badge bg-warning">{{ str_replace('_', ' ', $task->status) }}</span></td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">لا توجد مهام مطلوبة منك حالياً 🎉</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- سجل الحضور الأخير -->
        <div class="col-md-6 grid-margin stretch-card mb-4">
            <div class="card glass-card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title mb-0">سجل حضوري الأخير</h6>
                        <a href="{{ route('admin.attendances.index') }}" class="text-primary text-decoration-underline" style="font-size: 14px;">عرض الكل</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table custom-table mb-0">
                            <thead>
                                <tr>
                                    <th>التاريخ</th>
                                    <th>الحضور</th>
                                    <th>الانصراف</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentAttendance ?? [] as $log)
                                <tr>
                                    <td class="fw-bold">{{ \Carbon\Carbon::parse($log->attendance_date)->format('d M Y') }}</td>
                                    <td class="text-success"><i data-feather="arrow-down-left" class="icon-sm me-1"></i> {{ $log->check_in_at ? \Carbon\Carbon::parse($log->check_in_at)->format('h:i A') : '-' }}</td>
                                    <td class="text-danger"><i data-feather="arrow-up-right" class="icon-sm me-1"></i> {{ $log->check_out_at ? \Carbon\Carbon::parse($log->check_out_at)->format('h:i A') : '-' }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted py-4">لا توجد سجلات حضور مؤخراً</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection