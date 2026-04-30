@extends('layouts.master')

@section('title', __('index.media_gallery'))

@section('main-content')
    <section class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"><i class="link-icon" data-feather="image"></i> {{ __('index.media_gallery') }}: <span class="text-primary">{{ $project->name }}</span></h4>
            <a href="{{ route('admin.projects.show', $project->id) }}" class="btn btn-secondary btn-sm">عودة للمشروع</a>
        </div>
        
        <div class="row">
            @forelse($mediaFiles as $media)
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body p-2 text-center bg-light">
                            @if(in_array(strtolower($media->attachment_extension), ['mp4', 'mov']))
                                <video width="100%" height="200" controls class="rounded">
                                    <source src="{{ asset('uploads/task/' . $media->attachment) }}" type="video/{{ strtolower($media->attachment_extension) }}">
                                </video>
                            @else
                                <a href="{{ asset('uploads/task/' . $media->attachment) }}" target="_blank">
                                    <img src="{{ asset('uploads/task/' . $media->attachment) }}" class="img-fluid rounded" style="height: 200px; object-fit: cover; width: 100%;">
                                </a>
                            @endif
                        </div>
                        <div class="card-footer bg-white p-2">
                            <p class="text-muted small mb-0 text-truncate" title="المهمة: {{ $media->task->name ?? '' }}">
                                <i data-feather="check-circle" style="width: 14px; height: 14px;"></i> {{ $media->task->name ?? 'مهمة غير معروفة' }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 mt-5">
                    <i data-feather="camera-off" style="width: 60px; height: 60px; color:#ccc; margin-bottom: 15px;"></i>
                    <h5 class="text-muted">لا توجد أي صور أو فيديوهات مرفوعة في مهام هذا المشروع حتى الآن.</h5>
                </div>
            @endforelse
        </div>
    </section>
@endsection