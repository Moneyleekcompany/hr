@canany(['list_leave_type','list_leave_request','access_admin_leave','list_leave_approval','time_leave_list'])
    <li class="nav-item {{ request()->routeIs('admin.leaves.*') ||
                            request()->routeIs('admin.time-leave-request.*') ||
                            request()->routeIs('admin.leave-approval.*') ||
                        request()->routeIs('admin.leave-request.*') ? 'active' : '' }} ">
        <a class="nav-link" href="{{route('admin.leaves.index')}}" aria-controls="leaves">
            <i class="link-icon" data-feather="bookmark"></i>
            <span class="link-title">{{ __('index.leave') }}</span>
        </a>
    </li>
@endcanany
