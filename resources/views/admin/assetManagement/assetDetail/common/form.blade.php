<style>
    /* تحسينات النماذج (Modern Inputs) */
    .form-control, .form-select {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        color: #1e293b;
    }
    .form-control:focus, .form-select:focus {
        background-color: #ffffff;
        border-color: #8a0c51;
        box-shadow: 0 0 0 4px rgba(138, 12, 81, 0.15); /* توهج ناعم باللون الأساسي */
        transform: translateY(-1px);
    }
    .form-label { font-weight: 700; color: #475569; margin-bottom: 0.5rem; font-size: 0.9rem; }
</style>

<div class="row g-4 animate-fade-in">

    <div class="col-lg-4 col-md-6">
        <label for="name" class="form-label">{{ __('index.name') }}Name <span style="color: red">*</span></label>
        <input type="text" class="form-control"
               id="name"
               name="name"
               value="{{ (isset($assetDetail) ? $assetDetail->name: old('name') )}}"
               required autocomplete="off"
               placeholder="{{ __('index.enter_name') }}">
    </div>

    <div class="col-lg-4 col-md-6">
        <label for="type" class="form-label">{{ __('index.type') }} <span style="color: red">*</span></label>
        <select class="form-select" id="type" name="type_id" required>
            <option value="" {{isset($assetDetail) ? '': 'selected'}}  disabled>{{ __('index.select_asset_type') }}</option>
            @foreach($assetType as $key =>  $value)
                <option value="{{$value->id}}" {{ isset($assetDetail) && ($assetDetail->type_id ) == $value->id || old('type_id') == $value->id ? 'selected': '' }}>
                    {{ucfirst($value->name)}}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-lg-4 col-md-6">
        <label for="assetCode" class="form-label">{{ __('index.asset_code') }}</label>
        <input type="text" class="form-control"
               id="assetCode"
               name="asset_code"
               value="{{ ( isset($assetDetail) ? $assetDetail->asset_code: old('asset_code') )}}"
               autocomplete="off"
               placeholder="{{ __('index.enter_asset_code') }}">
    </div>

    <div class="col-lg-4 col-md-6">
        <label for="assetCode" class="form-label">{{ __('index.asset_serial_number') }}</label>
        <input type="text" class="form-control"
               id="assetCode"
               name="asset_serial_no"
               value="{{ ( isset($assetDetail) ? $assetDetail->asset_serial_no: old('asset_serial_no') )}}"
               autocomplete="off"
               placeholder="{{ __('index.enter_asset_serial_number') }}">
    </div>


    <div class="col-lg-4 col-md-6">
        <label for="is_working" class="form-label">{{ __('index.is_working') }}</label>
        <select class="form-select" id="type" name="is_working" >
            <option value="" {{(isset($assetDetail) && $assetDetail->is_working)  ? '': 'selected'}} > {{ __('index.select_status') }}</option>
                @foreach(\App\Models\Asset::IS_WORKING as $value)
                    <option value="{{$value}}" {{ isset($assetDetail) && ($assetDetail->is_working ) == $value || old('is_working') == $value ?'selected': '' }}>
                        {{ucfirst($value)}}
                    </option>
                @endforeach
        </select>
    </div>

    <div class="col-lg-4 col-md-6">
        <label for="purchased_date" class="form-label">{{ __('index.purchased_date') }} <span style="color: red">*</span></label>
        <input type="date"
               class="form-control"
               id="purchased_date"
               name="purchased_date"
               value="{{ ( isset($assetDetail) ? ($assetDetail->purchased_date): old('purchased_date') )}}"
               required
               autocomplete="off" >
    </div>

    <div class="col-lg-4 col-md-6">
        <label for="warranty_available" class="form-label">{{ __('index.warranty_available') }} <span style="color: red">*</span></label>
        <select class="form-select" id="warranty_available" name="warranty_available" required>
            <option value="" {{(isset($assetDetail) && $assetDetail->warranty_available)  ? '': 'selected'}} >{{ __('index.select_warranty_availability') }} </option>
            @foreach(\App\Models\Asset::BOOLEAN_DATA as $key => $value)
                <option value="{{$key}}" {{ isset($assetDetail) && ($assetDetail->warranty_available ) == $key || !is_null(old('warranty_available')) && old('warranty_available') == $key ?'selected': '' }}>
                    {{ucfirst($value)}}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-lg-4 col-md-6">
        <label for="warranty_end_date" class="form-label">{{ __('index.warranty_end_date') }}</label>
        <input type="date"
               class="form-control"
               id="warranty_end_date"
               name="warranty_end_date"
               value="{{(isset($assetDetail) ? ($assetDetail->warranty_end_date): old('warranty_end_date') )}}"
               autocomplete="off"
        >
    </div>

    <div class="col-lg-4 col-md-6">
        <label for="is_available" class="form-label">{{ __('index.is_available_for_employee') }} <span style="color: red">*</span></label>
        <select class="form-select" id="is_available" name="is_available" required>
            <option value="" {{(isset($assetDetail) && $assetDetail->is_available)  ? '': 'selected'}} >{{ __('index.select_availability') }}  </option>
            @foreach(\App\Models\Asset::BOOLEAN_DATA as $key => $value)
                <option value="{{$key}}" {{ isset($assetDetail) && ($assetDetail->is_available ) == $key || !is_null(old('is_available')) && old('is_available') == $key ?'selected': '' }}>
                    {{ucfirst($value)}}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-lg-6 col-md-6">
        <label for="assigned_to" class="form-label">{{ __('index.assigned_to') }} </label>
        <select class="form-select" id="assigned_to" name="assigned_to" >
            <option value="" {{isset($assetDetail) || old('assigned_to') ? '': 'selected'}}  >{{ __('index.select_employee') }}</option>
            @foreach($employees as $key =>  $value)
                <option value="{{$value->id}}" {{ isset($assetDetail) && ($assetDetail->assigned_to ) == $value->id || old('assigned_to') == $value->id ? 'selected': old('assigned_to') }}>
                    {{ucfirst($value->name)}}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-lg-6 col-md-6">
        <label for="assigned_date" class="form-label">{{ __('index.assigned_date') }}</label>
        <input type="date"
               class="form-control"
               id="assigned_date"
               name="assigned_date"
               value="{{ ( isset($assetDetail) ? ($assetDetail->assigned_date): old('assigned_date') )}}"
               autocomplete="off"
        >
    </div>

    <div class="col-lg-6 col-md-6">
        <label for="image" class="form-label">{{ __('index.upload_image') }} <span style="color: red">*</span> </label>
        <input class="form-control"
               type="file"
               id="image"
               name="image"
               accept=".jpeg,.png,.jpg,.webp"
               value="{{ isset($assetDetail) ? $assetDetail->image : old('image') }}"
            {{isset($assetDetail) ? '': 'required'}}
        >
        <img class="mt-3 {{(isset($assetDetail) && $assetDetail->image) ? '': 'd-none'}}"
             id="image-preview"
             src="{{ (isset($assetDetail) && $assetDetail->image) ? asset(\App\Models\Asset::UPLOAD_PATH.$assetDetail->image) : ''}}"
             style="object-fit: contain"
             width="200"
             height="200"
        >
    </div>

    <div class="col-lg-6">
        <label for="note" class="form-label">{{ __('index.description') }}</label>
        <textarea class="form-control" name="note" id="tinymceExample" rows="2">{{ ( isset($assetDetail) ? $assetDetail->note: old('note') )}}</textarea>
    </div>

    @canany(['edit_assets','create_assets'])
        <div class="col-12 text-start mt-4">
            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="link-icon" data-feather="plus"></i>
                {{isset($assetDetail)? __('index.update'):__('index.create')}}
            </button>
        </div>
    @endcanany
</div>
