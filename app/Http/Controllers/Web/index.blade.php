@extends('layouts.master')
@section('title', __('index.kpi_management'))

@section('main-content')
    <section class="content">
        @include('admin.section.flash_message')
        <div class="row">
            <div class="col-md-12">
                <div class="card glass-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0 fw-bold">🌟 {{ __('index.kpi_evaluations') }}</h4>
                        <a href="{{ route('admin.kpi.create') }}" class="btn btn-primary rounded-pill px-4">
                            <i class="link-icon me-2" data-feather="plus"></i> {{ __('index.add_kpi') }}
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table custom-table mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('index.employee') }}</th>
                                        <th>{{ __('index.month') }}/{{ __('index.year') }}</th>
                                        <th>{{ __('index.attendance_score') }}</th>
                                        <th>{{ __('index.task_score') }}</th>
                                        <th>{{ __('index.direct_manager_score') }}</th>
                                        <th>{{ __('index.total_score') }}</th>
                                        <th class="text-center">{{ __('index.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($evaluations as $kpi)
                                        <tr>
                                            <td class="fw-bold">{{ $kpi->user->name ?? 'غير معروف' }}</td>
                                            <td>{{ $kpi->month }} / {{ $kpi->year }}</td>
                                            <td>{{ $kpi->attendance_score }}%</td>
                                            <td>{{ $kpi->task_score }}%</td>
                                            <td>{{ $kpi->direct_manager_score }}%</td>
                                            <td>
                                                @php
                                                    $badgeClass = 'bg-primary';
                                                    if($kpi->total_score < 50) $badgeClass = 'bg-danger';
                                                    elseif($kpi->total_score < 75) $badgeClass = 'bg-warning';
                                                    elseif($kpi->total_score < 90) $badgeClass = 'bg-info';
                                                @endphp
                                                <span class="badge {{ $badgeClass }} fs-6">{{ $kpi->total_score }}%</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                        إجراء
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item text-primary" href="{{ route('admin.kpi.edit', $kpi->id) }}"><i data-feather="edit" class="me-2 icon-sm"></i> {{ __('index.edit') }}</a></li>
                                                        <li><a class="dropdown-item text-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')" href="{{ route('admin.kpi.delete', $kpi->id) }}"><i data-feather="trash-2" class="me-2 icon-sm"></i> {{ __('index.delete') }}</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7">
                                                <div class="empty-state-modern text-center py-4">
                                                    <i data-feather="star" style="width: 50px; height: 50px; color: #cbd5e1; margin-bottom: 15px;"></i>
                                                    <p class="text-muted fw-bold">{{ __('index.no_records_found') }}</p>
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
        </div>
    </section>
@endsection