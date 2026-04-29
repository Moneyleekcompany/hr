@canany([
    'list_router',
])
    <li class="nav-item  {{
                   request()->routeIs('admin.routers.*') ||
                   request()->routeIs('admin.qr.*')||
                   request()->routeIs('admin.nfc.*') ||
                   request()->routeIs('admin.zkteco.*')

                ? 'active' : ''
            }}"
    >
        <a class="nav-link" data-bs-toggle="collapse"
           href="#attendance_method"
           data-href="#"
           role="button" aria-expanded="false" aria-controls="settings">
            <i class="link-icon" data-feather="tool"></i>
            <span class="link-title"> {{ __('index.attendance_methods') }} </span>
            <i class="link-arrow" data-feather="chevron-down"></i>
        </a>
        <div class="{{
                      request()->routeIs('admin.routers.*') ||
                      request()->routeIs('admin.qr.*') ||
                      request()->routeIs('admin.nfc.*') ||
                      request()->routeIs('admin.zkteco.*')

                       ? '' : 'collapse'  }} " id="attendance_method">

            <ul class="nav sub-menu">

                @can('list_router')
                    <li class="nav-item">
                        <a
                            href="{{route('admin.routers.index')}}"
                            data-href="{{route('admin.routers.index')}}"
                            class="nav-link {{request()->routeIs('admin.routers.*') ? 'active' : ''}}">{{ __('index.routers') }}
                        </a>
                    </li>
                @endcan

                @can('list_general_setting')
                    <li class="nav-item">
                        <a
                            href="{{route('admin.nfc.index')}}"
                            data-href="{{route('admin.nfc.index')}}"
                            class="nav-link {{request()->routeIs('admin.nfc.*') ? 'active' : ''}}">{{ __('index.nfc') }}</a>
                    </li>
                @endcan


                <li class="nav-item">
                    <a
                        href="{{route('admin.qr.index')}}"
                        data-href="{{route('admin.qr.index')}}"
                        class="nav-link {{request()->routeIs('admin.qr.*') ? 'active' : ''}}">{{ __('index.qr') }}</a>
                </li>

            <li class="nav-item">
                <a
                    href="{{route('admin.zkteco-devices.index')}}"
                    data-href="{{route('admin.zkteco-devices.index')}}"
                    class="nav-link {{request()->routeIs('admin.zkteco-devices.*') || request()->routeIs('admin.zkteco.*') ? 'active' : ''}}">{{ __('index.biometric_device') }}</a>
            </li>

            </ul>
        </div>
    </li>
@endcanany


@canany([
    'list_role',
    'list_router',
    'list_general_setting',
    'list_app_setting',
    'list_notification'
])
    <li class="nav-item  {{
                   request()->routeIs('admin.roles.*') ||
                   request()->routeIs('admin.notifications.*')||
                   request()->routeIs('admin.general-settings.*')||
                   request()->routeIs('admin.fiscal_year.*')||
                    request()->routeIs('admin.payment-currency.*')||
                    request()->routeIs('admin.security_logs.*')
                ? 'active' : ''
            }}"
    >
        <a class="nav-link" data-bs-toggle="collapse"
           href="#setting"
           data-href="#"
           role="button" aria-expanded="false" aria-controls="settings">
            <i class="link-icon" data-feather="settings"></i>
            <span class="link-title"> {{ __('index.settings') }} </span>
            <i class="link-arrow" data-feather="chevron-down"></i>
        </a>
        <div class="{{ request()->routeIs('admin.roles.*') ||
                      request()->routeIs('admin.general-settings.*') ||
                      request()->routeIs('admin.app-settings.*') ||
                      request()->routeIs('admin.notifications.*')||
                      request()->routeIs('admin.payment-currency.*')||
                      request()->routeIs('admin.fiscal_year.*')||
                      request()->routeIs('admin.feature.index')||
                      request()->routeIs('admin.security_logs.*')

                       ? '' : 'collapse'  }} " id="setting">

            <ul class="nav sub-menu">
                @can('list_role')
                    <li class="nav-item">
                    <a
                        href="{{route('admin.roles.index')}}"
                        data-href="{{route('admin.roles.index')}}"
                       class="nav-link {{request()->routeIs('admin.roles.*') ? 'active' : ''}}">{{ __('index.roles_permissions') }}</a>
                </li>
                @endcan

                @can('list_general_setting')
                    <li class="nav-item">
                        <a
                            href="{{route('admin.general-settings.index')}}"
                            data-href="{{route('admin.general-settings.index')}}"
                            class="nav-link {{request()->routeIs('admin.general-settings.*') ? 'active' : ''}}">{{ __('index.general_settings') }}</a>
                    </li>
                @endcan

                @can('list_app_setting')
                    <li class="nav-item">
                        <a
                            href="{{route('admin.app-settings.index')}}"
                            data-href="{{route('admin.app-settings.index')}}"
                           class="nav-link {{request()->routeIs('admin.app-settings.*') ? 'active' : ''}}">{{ __('index.app_settings') }}</a>
                    </li>
                @endcan

                @can('list_notification')
                    <li class="nav-item">
                        <a
                            href="{{route('admin.notifications.index')}}"
                            data-href="{{route('admin.notifications.index')}}"
                           class="nav-link {{request()->routeIs('admin.notifications.*') ? 'active' : ''}}">{{ __('index.notifications') }}</a>
                    </li>
                @endcan

                <li  class="nav-item {{request()->routeIs('admin.payment-currency.*')  ? 'active' : '' }}">
                    <a
                        href="{{route('admin.payment-currency.index')}}"
                        data-href="{{route('admin.payment-currency.index')}}"
                        class="nav-link {{request()->routeIs('admin.payment-currency.*') ? 'active' : ''}}"> {{ __('index.payment_currency') }}</a>
                </li>

                @can('feature_list')
                    <li  class="nav-item {{request()->routeIs('admin.feature.index')  ? 'active' : '' }}">
                        <a
                            href="{{route('admin.feature.index')}}"
                            data-href="{{route('admin.feature.index')}}"
                            class="nav-link {{request()->routeIs('admin.feature.index') ? 'active' : ''}}"> {{ __('index.feature_control') }}</a>
                    </li>
                @endcan

{{--                @can('feature_list')--}}
                    <li  class="nav-item {{ request()->routeIs('admin.fiscal_year.*')  ? 'active' : '' }}">
                        <a
                            href="{{route('admin.fiscal_year.index')}}"
                            data-href="{{route('admin.fiscal_year.index')}}"
                            class="nav-link {{request()->routeIs('admin.fiscal_year.*') ? 'active' : ''}}"> {{ __('index.fiscal_year') }}</a>
                    </li>
{{--                @endcan--}}

                @if(optional(auth()->user()->role)->slug === 'admin')
                <li class="nav-item {{ request()->routeIs('admin.security_logs.*') ? 'active' : '' }}">
                    <a
                        href="{{route('admin.security_logs.index')}}"
                        data-href="{{route('admin.security_logs.index')}}"
                        class="nav-link {{request()->routeIs('admin.security_logs.*') ? 'active' : ''}}"> السجل الأمني (Security Logs)</a>
                </li>
                @endif


            </ul>
        </div>
    </li>
@endcanany
