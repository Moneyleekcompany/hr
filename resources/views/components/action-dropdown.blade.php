@props(['editUrl' => null, 'showUrl' => null, 'deleteUrl' => null])

<div class="dropdown">
    <button class="btn btn-sm btn-light dropdown-toggle border-0 shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-ellipsis-v"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 8px;">
        @if($showUrl)
            <li><a class="dropdown-item text-info py-2" href="{{ $showUrl }}"><i class="fa fa-eye me-2"></i> {{ __('index.view') }}</a></li>
        @endif
        @if($editUrl)
            <li><a class="dropdown-item text-primary py-2" href="{{ $editUrl }}"><i class="fa fa-edit me-2"></i> {{ __('index.edit') }}</a></li>
        @endif
        @if($deleteUrl)
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger py-2 delete-btn" href="#" data-url="{{ $deleteUrl }}"><i class="fa fa-trash me-2"></i> {{ __('index.delete') }}</a></li>
        @endif
    </ul>
</div>