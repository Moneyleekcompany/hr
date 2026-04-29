@extends('layouts.master')

@section('title', 'أجهزة البصمة')

@section('main-content')
    <section class="content">
        @include('admin.section.flash_message')
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">قائمة أجهزة البصمة</h4>
                <div class="d-flex">
                    <a href="{{ route('admin.zkteco.sync') }}" class="btn btn-sm btn-success me-2">
                        <i class="link-icon" data-feather="refresh-cw"></i> سحب البصمات الان
                    </a>
                    <a href="{{ route('admin.zkteco-devices.create') }}" class="btn btn-sm btn-primary">
                        <i class="link-icon" data-feather="plus"></i> إضافة جهاز جديد
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم الجهاز</th>
                                <th>الفرع</th>
                                <th>IP Address</th>
                                <th>البورت</th>
                                <th class="text-center">الحالة</th>
                                <th class="text-center">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($devices as $key => $device)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{{ $device->name }}</td>
                                    <td>{{ $device->branch ? $device->branch->name : 'الفرع الرئيسي' }}</td>
                                    <td>{{ $device->ip_address }}</td>
                                    <td>{{ $device->port }}</td>
                                    <td class="text-center">
                                        <label class="switch">
                                            <input class="toggleStatus" onchange="window.location.href='{{ route('admin.zkteco-devices.toggle-status', $device->id) }}'"
                                                   type="checkbox" {{ $device->is_active ? 'checked' : '' }}>
                                            <span class="slider round"></span>
                                        </label>
                                    </td>
                                    <td class="text-center">
                                        <ul class="d-flex list-unstyled mb-0 justify-content-center">
                                            <li class="me-2">
                                                <a href="{{ route('admin.zkteco-devices.edit', $device->id) }}">
                                                    <i class="link-icon" data-feather="edit"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('admin.zkteco-devices.delete', $device->id) }}" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                                    <i class="link-icon text-danger" data-feather="delete"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <h5 class="text-muted mb-3">لا توجد أجهزة بصمة مضافة حالياً</h5>
                                        <a href="{{ route('admin.zkteco-devices.create') }}" class="btn btn-primary">
                                            <i class="link-icon" data-feather="plus"></i> اضغط هنا لإضافة أول جهاز بصمة وربطه بفرع
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $devices->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection