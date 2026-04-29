<div class="row">
    <div class="col-lg-6 mb-3">
        <label class="form-label">اسم الجهاز <span class="text-danger">*</span></label>
        <input type="text" name="name" value="{{ isset($device) ? $device->name : old('name') }}" class="form-control" required placeholder="مثال: جهاز الفرع الأول">
    </div>

    <div class="col-lg-6 mb-3">
        <label class="form-label">الفرع التابع له</label>
        <select name="branch_id" class="form-select">
            <option value="">الفرع الرئيسي</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}" {{ (isset($device) && $device->branch_id == $branch->id) ? 'selected' : '' }}>{{ $branch->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-lg-6 mb-3">
        <label class="form-label">الـ IP الخاص بالجهاز <span class="text-danger">*</span></label>
        <input type="text" name="ip_address" value="{{ isset($device) ? $device->ip_address : old('ip_address') }}" class="form-control" required placeholder="192.168.1.201">
    </div>

    <div class="col-lg-6 mb-3">
        <label class="form-label">البورت (Port) <span class="text-danger">*</span></label>
        <input type="number" name="port" value="{{ isset($device) ? $device->port : (old('port') ?? 4370) }}" class="form-control" required>
    </div>

    <div class="col-lg-6 mb-3">
        <label class="form-label">الحالة</label>
        <select name="is_active" class="form-select" required>
            <option value="1" {{ (isset($device) && $device->is_active == 1) ? 'selected' : '' }}>نشط</option>
            <option value="0" {{ (isset($device) && $device->is_active == 0) ? 'selected' : '' }}>غير نشط</option>
        </select>
    </div>

    <div class="col-lg-12 mt-3">
        <button type="submit" class="btn btn-primary"><i class="link-icon" data-feather="save"></i> {{ isset($device) ? 'تحديث البيانات' : 'حفظ الجهاز' }}</button>
    </div>
</div>