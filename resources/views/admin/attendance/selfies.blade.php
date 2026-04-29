@extends('layouts.master')

@section('title', 'معرض صور الحضور (Selfies)')

@section('main-content')
<section class="content">
    <div class="card mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0"><i class="link-icon" data-feather="camera"></i> مراجعة صور الحضور اليومية (Selfie)</h4>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.attendance.selfies') }}" class="row align-items-center mb-4">
                <div class="col-md-4">
                    <label class="form-label font-weight-bold">اختر التاريخ المُراد مراجعته:</label>
                    <input type="date" name="attendance_date" value="{{ $date }}" class="form-control" onchange="this.form.submit()">
                </div>
            </form>

            <div class="row">
                @forelse($attendances as $attendance)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100 border shadow-sm">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0 text-center text-primary font-weight-bold">{{ $attendance->employee->name ?? 'غير معروف' }}</h6>
                            </div>
                            <div class="card-body p-3 text-center">
                                @if($attendance->check_in_image)
                                    <div class="mb-3">
                                        <span class="badge bg-success mb-2">حضور ({{ \Carbon\Carbon::parse($attendance->check_in_at)->format('h:i A') }})</span>
                                        <a href="{{ asset('uploads/attendance/'.$attendance->check_in_image) }}" target="_blank">
                                            <img src="{{ asset('uploads/attendance/'.$attendance->check_in_image) }}" class="img-fluid rounded border" style="height: 180px; width:100%; object-fit: cover;" alt="Check In">
                                        </a>
                                    </div>
                                @endif
                                
                                @if($attendance->check_out_image)
                                    <div class="{{ $attendance->check_in_image ? 'mt-3 border-top pt-3' : '' }}">
                                        <span class="badge bg-danger mb-2">انصراف ({{ \Carbon\Carbon::parse($attendance->check_out_at)->format('h:i A') }})</span>
                                        <a href="{{ asset('uploads/attendance/'.$attendance->check_out_image) }}" target="_blank">
                                            <img src="{{ asset('uploads/attendance/'.$attendance->check_out_image) }}" class="img-fluid rounded border" style="height: 180px; width:100%; object-fit: cover;" alt="Check Out">
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i data-feather="image" style="width: 50px; height: 50px; color:#ccc; margin-bottom: 10px;"></i>
                        <h5 class="text-muted">لا توجد صور حضور (Selfie) مسجلة في هذا التاريخ.</h5>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>
@endsection