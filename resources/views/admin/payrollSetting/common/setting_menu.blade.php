{{--<div class="card overflow-hidden">--}}
{{--<ul class=" nav payroll-sidebar-menu">--}}
{{--    <li class="nav-item {{request()->routeIs('admin.salary-components.*') ? 'bg-danger' : '' }} w-100" style="border-bottom: 1px solid #ede7e7;">--}}
{{--        <a class="nav-link {{request()->routeIs('admin.salary-components.*') ? 'text-white' : 'text-black' }}"--}}
{{--           href="{{ route('admin.salary-components.index')}}">--}}
{{--            Salary Component--}}
{{--        </a>--}}
{{--    </li>--}}
{{--    <li class="nav-item {{ request()->routeIs('admin.salary-groups.*')  ? 'bg-danger' : '' }} w-100" style="border-bottom: 1px solid #ede7e7;">--}}
{{--        <a class="nav-link {{ request()->routeIs('admin.salary-groups.*') ? 'text-white' : 'text-black' }}"--}}
{{--           href="{{ route('admin.salary-groups.index')}}">--}}
{{--            Salary Group--}}
{{--        </a>--}}
{{--    </li>--}}

{{--    <li class="nav-item {{request()->routeIs('admin.salary-tds.*')  ? 'bg-danger' : '' }} w-100" style="border-bottom: 1px solid #ede7e7;">--}}
{{--        <a class="nav-link {{request()->routeIs('admin.salary-tds.*') ? 'text-white' : 'text-black' }}"--}}
{{--           href="{{ route('admin.salary-tds.index')}}">--}}
{{--            Salary TDS--}}
{{--        </a>--}}
{{--    </li>--}}

{{--    <li class="nav-item {{request()->routeIs('admin.overtime.*')  ? 'bg-danger' : '' }} w-100" style="border-bottom: 1px solid #ede7e7;">--}}
{{--        <a class="nav-link {{request()->routeIs('admin.overtime.*') ? 'text-white' : 'text-black' }}"--}}
{{--           href="{{ route('admin.overtime.index')}}">--}}
{{--            OverTime--}}
{{--        </a>--}}
{{--    </li>--}}

{{--    <li class="nav-item {{request()->routeIs('admin.under-time.*')  ? 'bg-danger' : '' }} w-100" style="border-bottom: 1px solid #ede7e7;">--}}
{{--        <a class="nav-link {{request()->routeIs('admin.under-time.*') ? 'text-white' : 'text-black' }}"--}}
{{--           href="{{ route('admin.under-time.create')}}">--}}
{{--            UnderTime--}}
{{--        </a>--}}
{{--    </li>--}}

{{--    <li class="nav-item {{request()->routeIs('admin.payment-methods.*')  ? 'bg-danger' : '' }} w-100" style="border-bottom: 1px solid #ede7e7;">--}}
{{--        <a class="nav-link {{request()->routeIs('admin.payment-methods.*') ? 'text-white' : 'text-black' }}"--}}
{{--           href="{{ route('admin.payment-methods.index')}}">--}}
{{--            Payment Method--}}
{{--        </a>--}}
{{--    </li>--}}

{{--    <li class="nav-item {{request()->routeIs('admin.advance-salaries.setting')  ? 'bg-danger' : '' }} w-100">--}}
{{--        <a class="nav-link {{request()->routeIs('admin.advance-salaries.setting') ? 'text-white' : 'text-black' }}"--}}
{{--           href="{{ route('admin.advance-salaries.setting')}}">--}}
{{--            Advance Salary--}}
{{--        </a>--}}
{{--    </li>--}}

{{--</ul>--}}
{{--</div>--}}

<div class="card overflow-hidden position-sticky top-0 mb-4">
    <ul class="nav payroll-sidebar-menu">
        <li class="nav-item {{request()->routeIs('admin.salary-components.*') ? 'bg-danger' : '' }} w-100" style="border-bottom: 1px solid #ede7e7;">
            <a class="nav-link {{request()->routeIs('admin.salary-components.*') ? 'text-white' : 'text-black' }}"
               href="{{ route('admin.salary-components.index')}}">
                {{ __('index.salary_component') }}
            </a>
        </li>
        <li class="nav-item {{ request()->routeIs('admin.salary-groups.*')  ? 'bg-danger' : '' }} w-100" style="border-bottom: 1px solid #ede7e7;">
            <a class="nav-link {{ request()->routeIs('admin.salary-groups.*') ? 'text-white' : 'text-black' }}"
               href="{{ route('admin.salary-groups.index')}}">
                {{ __('index.salary_group') }}
            </a>
        </li>
        <li class="nav-item {{request()->routeIs('admin.ssf.*') ? 'bg-danger' : '' }} w-100" style="border-bottom: 1px solid #ede7e7;">
            <a class="nav-link {{request()->routeIs('admin.ssf.*') ? 'text-white' : 'text-black' }}"
               href="{{ route('admin.ssf.index')}}">
                SSF
            </a>
        </li>
        <li class="nav-item {{request()->routeIs('admin.bonus.*') ? 'bg-danger' : '' }} w-100" style="border-bottom: 1px solid #ede7e7;">
            <a class="nav-link {{request()->routeIs('admin.bonus.*') ? 'text-white' : 'text-black' }}"
               href="{{ route('admin.bonus.index')}}">
                Bonus
            </a>
        </li>
        <li class="nav-item {{request()->routeIs('admin.salary-tds.*')  ? 'bg-danger' : '' }} w-100" style="border-bottom: 1px solid #ede7e7;">
            <a class="nav-link {{request()->routeIs('admin.salary-tds.*') ? 'text-white' : 'text-black' }}"
               href="{{ route('admin.salary-tds.index')}}">
                {{ __('index.salary_tds') }}
            </a>
        </li>
        <li class="nav-item {{request()->routeIs('admin.advance-salaries.setting')  ? 'bg-danger' : '' }} w-100" style="border-bottom: 1px solid #ede7e7;">
            <a class="nav-link {{request()->routeIs('admin.advance-salaries.setting') ? 'text-white' : 'text-black' }}"
               href="{{ route('admin.advance-salaries.setting')}}">
                {{ __('index.advance_salary') }}
            </a>
        </li>
        <li class="nav-item {{request()->routeIs('admin.overtime.*')  ? 'bg-danger' : '' }} w-100" style="border-bottom: 1px solid #ede7e7;">
            <a class="nav-link {{request()->routeIs('admin.overtime.*') ? 'text-white' : 'text-black' }}"
               href="{{ route('admin.overtime.index')}}">
                {{ __('index.overtime') }}
            </a>
        </li>
        <li class="nav-item {{request()->routeIs('admin.under-time.*')  ? 'bg-danger' : '' }} w-100" style="border-bottom: 1px solid #ede7e7;">
            <a class="nav-link {{request()->routeIs('admin.under-time.*') ? 'text-white' : 'text-black' }}"
               href="{{ route('admin.under-time.create')}}">
                {{ __('index.undertime') }}
            </a>
        </li>
        <li class="nav-item {{request()->routeIs('admin.payment-methods.*')  ? 'bg-danger' : '' }} w-100" style="">
            <a class="nav-link {{request()->routeIs('admin.payment-methods.*') ? 'text-white' : 'text-black' }}"
               href="{{ route('admin.payment-methods.index')}}">
                {{ __('index.payment_method') }}
            </a>
        </li>



    </ul>
</div>
