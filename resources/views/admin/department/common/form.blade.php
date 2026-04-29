<div class="row">
    <div class="col-lg-4 col-md-6 mb-4">
        <label for="exampleFormControlSelect1" class="form-label">{{ __('index.company_name') }} <span style="color: red">*</span></label>
        <select class="form-select" id="exampleFormControlSelect1" name="company_id">
            <option selected value="{{ isset($companyDetail) ? $companyDetail->id : '' }}">{{ isset($companyDetail) ? $companyDetail->name : '' }}</option>
        </select>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <label for="exampleFormControlSelect1" class="form-label">{{ __('index.branch') }} <span style="color: red">*</span></label>
        <select class="form-select" id="exampleFormControlSelect1" name="branch_id" required>
            <option value="" {{ !isset($departmentsDetail) ? 'selected' : '' }} disabled>{{ __('index.select_branch') }}</option>
            @if($companyDetail)
                @foreach($companyDetail->branches()->get() as $key => $branch)
                    <option value="{{ $branch->id }}" @selected( old('branch_id', isset($departmentsDetail) && $departmentsDetail->branch_id ) == $branch->id)>{{ ucfirst($branch->name) }}</option>
                @endforeach
            @endif
        </select>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <label for="name" class="form-label">{{ __('index.department_name') }} <span style="color: red">*</span></label>
        <input type="text" class="form-control" id="dept_name" required name="dept_name" value="{{ isset($departmentsDetail) ? $departmentsDetail->dept_name : '' }}" autocomplete="off" placeholder="">
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <label for="exampleFormControlSelect1" class="form-label">{{ __('index.department_head') }}</label>
        <select class="form-select" id="exampleFormControlSelect1" name="dept_head_id">
            <option value="" {{ !isset($departmentsDetail) ? 'selected' : '' }} disabled>{{ __('index.select_department_head') }}</option>
            @foreach($users as $key => $user)
                <option value="{{ $user->id }}" @if(isset($departmentsDetail) && $departmentsDetail->dept_head_id ==$user->id) selected @endif >{{ ucfirst($user->name) }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <label for="address" class="form-label">{{ __('index.address') }} <span style="color: red">*</span></label>
        <input type="text" class="form-control" id="address" required name="address" value="{{ isset($departmentsDetail) ? $departmentsDetail->address : old('address') }}" autocomplete="off" placeholder="">
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <label for="number" class="form-label">{{ __('index.phone_number') }} <span style="color: red">*</span></label>
        <input type="number" class="form-control" id="phone" required name="phone" value="{{ isset($departmentsDetail) ? $departmentsDetail->phone : old('phone') }}" autocomplete="off" placeholder="">
    </div>

    <div class="col-lg-4 mb-4">
        <label for="exampleFormControlSelect1" class="form-label">{{ __('index.status') }}</label>
        <select class="form-select" id="exampleFormControlSelect1" name="is_active">
            <option value="" {{ !isset($departmentsDetail) ? 'selected' : '' }} disabled>{{ __('index.select_status') }}</option>
            <option value="1" {{ isset($departmentsDetail) && $departmentsDetail->is_active == 1 ? 'selected' : old('is_active') }}>{{ __('index.active') }}</option>
            <option value="0" {{ isset($departmentsDetail) && $departmentsDetail->is_active == 0 ? 'selected' : old('is_active') }}>{{ __('index.inactive') }}</option>
        </select>
    </div>

    <div class="col-lg-6 mb-4 mt-lg-4">
        <button type="submit" class="btn btn-primary"><i class="link-icon" data-feather="plus"></i> {{ isset($departmentsDetail) ? __('index.update_department') : __('index.create_department') }}</button>
    </div>
</div>
