{{-- KPI Management --}}
@canany(['list_kpi','create_kpi'])
    <li class="nav-item {{ (request()->is('admin/kpi*')) ? 'active' : '' }}">
        <a href="{{route('admin.kpi.index')}}" class="nav-link" data-href="{{route('admin.kpi.index')}}">
            <i class="link-icon" data-feather="star"></i>
            <span class="link-title">@lang('index.kpi_management')</span>
        </a>
    </li>
@endcanany