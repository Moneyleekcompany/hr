@props(['status'])

@php
    $badgeClass = 'badge-soft-info';
    // تصنيف الحالات تلقائياً وإعطائها اللون المناسب
    if(in_array(strtolower($status), ['approved', 'completed', 'active', 'paid', 'present', 'مقبول', 'مكتمل', 'نشط', 'حاضر'])) {
        $badgeClass = 'badge-soft-success';
    } elseif(in_array(strtolower($status), ['rejected', 'cancelled', 'inactive', 'unpaid', 'absent', 'مرفوض', 'ملغى', 'غير نشط', 'غائب'])) {
        $badgeClass = 'badge-soft-danger';
    } elseif(in_array(strtolower($status), ['pending', 'on_hold', 'in_review', 'late', 'قيد الانتظار', 'متأخر'])) {
        $badgeClass = 'badge-soft-warning';
    }
@endphp

<span class="badge {{ $badgeClass }}">
    {{ ucfirst($status) }}
</span>