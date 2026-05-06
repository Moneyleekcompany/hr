@extends('layouts.master')

@section('title', 'لوحة التزام الحضور')

@section('action', 'لوحة التزام الحضور الشهري')

@section('main-content')
<section class="content">

    @include('admin.section.flash_message')
    @include('admin.attendance.common.breadcrumb')

    <div class="search-box p-4 pb-0 bg-white rounded mb-3 box-shadow">
        <form class="forms-sample" action="{{ route('admin.attendance.compliance') }}" method="get">
            <h5 class="mb-3">فلترة</h5>
            <div class="row align-items-center">
                <div class="col-lg-3 col-md-4 mb-4">
                    <label class="form-label">الشهر</label>
                    <input type="month" name="month" class="form-control" value="{{ $monthInput }}">
                </div>
                <div class="col-lg-3 col-md-4 mb-4">
                    <label class="form-label">الفرع</label>
                    <select class="form-select" name="branch_id">
                        <option value="">كل الفروع</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" @selected($branchId === $b->id)>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-4 mb-4">
                    <label class="form-label d-block">&nbsp;</label>
                    <button type="submit" class="btn btn-primary">عرض</button>
                </div>
            </div>
        </form>
    </div>

    {{-- KPI cards --}}
    <div class="row mb-3">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card box-shadow h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-1">معدل الالتزام العام</h6>
                    <h3 class="mb-0 {{ $summary['company_compliance_rate'] >= 90 ? 'text-success' : ($summary['company_compliance_rate'] >= 75 ? 'text-warning' : 'text-danger') }}">
                        {{ $summary['company_compliance_rate'] }}%
                    </h3>
                    <small class="text-muted">{{ $summary['recorded_attendance'] }} / {{ $summary['expected_attendance'] }} يوم/موظف</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card box-shadow h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-1">عدد الموظفين</h6>
                    <h3 class="mb-0">{{ $summary['total_employees'] }}</h3>
                    <small class="text-muted">{{ $summary['workdays_in_month'] }} يوم عمل في الشهر</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card box-shadow h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-1">إجمالي الغياب</h6>
                    <h3 class="mb-0 text-danger">{{ $summary['total_absences'] }}</h3>
                    <small class="text-muted">يوم/موظف</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card box-shadow h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-1">انصراف غير مسجّل</h6>
                    <h3 class="mb-0 text-warning">{{ $summary['total_missing_checkouts'] }}</h3>
                    <small class="text-muted">حالة هذا الشهر</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6 mb-3">
            <div class="card box-shadow h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-1">إجمالي ساعات الإضافي</h6>
                    <h3 class="mb-0 text-success">{{ $summary['total_overtime_hours'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card box-shadow h-100">
                <div class="card-body">
                    <h6 class="text-muted mb-1">إجمالي ساعات النقص</h6>
                    <h3 class="mb-0 text-warning">{{ $summary['total_undertime_hours'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- ملخص حسب الفرع --}}
    @if($byBranch->count() > 1)
    <div class="card box-shadow mb-3">
        <div class="card-body">
            <h5 class="mb-3">الالتزام حسب الفرع</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>الفرع</th>
                            <th class="text-center">عدد الموظفين</th>
                            <th class="text-center">أيام الحضور</th>
                            <th class="text-center">أيام الغياب</th>
                            <th class="text-center">معدل الالتزام</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($byBranch as $row)
                            <tr>
                                <td>{{ $row->branch }}</td>
                                <td class="text-center">{{ $row->employees }}</td>
                                <td class="text-center">{{ $row->present_days }}</td>
                                <td class="text-center text-danger">{{ $row->absent_days }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $row->compliance_rate >= 90 ? 'bg-success' : ($row->compliance_rate >= 75 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ $row->compliance_rate }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Top lists --}}
    <div class="row mb-3">
        <div class="col-md-6 mb-3">
            <div class="card box-shadow h-100">
                <div class="card-body">
                    <h5 class="mb-3 text-danger">أعلى ٥ في الغياب</h5>
                    @if($topAbsent->isEmpty())
                        <p class="text-muted">لا توجد بيانات</p>
                    @else
                        <ol class="ps-3 mb-0">
                            @foreach($topAbsent as $emp)
                                <li class="mb-2">
                                    <strong>{{ $emp->name }}</strong>
                                    <small class="text-muted">({{ $emp->branch ?: '—' }})</small>
                                    — {{ $emp->absent_days }} يوم غياب
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card box-shadow h-100">
                <div class="card-body">
                    <h5 class="mb-3 text-success">أعلى ٥ في الالتزام</h5>
                    @if($topCompliant->isEmpty())
                        <p class="text-muted">لا توجد بيانات</p>
                    @else
                        <ol class="ps-3 mb-0">
                            @foreach($topCompliant as $emp)
                                <li class="mb-2">
                                    <strong>{{ $emp->name }}</strong>
                                    <small class="text-muted">({{ $emp->branch ?: '—' }})</small>
                                    — {{ $emp->compliance_rate }}%
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Detailed employee table --}}
    <div class="card box-shadow">
        <div class="card-body">
            <h5 class="mb-3">تفاصيل الموظفين</h5>
            <div class="table-responsive">
                <table id="dataTableExample" class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الموظف</th>
                            <th>الفرع</th>
                            <th class="text-center">حضور</th>
                            <th class="text-center">غياب</th>
                            <th class="text-center">انصراف ناقص</th>
                            <th class="text-center">إضافي (د)</th>
                            <th class="text-center">نقص (د)</th>
                            <th class="text-center">الالتزام %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $emp)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $emp->name }}</td>
                                <td>{{ $emp->branch ?: '—' }}</td>
                                <td class="text-center">{{ $emp->present_days }}</td>
                                <td class="text-center text-danger">{{ $emp->absent_days }}</td>
                                <td class="text-center">{{ $emp->missing_checkouts }}</td>
                                <td class="text-center">{{ $emp->overtime_minutes }}</td>
                                <td class="text-center">{{ $emp->undertime_minutes }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $emp->compliance_rate >= 90 ? 'bg-success' : ($emp->compliance_rate >= 75 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ $emp->compliance_rate }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center">لا توجد بيانات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</section>
@endsection
