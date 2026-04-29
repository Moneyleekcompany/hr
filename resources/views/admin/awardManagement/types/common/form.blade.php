<div class="row align-items-center">
    <div class="col-lg-6">
        <label for="name" class="form-label">{{ __('index.title') }}Title<span style="color: red">*</span></label>
        <input type="text" class="form-control" id="title"
               required
               name="title"
               value="{{ ( isset($awardTypeDetail) ? ($awardTypeDetail->title): old('title') )}}"
               autocomplete="off"
               placeholder=""
        >
    </div>

    @canany(['create_type','edit_type'])
        <div class="col-lg-6 mt-4">
            <button type="submit" class="btn btn-primary"><i class="link-icon" data-feather="{{isset($awardTypeDetail)? 'edit-2':'plus'}}"></i>
                {{isset($awardTypeDetail)? __('index.update'):__('index.create')}}
            </button>
        </div>
    @endcanany
</div>
