@props([
    'showUrl' => null,
    'editUrl' => null,
    'deleteUrl' => null,
    'deleteMethod' => 'POST',
    'deleteConfirm' => null,
    'extraItems' => [],
])

<div class="dropdown mt-action-menu">
    <button class="btn btn-sm mt-action-menu__toggle"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            aria-label="الإجراءات">
        <i class="ti ti-dots-vertical"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end mt-action-menu__list">
        @if($showUrl)
            <li>
                <a class="dropdown-item" href="{{ $showUrl }}">
                    <i class="ti ti-eye text-info"></i>
                    <span>{{ __('index.view') }}</span>
                </a>
            </li>
        @endif

        @if($editUrl)
            <li>
                <a class="dropdown-item" href="{{ $editUrl }}">
                    <i class="ti ti-edit text-primary"></i>
                    <span>{{ __('index.edit') }}</span>
                </a>
            </li>
        @endif

        @foreach($extraItems as $item)
            <li>
                <a class="dropdown-item"
                   href="{{ $item['url'] ?? '#' }}"
                   @if(!empty($item['confirm'])) data-confirm="{{ $item['confirm'] }}" @endif
                   @if(!empty($item['method'])) data-method="{{ $item['method'] }}" @endif>
                    @if(!empty($item['icon']))
                        <i class="ti {{ $item['icon'] }} {{ $item['icon_class'] ?? '' }}"></i>
                    @endif
                    <span>{{ $item['label'] }}</span>
                </a>
            </li>
        @endforeach

        @if($deleteUrl)
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item text-danger"
                   href="{{ $deleteUrl }}"
                   data-confirm="{{ $deleteConfirm ?? 'هل أنت متأكد من الحذف؟ لا يمكن التراجع عن هذا الإجراء.' }}"
                   data-confirm-variant="danger"
                   data-confirm-label="{{ __('index.delete') }}"
                   @if(strtoupper($deleteMethod) !== 'GET') data-method="{{ strtoupper($deleteMethod) }}" @endif>
                    <i class="ti ti-trash"></i>
                    <span>{{ __('index.delete') }}</span>
                </a>
            </li>
        @endif
    </ul>
</div>
