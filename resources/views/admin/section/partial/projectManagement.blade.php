@can('view_project_list')
    <li class="nav-item {{ request()->routeIs('admin.projects.*')  ? 'active' : '' }}">
        <a
            href="{{ route('admin.projects.index') }}"
            data-href="{{ route('admin.projects.index') }}"
            class="nav-link">
            <i class="link-icon" data-feather="box"></i>
            <span class="link-title">{{__('index.project_management')}}</span>
        </a>
    </li>
    <li class="nav-item {{ request()->routeIs('admin.tasks.kanban')  ? 'active' : '' }}">
        <a href="{{ route('admin.tasks.kanban') }}" class="nav-link">
            <i class="link-icon" data-feather="trello"></i>
            <span class="link-title">{{__('index.kanban_board')}}</span>
        </a>
</li>
@endcan
