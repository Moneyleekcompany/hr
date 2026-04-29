@can('view_payroll_list')
<li class="nav-item  {{
                   request()->routeIs('admin.salary-components.*') ||
                   request()->routeIs('admin.payment-methods.*') ||
                   request()->routeIs('admin.bonus.*') ||
                   request()->routeIs('admin.salary-tds.*') ||
                   request()->routeIs('admin.salary-groups.*') ||
                   request()->routeIs('admin.advance-salaries.*') ||
                   request()->routeIs('admin.overtime.*')||
                   request()->routeIs('admin.ssf.*')||
                   request()->routeIs('admin.under-time.*')||
                   request()->routeIs('admin.payroll.tax-report.*')||
                   request()->routeIs('admin.employee-salaries.*')

                ? 'active' : ''
            }}"
    >
    <a class="nav-link" data-bs-toggle="collapse"
       href="#payroll"
       data-href="#"
       role="button" aria-expanded="false" aria-controls="settings">
        <i class="link-icon" data-feather="gift"></i>
        <span class="link-title"> {{ __('index.payroll_management') }} </span>
        <i class="link-arrow" data-feather="chevron-down"></i>
    </a>
    <div class="{{
                request()->routeIs('admin.bonus.*') ||
                request()->routeIs('admin.salary-groups.*') ||
                request()->routeIs('admin.salary-tds.*')  ||
                request()->routeIs('admin.advance-salaries.*') ||
                request()->routeIs('admin.employee-salaries.*')||
                request()->routeIs('admin.overtime.*')||
                request()->routeIs('admin.under-time.*')||
                request()->routeIs('admin.ssf.*')||
                request()->routeIs('admin.payroll.tax-report.*')||
                request()->routeIs('admin.employee-salary.payroll*')


               ? '' : 'collapse'  }} " id="payroll">

        <ul class="nav sub-menu">
            <li class="nav-item">
                <a href="{{ route('admin.employee-salary.payroll') }}"
                   data-href="{{ route('admin.employee-salary.payroll') }}"
                   class="nav-link  {{ request()->routeIs('admin.employee-salary.payroll*') ? 'active':'' }}">{{ __('index.payroll') }}</a>
            </li>
            <li class="nav-item">
                <a href="{{route('admin.salary-components.index')}}"
                   data-href="{{route('admin.salary-components.index')}}"
                   class="nav-link {{
                      request()->routeIs('admin.salary-components.*') ||
                      request()->routeIs('admin.payment-methods.*') ||
                      request()->routeIs('admin.salary-groups.*') ||
                      request()->routeIs('admin.bonus.*') ||
                      request()->routeIs('admin.overtime.*')||
                      request()->routeIs('admin.ssf.*')||
                      request()->routeIs('admin.under-time.*')||
                      request()->routeIs('admin.salary-tds.*')

                      ? 'active' : ''
                      }}">{{ __('index.payroll_setting') }}
                </a>
            </li>
            @can('view_advance_salary_list')
            <li class="nav-item">
                <a href="{{route('admin.advance-salaries.index')}}"
                   data-href="{{route('admin.advance-salaries.index')}}"
                   class="nav-link  {{request()->routeIs('admin.advance-salaries.*') ? 'active':''}}">{{ __('index.advance_salary') }}</a>
            </li>
            @endcan
            @can('view_salary_list')
            <li class="nav-item">
                <a href="{{route('admin.employee-salaries.index')}}"
                   data-href="{{route('admin.employee-salaries.index')}}"
                   class="nav-link  {{request()->routeIs('admin.employee-salaries.*') ? 'active':''}}">{{ __('index.employee_salary') }}</a>
            </li>
            @endcan
            @can('view_tax_report')
            <li class="nav-item">
                <a href="{{route('admin.payroll.tax-report.index')}}"
                   data-href="{{route('admin.payroll.tax-report.index')}}"
                   class="nav-link  {{request()->routeIs('admin.payroll.tax-report.*') ? 'active':''}}">{{ __('index.tax_report') }}</a>
            </li>
            @endcan


        </ul>
    </div>
</li>
@endcan

