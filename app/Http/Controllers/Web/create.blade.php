@extends('layouts.master')
@section('title', isset($kpi) ? __('index.edit_kpi') : __('index.add_kpi'))

@section('main-content')
    <section class="content">
        @include('admin.section.flash_message')
        <div class="card glass-card">
            <div class="card-header border-bottom">
                <h4 class="card-title fw-bold mb-0">{{ isset($kpi) ? __('index.edit_kpi') : __('index.add_kpi') }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.kpi.store') }}" method="POST">
                    @csrf
                    <div class="row g-4">
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('index.employee') }} <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-select select2" required>
                                <option value="" disabled selected>اختر الموظف</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ (isset($kpi) && $kpi->user_id == $employee->id) ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">{{ __('index.month') }} <span class="text-danger">*</span></label>
                            <select name="month" class="form-select" required>
                                @for($i = 1; $i <= 12; $i++)
                                    @php $m = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                    <option value="{{ $m }}" {{ (isset($kpi) && $kpi->month == $m) || (!isset($kpi) && date('m') == $m) ? 'selected' : '' }}>
                                        {{ $m }}
                                    </option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold">{{ __('index.year') }} <span class="text-danger">*</span></label>
                            <input type="number" name="year" class="form-control" value="{{ isset($kpi) ? $kpi->year : date('Y') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('index.attendance_score') }} (%) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" max="100" name="attendance_score" class="form-control" value="{{ $kpi->attendance_score ?? old('attendance_score') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('index.task_score') }} (%) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" max="100" name="task_score" class="form-control" value="{{ $kpi->task_score ?? old('task_score') }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('index.direct_manager_score') }} (%) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" max="100" name="direct_manager_score" class="form-control" value="{{ $kpi->direct_manager_score ?? old('direct_manager_score') }}" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">{{ __('index.feedback') }}</label>
                            <textarea name="feedback" rows="4" class="form-control">{{ $kpi->feedback ?? old('feedback') }}</textarea>
                            <small class="text-muted">نصائح وملاحظات لتطوير أداء الموظف ليراها في لوحة التحكم الخاصة به.</small>
                        </div>

                        <div class="col-12 mt-4 text-end">
                            <a href="{{ route('admin.kpi.index') }}" class="btn btn-secondary rounded-pill px-4 me-2">رجوع</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5">حفظ التقييم</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script>$('.select2').select2();</script>
@endsection