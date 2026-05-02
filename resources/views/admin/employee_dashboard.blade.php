@extends('layouts.master')

@section('content')
<div class="page-content">
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin mb-4">
        <div>
            <h4 class="mb-2 mb-md-0">مرحباً بك، {{ auth()->user()->name }} 👋</h4>
            <p class="text-muted">إليك ملخص سريع لمهامك وحضورك اليوم.</p>
        </div>
    </div>

    <!-- بطاقات الإحصائيات الملونة -->
    <div class="row">
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

    <!-- زر تسجيل الحضور والانصراف (الخدمة الذاتية) -->
    <div class="row justify-content-center mt-3">
        <div class="col-md-8 col-lg-6 text-center stretch-card">
            <div class="card shadow-sm border-0" style="border-radius: 20px;">
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

    <!-- الجداول السفلية (المهام والحضور) -->
    <div class="row mt-4">
        <!-- المهام الحالية -->
        <div class="col-md-6 grid-margin stretch-card mb-4">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title mb-0">مهامي الحالية (قيد التنفيذ)</h6>
                        <a href="{{ route('admin.tasks.index') }}" class="text-primary text-decoration-underline" style="font-size: 14px;">عرض الكل</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
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
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-title mb-0">سجل حضوري الأخير</h6>
                        <a href="{{ route('admin.attendances.index') }}" class="text-primary text-decoration-underline" style="font-size: 14px;">عرض الكل</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
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