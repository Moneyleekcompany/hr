@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mt-4">
                <div class="card-header bg-danger text-white">
                    <h4 class="card-title mb-0"><i class="fa fa-shield-alt"></i> السجل الأمني (Security Logs)</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">يعرض هذا السجل محاولات التلاعب الفاشلة في نظام الحضور والانصراف (مثل استخدام Fake GPS أو محاولة تزييف بصمة الوجه).</p>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الموظف</th>
                                    <th>نوع التلاعب</th>
                                    <th>التفاصيل</th>
                                    <th>عنوان الـ IP</th>
                                    <th>التاريخ والوقت</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="font-weight-bold text-danger">{{ $log->user->name ?? 'غير معروف' }}</td>
                                    <td><span class="badge badge-warning">{{ $log->type }}</span></td>
                                    <td>{{ $log->message }}</td>
                                    <td>{{ $log->ip_address }}</td>
                                    <td dir="ltr" class="text-right">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-success">لا يوجد أي محاولات تلاعب مسجلة حتى الآن. النظام آمن!</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection