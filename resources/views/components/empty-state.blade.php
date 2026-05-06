@props([
    'icon' => 'ti-folder-off',
    'title' => null,
    'description' => null,
    'actionUrl' => null,
    'actionText' => null,
    'colspan' => null,
])

@php
    $titleText = $title ?? __('index.no_records_found');
    $btnText = $actionText ?? 'إضافة جديد';
@endphp

@if($colspan)
    <tr>
        <td colspan="{{ $colspan }}" class="border-0 p-0">
@endif

<div {{ $attributes->merge(['class' => 'mt-empty-state']) }}>
    <div class="mt-empty-state__icon">
        <i class="ti {{ $icon }}"></i>
    </div>
    <h5 class="mt-empty-state__title">{{ $titleText }}</h5>
    @if($description)
        <p class="mt-empty-state__desc">{{ $description }}</p>
    @endif
    @if($actionUrl)
        <a href="{{ $actionUrl }}" class="btn btn-primary mt-3">
            <i class="ti ti-plus"></i>
            {{ $btnText }}
        </a>
    @endif
</div>

@if($colspan)
        </td>
    </tr>
@endif
