@extends('layouts.master')

@section('title','Digital HR Dashboard')

<?php
    $attendanceDetail = (\App\Helpers\AppHelper::employeeTodayAttendanceDetail());

    $multipleEntries = count($attendanceDetail);
    $firstAttendance = $attendanceDetail->first();
    $lastAttendance = $attendanceDetail->last();

    $checkInAt = $firstAttendance['check_in_at'] ?? '';
    $checkOutAt = $lastAttendance['check_out_at'] ?? '';
    $attendanceDate = $lastAttendance['attendance_date'] ?? '';
    $viewCheckIn = $checkInAt ? \App\Helpers\AttendanceHelper::changeTimeFormatForAttendanceAdminView($appTimeSetting,$checkInAt) : '-:-:-';
    $viewCheckOut = $checkOutAt ? \App\Helpers\AttendanceHelper::changeTimeFormatForAttendanceAdminView($appTimeSetting, $checkOutAt) : '-:-:-';
?>

@section('nav-head',__('index.welcome').' : ' .ucfirst($dashboardDetail?->company_name) )

@section('styles')
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* توحيد المتغيرات اللونية العصرية */
        :root {
            --primary-color: #6366f1; /* نيلي عصري ومريح */
            --primary-soft: #e0e7ff;
            --bg-main: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            --hover-shadow: 0 10px 25px rgba(99, 102, 241, 0.1);
        }

        body, * { 
            font-family: 'Cairo', sans-serif !important; 
        }
        
        /* تغيير لون الخلفية العام ليكون أهدأ من الأبيض الصارخ */
        body {
            background-color: var(--bg-main) !important;
        }

        .alert {
            display: flex;
            align-items: center;
        }

        .scrolling-message {
            display: inline-block;
            white-space: nowrap;
            position: absolute;
            animation: scroll-left 10s linear infinite;
        }

        @keyframes scroll-left {
            0% {
                transform: translateX(100%);
            }
            100% {
                transform: translateX(-100%);
            }
        }
        
        /* تحسينات الجداول وحالات عدم وجود بيانات */
        .table-responsive { max-height: 400px; overflow-y: auto; }
        
        /* الجداول العائمة (Floating Tables) */
        .table.custom-table { border-collapse: separate; border-spacing: 0 8px; }
        .table.custom-table thead th { position: sticky; top: 0; background-color: transparent; border-bottom: none; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; z-index: 1; }
        .table.custom-table tbody tr { background-color: #ffffff; box-shadow: var(--card-shadow); transition: all 0.3s ease; border-radius: 12px; }
        .table.custom-table tbody tr:hover { transform: translateY(-3px); box-shadow: var(--hover-shadow); }
        .table.custom-table tbody td { border: none; padding: 1rem; vertical-align: middle; color: var(--text-main); font-weight: 600; }
        /* تدوير حواف الصفوف */
        .table.custom-table tbody td:first-child { border-top-right-radius: 12px; border-bottom-right-radius: 12px; }
        .table.custom-table tbody td:last-child { border-top-left-radius: 12px; border-bottom-left-radius: 12px; }
        
        /* Glassmorphism & Soft UI */
        .glass-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: var(--card-shadow) !important;
            transition: transform 0.2s ease-in-out;
        }
        .glass-card:hover { transform: translateY(-3px); box-shadow: var(--hover-shadow) !important; }

        /* تأثير الظهور الحركي (Fade-in) */
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeInUp 0.6s ease-out forwards; }
        /* تدرج في الظهور للعناصر */
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }

        /* الساعات الرقمية */
        .digital-clock { font-size: 3.5rem; font-weight: 800; color: #1e293b; letter-spacing: 2px; text-shadow: 2px 2px 4px rgba(0,0,0,0.05); }
        .digital-clock .colon { animation: pulse 1s infinite; color: #cbd5e1; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0; } }

        /* حالات الفراغ الذكية */
        .empty-state-modern {
            text-align: center; padding: 3rem; background: #f8fafc; border-radius: 20px; border: 1px dashed #cbd5e1;
        }
        .empty-state-modern svg { width: 100px; height: 100px; margin-bottom: 1.5rem; opacity: 0.7; color: #64748b; }
        
        .hr-card { background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border: 1px solid #e2e8f0; }
        .project-card { background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%); border: 1px solid #bae6fd; }
    </style>
@endsection

@section('main-content')

    <section class="content">
        <?php
            $projectPriority = [
                'low' => 'info',
                'medium' => 'warning',
                'high' => 'primary',
                'urgent' => 'primary'
            ];
        ?>

        <div id="loader" style="display:none;">
            <div class="loading">
                <div class="loading-content"></div>
            </div>
        </div>

        <div class="row animate-fade-in">
            <!-- القسم الأيمن: التبويبات والمحتوى -->
            <div class="col-xxl-9 col-xl-8">
                <ul class="nav nav-pills mb-4" id="dashboard-tabs" role="tablist">
                    @can('attendance_summary')
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold px-4 rounded-pill" id="hr-tab" data-bs-toggle="pill" data-bs-target="#hr-content" type="button" role="tab">نظرة عامة على الموارد البشرية</button>
                    </li>
                    @endcan
                    @canany(['project_detail','client_detail'])
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold px-4 rounded-pill @cannot('attendance_summary') active @endcannot" id="projects-tab" data-bs-toggle="pill" data-bs-target="#projects-content" type="button" role="tab">نظرة عامة على المشاريع</button>
                    </li>
                    @endcanany
                </ul>

                <div class="tab-content" id="dashboard-tabs-content">
            @can('attendance_summary')
            <div class="tab-pane fade show active" id="hr-content" role="tabpanel">
                <div class="row">
                    <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6 mb-4 d-flex animate-fade-in delay-1">
                        <div class="card hr-card glass-card w-100 rounded-4 shadow-sm">
                            <div class="card-body text-md-start text-center">
                                <div class="d-md-flex justify-content-between align-items-baseline mb-3">
                                    <h6 class="card-title mb-2 mb-md-0">{{ __('index.total_employees') }}</h6>
                                </div>

                                <div class="row align-items-center d-md-flex">
                                    <div class="col-lg-6 col-md-6">
                                        <h3>{{number_format($dashboardDetail?->total_employee)}}</h3>
                                    </div>
                                    <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                                        <i class="link-icon" data-feather="users"> </i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-3 col-xl-6 col-lg-6 col-md-6 mb-4 d-flex animate-fade-in delay-1">
                        <div class="card glass-card w-100">
                            <div class="card-body text-md-start text-center">
                                <div class="d-md-flex justify-content-between align-items-baseline mb-3">
                                    <h6 class="card-title mb-2 mb-md-0">{{ __('index.total_departments') }}</h6>
                                </div>
                                <div class="row align-items-center d-md-flex">
                                    <div class="col-lg-6 col-md-6">
                                        <h3>{{number_format($dashboardDetail?->total_departments)}}</h3>
                                    </div>
                                    <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                                        <i class="link-icon" data-feather="layers"> </i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 mb-4 d-flex animate-fade-in delay-2">
                        <div class="card glass-card w-100">
                            <div class="card-body text-md-start text-center">
                                <div class="d-md-flex justify-content-between align-items-baseline mb-3">
                                    <h6 class="card-title mb-2 mb-md-0">{{ __('index.total_holidays') }}</h6>
                                </div>
                                <div class="row align-items-center d-md-flex">
                                    <div class="col-lg-6 col-md-6">
                                        <h3>{{number_format($dashboardDetail?->total_holidays) ?? 0}}</h3>
                                    </div>
                                    <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                                        <i class="link-icon" data-feather="umbrella"> </i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 mb-4 d-flex animate-fade-in delay-2">
                        <div class="card glass-card w-100">
                            <div class="card-body text-md-start text-center">
                                <div class="d-md-flex justify-content-between align-items-baseline mb-3">
                                    <h6 class="card-title mb-2 mb-md-0">{{ __('index.paid_leaves') }}</h6>
                                </div>
                                <div class="row align-items-center d-md-flex">
                                    <div class="col-lg-6 col-md-6">
                                        <h3>{{number_format($dashboardDetail?->total_paid_leaves) ?? 0}}</h3>
                                    </div>
                                    <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                                        <i class="link-icon" data-feather="file-text"> </i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 mb-4 d-flex">
                        <div class="card glass-card w-100">
                            <div class="card-body text-md-start text-center">
                                <div class="d-md-flex justify-content-between align-items-baseline mb-3">
                                    <h6 class="card-title mb-2 mb-md-0">{{ __('index.on_leave_today') }}</h6>
                                </div>
                                <div class="row align-items-center d-md-flex">
                                    <div class="col-lg-6 col-md-6">
                                        <h3>{{number_format($dashboardDetail?->total_on_leave) ?? 0}}</h3>
                                    </div>
                                    <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                                        <i class="link-icon" data-feather="file-minus"> </i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 mb-4 d-flex">
                        <div class="card glass-card w-100">
                            <div class="card-body text-md-start text-center">
                                <div class="d-md-flex justify-content-between align-items-baseline mb-3">
                                    <h6 class="card-title mb-2 mb-md-0">{{ __('index.pending_leave_requests') }}</h6>
                                </div>
                                <div class="row align-items-center d-md-flex">
                                    <div class="col-lg-6 col-md-6">
                                        <h3>{{ number_format($dashboardDetail?->total_pending_leave_requests) ?? 0}}</h3>
                                    </div>
                                    <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                                        <i class="link-icon" data-feather="twitch"> </i>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 mb-4 d-flex">
                        <div class="card glass-card w-100">
                            <div class="card-body text-md-start text-center">
                                <div class="d-md-flex justify-content-between align-items-baseline mb-3">
                                    <h6 class="card-title mb-2 mb-md-0">{{ __('index.total_check_in_today') }}</h6>
                                </div>
                                <div class="row align-items-center d-md-flex">
                                    <div class="col-lg-6 col-md-6">
                                        <h3>{{number_format($dashboardDetail?->total_checked_in_employee) ?? 0 }}</h3>
                                    </div>
                                    <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                                        <i class="link-icon" data-feather="log-in"> </i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 mb-4 d-flex">
                        <div class="card glass-card w-100">
                            <div class="card-body text-md-start text-center">
                                <div class="d-md-flex justify-content-between align-items-baseline mb-3">
                                    <h6 class="card-title mb-2 mb-md-0">{{ __('index.total_check_out_today') }}</h6>
                                </div>
                                <div class="row align-items-center d-md-fle">
                                    <div class="col-lg-6 col-md-6">
                                        <h3>{{number_format($dashboardDetail?->total_checked_out_employee) ?? 0 }}</h3>
                                    </div>
                                    <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                                        <i class="link-icon" data-feather="log-out"> </i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            @endcan

            @canany(['project_detail','client_detail'])
            <div class="tab-pane fade @cannot('attendance_summary') show active @endcannot" id="projects-content" role="tabpanel">
                @can('project_detail')
                    <div class="row">
                        <div class="col-xxl-6 col-xl-6 d-flex mb-4 animate-fade-in delay-1">
                            <div class="card glass-card card-table flex-fill border-0 shadow-sm rounded-4">
                                <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4 d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-circle me-3 d-flex align-items-center justify-content-center">
                                        <i data-feather="bar-chart-2" style="width: 20px; height: 20px;"></i>
                                    </div>
                                    <h3 class="card-title mb-0 fw-bold">{{ __('index.projects_detail') }}</h3>
                                </div>
                                <div class="card-body p-4">
                                    <canvas id="projectChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-xxl-6 col-xl-6 d-flex animate-fade-in delay-2">
                            <div class="row">
                                <div class="col-xxl-6 col-xl-6 col-lg-4 col-md-4 mb-4">
                                    <div class="card glass-card project-card rounded-4 shadow-sm">
                                        <div class="card-body text-md-start text-center">
                                            <h6 class="card-title mb-2">{{ __('index.total_projects') }}</h6>
                                            <div class="row align-items-center d-md-flex">
                                                <div class="col-lg-6 col-md-6">
                                                    <h3>{{number_format($projectCardDetail['total_projects'])}}</h3>
                                                </div>
                                                <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                                                    <i class="link-icon" data-feather="layers"> </i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xxl-6 col-xl-6 col-lg-4 col-md-4 mb-4">
                                    <div class="card glass-card">
                                        <div class="card-body text-md-start text-center">
                                            <h6 class="card-title mb-2">{{ __('index.pending_projects') }}</h6>
                                            <div class="row align-items-center d-md-flex">
                                                <div class="col-lg-6 col-md-6">
                                                    <h3>{{number_format($projectCardDetail['not_started'])}}</h3>
                                                </div>
                                                <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                                                    <i class="link-icon" data-feather="layers"> </i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xxl-6 col-xl-6 col-lg-4 col-md-4 mb-4">
                                    <div class="card glass-card">
                                        <div class="card-body text-md-start text-center">
                                            <h6 class="card-title mb-2">{{ __('index.on_hold_projects') }}</h6>
                                            <div class="row align-items-center d-md-flex">
                                                <div class="col-lg-6 col-md-6">
                                                    <h3>{{number_format($projectCardDetail['on_hold'])}}</h3>
                                                </div>
                                                <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                                                    <i class="link-icon" data-feather="layers"> </i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xxl-6 col-xl-6 col-lg-4 col-md-4 mb-4">
                                    <div class="card glass-card">
                                        <div class="card-body text-md-start text-center">
                                            <h6 class="card-title mb-2">{{ __('index.in_progress_projects') }}</h6>
                                            <div class="row align-items-center d-md-flex">
                                                <div class="col-lg-6 col-md-6">
                                                    <h3>{{number_format($projectCardDetail['in_progress'])}}</h3>
                                                </div>
                                                <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                                                    <i class="link-icon" data-feather="layers"> </i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xxl-6 col-xl-6 col-lg-4 col-md-4 mb-4">
                                    <div class="card glass-card">
                                        <div class="card-body text-md-start text-center">
                                            <h6 class="card-title mb-2">{{ __('index.finished_projects') }}</h6>
                                            <div class="row align-items-center d-md-flex">
                                                <div class="col-lg-6 col-md-6">
                                                    <h3>{{number_format($projectCardDetail['completed'])}}</h3>
                                                </div>
                                                <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                                                    <i class="link-icon" data-feather="layers"> </i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xxl-6 col-xl-6 col-lg-4 col-md-4 mb-4">
                                    <div class="card glass-card">
                                        <div class="card-body text-md-start text-center">
                                            <h6 class="card-title mb-2">{{ __('index.cancelled_projects') }}</h6>
                                            <div class="row align-items-center d-md-flex">
                                                <div class="col-lg-6 col-md-6">
                                                    <h3>{{number_format($projectCardDetail['cancelled'])}}</h3>
                                                </div>
                                                <div class="col-lg-6 col-md-6 text-md-end dash-icon mt-md-0 mt-2">
                                                    <i class="link-icon" data-feather="layers"> </i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan

                <div class="row">
                @can('client_detail')
                    <div class="col-xxl-8 col-xl-8 mb-4 d-flex animate-fade-in delay-2">
                        <div class="card glass-card card-table flex-fill border-0 shadow-sm rounded-4">
                            <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-circle me-3 d-flex align-items-center justify-content-center">
                                        <i data-feather="star" style="width: 20px; height: 20px;"></i>
                                    </div>
                                    <h3 class="card-title mb-0 fw-bold">{{ __('index.top_clients') }}</h3>
                                </div>
                                <a href="{{route('admin.clients.index')}}" class="text-primary fw-bold small text-decoration-none">{{ __('index.view_all_clients') }}</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table custom-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>{{ __('index.name') }}</th>
                                                <th class="text-center">{{ __('index.email') }}</th>
                                                <th class="text-center">{{ __('index.contact') }}</th>
                                                <th class="text-center">{{ __('index.project') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($topClients as $key => $client)
                                                <tr>
                                                    <td class="table-avatar w-35">

                                                            <a href="{{route('admin.clients.show',$client->id)}}" class="avatar">
                                                                <img alt=""  src="{{asset(\App\Models\Client::UPLOAD_PATH.$client->avatar)}}">
                                                                <span class="ms-1">{{ucfirst($client->name)}}</span>
                                                            </a>

                                                    </td>
                                                    <td class="text-center">{{$client->email}}</td>
                                                    <td class="text-center">
                                                        {{$client->contact_no}}
                                                    </td>

                                                    <td class="text-center">
                                                        {{$client->project_count}}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="100%">
                                                        <div class="empty-state-modern">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                                            <p class="text-muted fw-bold">{{ __('index.no_records_found') }}</p>
                                                            <a href="{{ route('admin.clients.create') }}" class="btn btn-primary rounded-pill px-4 mt-2">إضافة عميل جديد</a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan

                @can('project_detail')
                    <div class="col-xxl-4 col-xl-4 mb-4 d-flex animate-fade-in delay-3">
                        <div class="card glass-card card-table flex-fill border-0 shadow-sm rounded-4">
                            <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4 d-flex align-items-center">
                                <div class="bg-info bg-opacity-10 text-info p-2 rounded-circle me-3 d-flex align-items-center justify-content-center">
                                    <i data-feather="pie-chart" style="width: 20px; height: 20px;"></i>
                                </div>
                                <h3 class="card-title mb-0 fw-bold">{{ __('index.task_details') }}</h3>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-center align-items-center p-4">
                                <div style="width: 100%; max-width: 260px; margin: auto;">
                                    <canvas id="tasksChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcan
                </div>

                @can('project_detail')
                    <div class="card glass-card card-table flex-fill border-0 shadow-sm rounded-4 mb-4 animate-fade-in delay-3">
                        <div class="card-header bg-transparent border-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 text-success p-2 rounded-circle me-3 d-flex align-items-center justify-content-center">
                                    <i data-feather="clock" style="width: 20px; height: 20px;"></i>
                                </div>
                                <h3 class="card-title mb-0 fw-bold">{{ __('index.recent_projects') }}</h3>
                            </div>
                            <a href="{{route('admin.projects.index')}}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">{{ __('index.view_all_projects') }}</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table custom-table mb-0">
                                    <thead>
                                    <tr>
                                        <th class="w-25">{{ __('index.title') }}</th>
                                        <th class="text-center">{{ __('index.date_start') }}</th>
                                        <th class="text-center">{{ __('index.deadline') }}</th>
                                        <th class="text-center">{{ __('index.leader') }}</th>
                                        <th class="text-center">{{ __('index.completion') }}</th>
                                        <th class="text-center">{{ __('index.priority') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($recentProjects as $key => $project)
                                        <tr>
                                            <td class="w-25">
                                                <a href="{{route('admin.projects.show',$project->id)}}" >{{ucfirst($project->name)}} </a>
                                            </td>
                                            <td class="text-center">{{\App\Helpers\AppHelper::formatDateForView($project->start_date)}}</td>
                                            <td class="text-center">
                                                {{\App\Helpers\AppHelper::formatDateForView($project->deadline)}}
                                            </td>

                                            <td class="member-listed text-center">
                                                @forelse($project->projectLeaders as $key => $leader)

                                                    <button type="button" class="p-0 border-0 bg-transparent ms-n3 " disabled data-toggle="tooltip" data-placement="top" title="{{ $leader->user ? ucfirst($leader->user->name) : 'Project Leader' }}">
                                                        <img class="rounded-circle" style="object-fit: cover"
                                                             src="{{ $leader->user ? asset(\App\Models\User::AVATAR_UPLOAD_PATH.$leader->user->avatar):
                                                                    asset('assets/images/img.png')
                                                        }}"
                                                             alt="profile">
                                                    </button>

                                                @empty

                                                @endforelse
                                            </td>
                                            <td class="text-center">
                                                <div class="progress">
                                                    <div class="progress-bar color2 rounded"
                                                         role="progressbar"
                                                         style="{{\App\Helpers\AppHelper::getProgressBarStyle($project->getProjectProgressInPercentage())}}"
                                                         aria-valuenow="25"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100" >
                                                        <span>{{($project->getProjectProgressInPercentage())}} %</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                    <span class="btn btn-{{$projectPriority[$project->priority]}} btn-xs cursor-default">
                                                            {{ucfirst($project->priority)}}
                                                    </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%">
                                                <div class="empty-state-modern">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                                    <p class="text-muted fw-bold">{{ __('index.no_records_found') }}</p>
                                                    <a href="{{ route('admin.projects.create') }}" class="btn btn-primary btn-sm rounded-pill mt-2">إنشاء مشروع جديد</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endcan
            </div>
            @endcan
                </div>
            </div>
            
            <!-- الساعة والحضور (تظهر للكل أو حسب الصلاحية كعمود جانبي) -->
            @can('allow_attendance')
                <div class="col-xxl-3 col-xl-4 mb-4 d-flex animate-fade-in delay-1">
                    <div class="card glass-card w-100 border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-body text-center clock-display p-4">
                            <!-- الساعة الرقمية الحديثة -->
                            <div class="digital-clock mb-2 mt-3" dir="ltr">
                                <span id="d-hour">00</span><span class="colon">:</span>
                                <span id="d-minute">00</span><span class="colon">:</span>
                                <span id="d-second" class="text-primary fs-4">00</span>
                                <span id="d-ampm" class="fs-6 text-muted"></span>
                            </div>

                            <div class="bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill d-inline-flex align-items-center fw-bolder mb-4 mt-2">
                                <i data-feather="calendar" class="me-2" style="width: 16px; height: 16px;"></i>
                                <span id="date">{{ \App\Helpers\AppHelper::getCurrentDate() }}</span>
                            </div>

                            <div class="punch-btn mb-4 d-flex justify-content-center gap-3">
                                @if($multipleAttendance > 1)
                                    @if($multipleEntries < $multipleAttendance || ($lastAttendance->check_in_at && !$lastAttendance->check_out_at))
                                        @if((!isset($firstAttendance->check_in_at) && !isset($firstAttendance->check_out_at)) || ($lastAttendance->check_in_at && $lastAttendance->check_out_at))
                                            <button href="{{route('admin.dashboard.takeAttendance','checkIn')}}" class="btn btn-success rounded-pill px-4 py-2 shadow-sm d-flex align-items-center fw-bold fs-6" id="startWorkingBtn" data-audio="{{asset('assets/audio/beep.mp3')}}">
                                                <i data-feather="log-in" class="me-1"></i> {{ __('index.punch_in') }}
                                            </button>
                                        @elseif(($firstAttendance->check_in_at && !$firstAttendance->check_out_at) || ($lastAttendance->check_in_at && !$lastAttendance->check_out_at))
                                            <button href="{{route('admin.dashboard.takeAttendance','checkOut')}}" class="btn btn-danger rounded-pill px-4 py-2 shadow-sm d-flex align-items-center fw-bold fs-6" id="stopWorkingBtn" data-audio="{{asset('assets/audio/beep.mp3')}}">
                                                <i data-feather="log-out" class="me-1"></i> {{ __('index.punch_out') }}
                                            </button>
                                        @endif
                                    @endif
                                @else
                                    <button href="{{route('admin.dashboard.takeAttendance','checkIn')}}" class="btn btn-success rounded-pill px-4 py-2 shadow-sm d-flex align-items-center fw-bold fs-6 {{ $checkInAt ? 'd-none' : ''}}" id="startWorkingBtn" data-audio="{{asset('assets/audio/beep.mp3')}}">
                                        <i data-feather="log-in" class="me-1"></i> {{ __('index.punch_in') }}
                                    </button>
                                    <button href="{{route('admin.dashboard.takeAttendance','checkOut')}}" class="btn btn-danger rounded-pill px-4 py-2 shadow-sm d-flex align-items-center fw-bold fs-6 {{ $checkOutAt ? 'd-none' : ''}}" id="stopWorkingBtn" data-audio="{{asset('assets/audio/beep.mp3')}}">
                                        <i data-feather="log-out" class="me-1"></i> {{ __('index.punch_out') }}
                                    </button>
                                @endif
                            </div>

                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="bg-success bg-opacity-10 border border-success border-opacity-25 rounded-4 p-3 h-100 d-flex flex-column justify-content-center align-items-center">
                                        <span class="text-muted small fw-bold mb-1">{{ __('index.check_in_at') }}</span>
                                        <span class="text-success fw-bolder fs-5 mb-0" id="checkInTime">{{$viewCheckIn}}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-4 p-3 h-100 d-flex flex-column justify-content-center align-items-center">
                                        <span class="text-muted small fw-bold mb-1">{{ __('index.check_out_at') }}</span>
                                        <span class="text-danger fw-bolder fs-5 mb-0" id="checkOutTime">{{$viewCheckOut}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan
            
        </div>

        <!-- Camera Modal (Selfie) -->
        <div class="modal fade" id="cameraModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">التقاط صورة الحضور (Selfie)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body text-center">
                <video id="cameraVideo" width="100%" autoplay playsinline style="border-radius:8px;"></video>
                <canvas id="cameraCanvas" style="display:none;"></canvas>
              </div>
              <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary" id="captureBtn">التقاط الصورة وتسجيل الحضور</button>
              </div>
            </div>
          </div>
        </div>
    </section>
@endsection

<script src="{{asset('assets/vendors/chartjs/Chart.min.js')}}"></script>

@section('scripts')
    <script>
        let translatedStrings = @json(__('index'));
    </script>
    @include('admin.dashboard_scripts')
@endsection
