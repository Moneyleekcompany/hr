@foreach($permissionGroupTypeList as $key => $permissionGroupType)
    <div class="mb-4 ">
        <h5 class="btn btn-dark"
            id="{{$permissionGroupType->slug}}">{{$permissionGroupType->name}} @lang('index.permissions')</h5>
    </div>
    <div class="row mb-2 {{$permissionGroupType->slug}}">
        @foreach($permissionGroupType->permissionGroups as $key=>$value)

            @php
                $collectionArray = $value->getPermission->pluck('id')->toArray();

                $checkAll = '';

                if(count($role_permission) > 0){
                    $diff = array_diff($collectionArray, $role_permission);

                     if (empty($diff)) {
                       $checkAll = 'checked';
                     }
                }

            @endphp
            <div class="col-lg-3 col-md-6 mb-4 pb-4">
                <div class="group-checkbox">
                    <div class="head-checkbox">
                        <div class="title-ch mb-3">
                            <h5>{{$value->name}} @lang('index.module'):</h5>
                        </div>

                        <div class="checkAll mb-2">
                            <label class="label-ch">
                                <input class="js-check-all" type="checkbox" name=""
                                       data-check-all="website" {{ $checkAll }}>
                                <span class="text">@lang('index.check_all')</span>
                            </label>
                        </div>
                    </div>
                    <ul class="js-check-all-target list-ch" data-check-all="website">
                        @foreach($value->getPermission as $keys => $permission)
                            @php
                                $checked='';
                                if(count($role_permission) > 0){
                                    if(in_array($permission->id,$role_permission)){
                                        $checked = "checked = 'checked'";
                                    }
                                }
                            @endphp
                            <li class="item">
                                <label class="label">
                                    <input class="module_checkbox"
                                           type="checkbox"
                                           id="{{$permission->permission_key}}"
                                           name="permission_value[]"
                                           value="{{$permission->id}}"
                                        {{$checked}}>
                                    <span class="text">{{$permission->name}}
                                    </span>
                                    @if($permission->permission_key == 'access_admin_leave')
                                        <p class="grant_leave">
                                            {{ __('index.admin_permission_msg') }}

                                        </p>
                                    @endif
                                </label>

                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endforeach
    </div>
@endforeach


<div class="text-start">
    <button type="submit" class="btn btn-success btn-md">
        {{$isEdit ? __('index.update'): __('index.save') }}
    </button>
</div>

