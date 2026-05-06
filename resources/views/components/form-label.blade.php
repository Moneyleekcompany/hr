@props([
    'for' => null,
    'required' => false,
    'optional' => false,
])

<label @if($for) for="{{ $for }}" @endif {{ $attributes->merge(['class' => 'form-label']) }}>
    {{ $slot }}
    @if($required)
        <span class="text-danger ms-1" aria-hidden="true">*</span>
        <span class="visually-hidden">إلزامي</span>
    @endif
    @if($optional)
        <small class="text-muted ms-1">(اختياري)</small>
    @endif
</label>
