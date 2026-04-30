@extends('layouts.master')

@section('title', __('index.kanban_board'))

@section('styles')
<style>
    .kanban-column { min-height: 50vh; padding: 10px; background: #f3f4f6; border-radius: 8px; }
    .cursor-grab { cursor: grab; }
    .cursor-grab:active { cursor: grabbing; }
    .min-w-250px { min-width: 280px; }
</style>
@endsection

@section('main-content')
    <section class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"><i class="link-icon" data-feather="trello"></i> {{ __('index.kanban_board') }}</h4>
            <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary btn-sm"><i data-feather="plus"></i> {{ __('index.create_task') }}</a>
        </div>
        
        <div class="row flex-nowrap overflow-auto pb-4" style="min-height: 70vh;">
            @php
                $columns = [
                    'not_started' => ['title' => __('index.not_started'), 'color' => 'secondary'],
                    'in_progress' => ['title' => __('index.in_progress'), 'color' => 'primary'],
                    'in_review' => ['title' => __('index.in_review'), 'color' => 'warning text-dark'],
                    'completed' => ['title' => __('index.completed'), 'color' => 'success']
                ];
            @endphp

            @foreach($columns as $statusKey => $columnData)
            <div class="col-md-3 min-w-250px">
                <div class="card bg-light border-0">
                    <div class="card-header bg-{{ $columnData['color'] }} border-0 shadow-sm rounded-top">
                        <h6 class="mb-0 fw-bold">{{ $columnData['title'] }} <span class="badge bg-light text-dark float-end">{{ count($kanbanTasks[$statusKey]) }}</span></h6>
                    </div>
                    <div class="card-body p-2 kanban-column" data-status="{{ $statusKey }}">
                        @foreach($kanbanTasks[$statusKey] as $task)
                            <div class="card mb-3 shadow-sm cursor-grab border-0" data-task-id="{{ $task->id }}">
                                <div class="card-body p-3">
                                    <a href="{{ route('admin.tasks.show', $task->id) }}" class="text-dark fw-bold mb-1 d-block">{{ $task->name }}</a>
                                    <p class="text-muted small mb-2"><i data-feather="box" style="width:12px;height:12px;"></i> {{ $task->project->name ?? 'بدون مشروع' }}</p>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <span class="badge bg-{{ $task->priority == 'high' ? 'danger' : ($task->priority == 'medium' ? 'warning' : 'info') }}">{{ __('index.'.$task->priority) ?? $task->priority }}</span>
                                        <div class="members">
                                            @foreach($task->assignedMembers as $member)
                                                <img src="{{ $member->user->avatar ? asset(\App\Models\User::AVATAR_UPLOAD_PATH.$member->user->avatar) : asset('assets/images/default-avatar.png') }}" class="rounded-circle border" style="width:25px;height:25px;margin-left:-10px;object-fit:cover;" title="{{ $member->user->name }}">
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const columns = document.querySelectorAll('.kanban-column');
        columns.forEach(column => {
            new Sortable(column, {
                group: 'kanban',
                animation: 150,
                ghostClass: 'bg-white',
                onEnd: function (evt) {
                    const itemEl = evt.item;
                    const newStatus = evt.to.getAttribute('data-status');
                    const taskId = itemEl.getAttribute('data-task-id');
                    
                    // إرسال Ajax
                    $.ajax({
                        url: '{{ route("admin.tasks.update-status-kanban") }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            task_id: taskId,
                            status: newStatus
                        },
                        success: function(response) {
                            if(response.success) {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: response.message,
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            } else {
                                Swal.fire('تنبيه!', response.message, 'warning');
                                evt.from.appendChild(itemEl); // إرجاع الكارت لمكانه القديم في حال الرفض
                            }
                        },
                        error: function() {
                            Swal.fire('خطأ!', 'حدث خطأ في الاتصال بالسيرفر.', 'error');
                            evt.from.appendChild(itemEl); 
                        }
                    });
                },
            });
        });
    });
</script>
@endsection