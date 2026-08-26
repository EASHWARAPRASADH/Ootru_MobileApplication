<x-master-layout :assets="$assets ?? []">
    <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3">
                            <h5 class="font-weight-bold">{{ $pageTitle ?? __('message.list') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-lg-12">
                                <ul class="nav flex-column nav-pills me-3 tabslink" id="tabs-text" role="tablist">
                                    @if (in_array($page, ['profile_form', 'password-form', 'change_email_form', 'otp_form']))
                                        <li class="nav-item">
                                            <a href="javascript:void(0)"
                                                data-href="{{ route('layout_page') }}?page=profile_form"
                                                data-target=".paste_here"
                                                class="nav-link {{ $page == 'profile_form' ? 'active' : '' }}"
                                                data-toggle="tabajax" rel="tooltip"> {{ __('message.profile') }} </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0)"
                                                data-href="{{ route('layout_page') }}?page=password_form"
                                                data-target=".paste_here"
                                                class="nav-link {{ $page == 'password_form' ? 'active' : '' }}"
                                                data-toggle="tabajax" rel="tooltip">
                                                {{ __('message.change_password') }} </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="javascript:void(0)"
                                                data-href="{{ route('layout_page') }}?page=change_email_form"
                                                data-target=".paste_here"
                                                class="nav-link {{ $page == 'change_email_form' ? 'active' : '' }}"
                                                data-toggle="tabajax" rel="tooltip">
                                                {{ __('message.change_email') }} </a>
                                        </li>
                                    @else
                                        @hasanyrole('admin|demo_admin')
                                            <li class="nav-item">
                                                <a href="javascript:void(0)"
                                                    data-href="{{ route('layout_page') }}?page=general-setting"
                                                    data-target=".paste_here"
                                                    class="nav-link {{ $page == 'general-setting' ? 'active' : '' }}"
                                                    data-toggle="tabajax" rel="tooltip">
                                                    {{ __('message.general_settings') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)"
                                                    data-href="{{ route('layout_page') }}?page=mobile-config"
                                                    data-target=".paste_here"
                                                    class="nav-link {{ $page == 'mobile-config' ? 'active' : '' }}"
                                                    data-toggle="tabajax" rel="tooltip">
                                                    {{ __('message.mobile_config') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)"
                                                    data-href="{{ route('layout_page') }}?page=mail-setting"
                                                    data-target=".paste_here"
                                                    class="nav-link {{ $page == 'mail-setting' ? 'active' : '' }}"
                                                    data-toggle="tabajax" rel="tooltip">
                                                    {{ __('message.mail_settings') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)"
                                                    data-href="{{ route('layout_page') }}?page=language-setting"
                                                    data-target=".paste_here"
                                                    class="nav-link {{ $page == 'language-setting' ? 'active' : '' }}"
                                                    data-toggle="tabajax" rel="tooltip">
                                                    {{ __('message.language_settings') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)"
                                                    data-href="{{ route('layout_page') }}?page=notification-setting"
                                                    data-target=".paste_here"
                                                    class="nav-link {{ $page == 'notification-setting' ? 'active' : '' }}"
                                                    data-toggle="tabajax" rel="tooltip">
                                                    {{ __('message.notification_settings') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)"
                                                    data-href="{{ route('layout_page') }}?page=payment-setting"
                                                    data-target=".paste_here"
                                                    class="nav-link {{ $page == 'payment-setting' ? 'active' : '' }}"
                                                    data-toggle="tabajax" rel="tooltip">
                                                    {{ __('message.payment_settings') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)"
                                                    data-href="{{ route('layout_page') }}?page=invoice-setting"
                                                    data-target=".paste_here"
                                                    class="nav-link {{ $page == 'invoice-setting' ? 'active' : '' }}"
                                                    data-toggle="tabajax" rel="tooltip">
                                                    {{ __('message.invoice_settings') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)"
                                                    data-href="{{ route('layout_page') }}?page=order-setting"
                                                    data-target=".paste_here"
                                                    class="nav-link {{ $page == 'order-setting' ? 'active' : '' }}"
                                                    data-toggle="tabajax" rel="tooltip">
                                                    {{ __('message.order_settings') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)"
                                                    data-href="{{ route('layout_page') }}?page=register-setting"
                                                    data-target=".paste_here"
                                                    class="nav-link {{ $page == 'register-setting' ? 'active' : '' }}"
                                                    data-toggle="tabajax" rel="tooltip">
                                                    {{ __('message.register_setting') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)"
                                                    data-href="{{ route('layout_page') }}?page=reference-setting"
                                                    data-target=".paste_here"
                                                    class="nav-link {{ $page == 'reference-setting' ? 'active' : '' }}"
                                                    data-toggle="tabajax" rel="tooltip">
                                                    {{ __('message.reference_setting') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)"
                                                    data-href="{{ route('layout_page') }}?page=insurance-setting"
                                                    data-target=".paste_here"
                                                    class="nav-link {{ $page == 'insurance-setting' ? 'active' : '' }}"
                                                    data-toggle="tabajax" rel="tooltip">
                                                    {{ __('message.insurance_setting') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)"
                                                    data-href="{{ route('layout_page') }}?page=ordermail-setting"
                                                    data-target=".paste_here"
                                                    class="nav-link {{ $page == 'ordermail-setting' ? 'active' : '' }}"
                                                    data-toggle="tabajax" rel="tooltip">
                                                    {{ __('message.mail_template_setting') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)"
                                                    data-href="{{ route('layout_page') }}?page=database-backup"
                                                    data-target=".paste_here"
                                                    class="nav-link {{ $page == 'database-backup' ? 'active' : '' }}"
                                                    data-toggle="tabajax" rel="tooltip">
                                                    {{ __('message.database_backup') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)"
                                                    data-href="{{ route('layout_page') }}?page=print-label-mobail-number"
                                                    data-target=".paste_here"
                                                    class="nav-link {{ $page == 'print-label-mobail-number' ? 'active' : '' }}"
                                                    data-toggle="tabajax" rel="tooltip">
                                                    {{ __('message.print_label') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)"
                                                    data-href="{{ route('layout_page') }}?page=sms-settings"
                                                    data-target=".paste_here"
                                                    class="nav-link {{ $page == 'sms-settings' ? 'active' : '' }}"
                                                    data-toggle="tabajax" rel="tooltip">
                                                    {{ __('message.sms_setting') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)"
                                                    data-href="{{ route('layout_page') }}?page=crisp-setting"
                                                    data-target=".paste_here"
                                                    class="nav-link {{ $page == 'crisp-setting' ? 'active' : '' }}"
                                                    data-toggle="tabajax" rel="tooltip">
                                                    {{ __('message.crisp_config') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)"
                                                    data-href="{{ route('layout_page') }}?page=emergency-setting"
                                                    data-target=".paste_here"
                                                    class="nav-link {{ $page == 'emergency-setting' ? 'active' : '' }}"
                                                    data-toggle="tabajax" rel="tooltip">
                                                    {{ __('message.emergency_config') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)"
                                                    data-href="{{ route('layout_page') }}?page=onesingal-setting"
                                                    data-target=".paste_here"
                                                    class="nav-link {{ $page == 'onesingal-setting' ? 'active' : '' }}"
                                                    data-toggle="tabajax" rel="tooltip">
                                                    {{ __('message.onesingal_config') }}</a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="javascript:void(0)"
                                                    data-href="{{ route('layout_page') }}?page=highdemand-settings"
                                                    data-target=".paste_here"
                                                    class="nav-link {{ $page == 'highdemand-settings' ? 'active' : '' }}"
                                                    data-toggle="tabajax" rel="tooltip">
                                                    {{ __('message.high_demand_zone_config') }}</a>
                                            </li>
                                        @endhasanyrole
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12 col-lg-12">
                                <div class="tab-content" id="pills-tabContent-1">
                                    <div class="tab-pane active p-1">
                                        <div class="paste_here"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('bottom_script')
        <script>
            $(document).ready(function(event) {
                var $this = $('.nav-item').find('a.active');
                loadurl = "{{ route('layout_page') }}?page={{ $page }}";

                targ = $this.attr('data-target');

                id = this.id || '';

                $.post(loadurl, {
                    '_token': $('meta[name=csrf-token]').attr('content')
                }, function(data) {
                    $(targ).html(data);
                });

                $this.tab('show');
                return false;
            });
        </script>
        @if (request('page') == 'otp_form')
            <script>
                $(document).ready(function() {
                    const page = '{{ request('page') }}';
                    const target = $('.paste_here');

                    $.ajax({
                        url: '{{ route('layout_page') }}?page=' + page,
                        success: function(data) {
                            target.html(data);
                        },
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(data) {
                            target.html(data);
                        },
                        error: function() {
                            target.html('<div class="alert alert-danger">Failed to load OTP form.</div>');
                        }
                    });
                });
            </script>
        @endif
    @endsection
</x-master-layout>
