

<div class="row align-items-center">


    <div class="col-lg-6 col-md-6 mb-4">
        <label for="name" class="form-label"> @lang('index.title') <span style="color: red">*</span></label>
        <input type="text" class="form-control" id="title" required name="title" value="{{ (old('title') || isset($qrData) ? $qrData->title : '' ) }}" autocomplete="off" placeholder="QR Title">
    </div>

    <div class="col-lg-6 col-md-6 mb-4 mt-md-4 text-start">
        <button type="submit" class="btn btn-primary"><i class="link-icon" data-feather="plus"></i> {{ isset($qrData) ? __('index.update') : __('index.create') }} @lang('index.qr')</button>
    </div>
</div>
