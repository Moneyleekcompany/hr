<div class="row">
    <div class="col-lg-4 col-md-6 mb-4">
        <label for="title" class="form-label">@lang('index.task_name') <span style="color: red">*</span></label>
        <input type="text" class="form-control" id="name" name="name" required value="{{ ( isset($taskDetail) ?  $taskDetail->name: old('name') )}}"
               autocomplete="off" placeholder="@lang('index.enter_task_name')">
    </div>

    @if(isset($project))
        <div class="col-lg-4 col-md-6 mb-4">
            <label for="project" class="form-label">@lang('index.project') <span style="color: red">*</span> </label>
            <select class="form-select form-select"  name="project_id"  >
                <option value="{{$project->id}}" selected >{{ucfirst($project->name)}}</option>
            </select>
        </div>
    @else
        <div class="col-lg-4 col-md-6 mb-4">
            <label for="project" class="form-label">@lang('index.project') <span style="color: red">*</span> </label>
            <select class="form-select form-select" id="project" name="project_id"  >
                <option value="" {{isset($taskDetail) ? '' : 'selected'}}  disabled>@lang('index.select_project')</option>
                @foreach($projectLists as $key => $value)
                    <option value="{{$value->id}}" {{ (isset($taskDetail) && ($taskDetail->project_id) == $value->id) || ( old('project_id') == $value->id)? 'selected': '' }}>
                        {{$value->name}}
                    </option>
                @endforeach
            </select>
        </div>
    @endif

    <div class="col-lg-4 col-md-6 mb-4">
        <label for="start_date" class="form-label">@lang('index.task_start_date') <span style="color: red">*</span> </label>
        @if($isBsEnabled)
            <input type="text" class="form-control startNpDate" id="start_date" name="start_date" required value="{{ ( isset( $taskDetail) ?  \App\Helpers\AppHelper::taskDate($taskDetail->start_date): old('start_date') )}}"
                   autocomplete="off" >
        @else
            <input type="datetime-local" class="form-control" id="start_date" name="start_date" required value="{{ ( isset( $taskDetail) ?  $taskDetail->start_date: old('start_date') )}}"
                   autocomplete="off" >
        @endif

    </div>
    @if($isBsEnabled)
        <div class="col-lg-4 col-md-6 mb-3">
            <label for="start_time" class="form-label">@lang('index.task_start_time') <span style="color: red">*</span> </label>
            <input type="time" class="form-control" id="start_time" name="start_time" required value="{{ ( isset( $taskDetail) ? date('H:i:s', strtotime($taskDetail->start_date)) : '' )}}"
                   autocomplete="off" >
        </div>
    @endif
    <div class="col-lg-4 col-md-6 mb-4">
        <label for="end_date" class="form-label">@lang('index.task_end_date') <span style="color: red">*</span> </label>
        @if($isBsEnabled)
            <input type="text" class="form-control npDeadline" id="end_date" name="end_date" required value="{{ ( isset( $taskDetail) ?  \App\Helpers\AppHelper::taskDate($taskDetail->end_date): old('end_date') )}}"
                   autocomplete="off" >
        @else
            <input type="datetime-local" class="form-control" id="end_date" name="end_date" required value="{{ ( isset( $taskDetail) ?  $taskDetail->end_date: old('end_date') )}}"
                   autocomplete="off" >
        @endif


    </div>
    @if($isBsEnabled)
        <div class="col-lg-4 col-md-6 mb-3">
            <label for="end_time" class="form-label">@lang('index.task_end_time') <span style="color: red">*</span> </label>
            <input type="time" class="form-control" id="end_time" name="end_time" required value="{{ ( isset( $taskDetail) ? date('H:i:s', strtotime($taskDetail->end_date)) : '' )}}"
                   autocomplete="off" >
        </div>
    @endif
    <div class="col-lg-4 col-md-6 mb-4">
        <label for="priority" class="form-label">@lang('index.priority')</label>
        <select class="form-select" id="priority" name="priority"  >
            <option value="" {{isset($taskDetail) ? '' : 'selected'}}  disabled>@lang('index.select_priority')</option>
            @foreach(\App\Models\Task::PRIORITY as $value)
                <option value="{{$value}}" {{ (isset($taskDetail) && ($taskDetail->priority ) == $value) || ( old('priority') == $value) ? 'selected': '' }}>
                    {{ucfirst($value)}}</option>
            @endforeach
        </select>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <label for="status" class="form-label">@lang('index.task_status') </label>
        <select class="form-select" id="status" name="status">
            <option value="" disabled {{isset($taskDetail) ? '' : 'selected'}}>@lang('index.select_task_status')</option>
            <option value="not_started" {{ (isset($taskDetail) && $taskDetail->status == 'not_started') || old('status') == 'not_started' ? 'selected' : '' }}>@lang('index.not_started')</option>
            <option value="in_progress" {{ (isset($taskDetail) && $taskDetail->status == 'in_progress') || old('status') == 'in_progress' ? 'selected' : '' }}>@lang('index.in_progress')</option>
            <option value="in_review" {{ (isset($taskDetail) && $taskDetail->status == 'in_review') || old('status') == 'in_review' ? 'selected' : '' }}>@lang('index.in_review')</option>
            <option value="completed" {{ (isset($taskDetail) && $taskDetail->status == 'completed') || old('status') == 'completed' ? 'selected' : '' }}>@lang('index.completed')</option>
            <option value="on_hold" {{ (isset($taskDetail) && $taskDetail->status == 'on_hold') || old('status') == 'on_hold' ? 'selected' : '' }}>@lang('index.on_hold')</option>
            <option value="cancelled" {{ (isset($taskDetail) && $taskDetail->status == 'cancelled') || old('status') == 'cancelled' ? 'selected' : '' }}>@lang('index.cancelled')</option>
        </select>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="form-check form-switch mt-4 pt-2">
            <input class="form-check-input" type="checkbox" id="is_recurring" name="is_recurring" value="1" {{ (isset($taskDetail) && $taskDetail->is_recurring) ? 'checked' : '' }}>
            <label class="form-check-label fw-bold" for="is_recurring">@lang('index.is_recurring')</label>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4 recurring_options" style="display: {{ (isset($taskDetail) && $taskDetail->is_recurring) ? 'block' : 'none' }};">
        <label class="form-label">@lang('index.recurring_frequency')</label>
        <select class="form-select" name="recurring_frequency">
            <option value="daily" {{ (isset($taskDetail) && $taskDetail->recurring_frequency == 'daily') ? 'selected' : '' }}>يومياً</option>
            <option value="weekly" {{ (isset($taskDetail) && $taskDetail->recurring_frequency == 'weekly') ? 'selected' : '' }}>أسبوعياً</option>
            <option value="monthly" {{ (isset($taskDetail) && $taskDetail->recurring_frequency == 'monthly') ? 'selected' : '' }}>شهرياً</option>
        </select>
    </div>

    @if(isset($projectMember))
        <div class=" col-lg-12 mb-3">
            <label for="employee" class="form-label">@lang('index.assign_member') <span style="color: red">*</span></label>
            <br>
            <select class="form-select" id="taskMember" name="assigned_member[]" multiple="multiple" required>
                @if(isset($projectMember))
                    @foreach($projectMember as $key => $datum)
                        <option value="{{$datum->user->id}}"  >{{ucfirst($datum->user->name)}}</option>
                    @endforeach
                @endif
            </select>
        </div>
    @else
        <div class=" col-lg-12 mb-3 taskMemberAssignDiv">
            <label for="employee" class="form-label">@lang('index.assign_member') <span style="color: red">*</span></label>
            <br>
            <select class="form-select" id="taskMember" name="assigned_member[]" multiple="multiple" required>
                @if(isset($taskDetail))
                    @foreach($taskDetail->project->assignedMembers as $key => $value)
                        <option value="{{$value->user->id}}" {{ isset($taskDetail) && in_array($value->user->id,$memberId)  ? 'selected' : '' }}  >{{ucfirst($value->user->name)}}</option>
                    @endforeach
                @endif
            </select>
        </div>
    @endif

    <div class="col-lg-12 mb-4">
        <label for="description" class="form-label">@lang('index.description')<span style="color: red">*</span></label>
        <textarea class="form-control" name="description" id="tinymceExample" rows="4">{{ ( isset($taskDetail) ? $taskDetail->description: old('description') )}}</textarea>
    </div>

    @if(isset($taskDetail))
        <div class="col-lg-12 mb-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="">@lang('index.uploaded_files_and_images')</h6>
                </div>
                <div class="card-body">
                    @if(count($files) < 1 && count($images) < 1)
                        <div class="row">
                            <p class="text-muted">@lang('index.no_project_file_uploaded')</p>
                        </div>
                    @endif
                    <div class="row mb-4">
                        @forelse($images as $key => $imageData)
                            <div class="col-lg-3">
                                <div class="uploaded-image">
                                    <img class="w-100" style=""
                                         src="{{ asset(\App\Models\Attachment::UPLOAD_PATH.$imageData->attachment) }}"
                                         alt="document images">
                                    <a class="documentDelete" data-href="{{route('admin.attachment.delete',$imageData->id)}}">
                                        <i class="link-icon remove-image" data-feather="x"></i>
                                    </a>
                                </div>
                            </div>
                        @empty

                        @endforelse
                    </div>
                        <div class="row">
                            @forelse($files as $key => $fileData)
                                <div class="uploaded-files">
                                    <div class="row align-items-center">
                                        <div class="col-lg-1">
                                            <div class="file-icon">
                                                <i class="link-icon" data-feather="file-text"></i>
                                            </div>
                                        </div>
                                        <div class="col-lg-10">
                                            <a target="_blank" href="{{asset(\App\Models\Attachment::UPLOAD_PATH.$fileData->attachment)}}">
                                                {{asset(\App\Models\Attachment::UPLOAD_PATH.$fileData->attachment)}}
                                            </a>
                                        </div>

                                        <div class="col-lg-1">
                                            <a class="documentDelete" data-href="{{route('admin.attachment.delete',$fileData->id)}}">
                                                <i class="link-icon remove-files" data-feather="x"></i>
                                            </a>
                                        </div>

                                    </div>

                                </div>
                            @empty

                            @endforelse
                        </div>
                </div>
            </div>
        </div>
    @endif

    <div class="col-lg-12 mb-4">
        <h6 class="mb-2">@lang('index.task_attachments')</h6>
        <div>
            <input id="image-uploadify" type="file"  name="attachments[]"
                   accept=".pdf,.jpg,.jpeg,.png,.docx,.doc,.xls,.txt,.zip"  multiple />
        </div>
    </div>
    <input type="hidden" readonly id="taskNotification" name="notification" value="0">

    <div class="col-12">
        <button type="submit" class="btn btn-primary me-2"><i class="link-icon" data-feather="{{isset($taskDetail)? 'edit-2':'plus'}}"></i> {{isset($taskDetail)? __('index.update_task') : __('index.create_task')}}</button>

        <button type="submit" id="withTaskNotification" class="btn btn-primary">
            <i class="link-icon" data-feather="{{isset($taskDetail)? 'edit-2':'plus'}}"></i>
            {{isset($taskDetail)?  __('index.update_send'): __('index.create_send')}}
        </button>
    </div>
</div>
