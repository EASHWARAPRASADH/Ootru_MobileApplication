<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3 flex-wrap">
                            <h5 class="font-weight-bold mb-0">{{ $pageTitle ?? __('message.list') }}</h5>
                            <div class="d-flex align-items-center mt-2 mt-md-0">
                                @if($auth_user->can('permission-add'))
                                    <a href="{{ route('permission.add',['type'=>'permission']) }}" class="btn btn-sm btn-primary loadRemoteModel"><i class="fa fa-plus-circle"></i> {{ __('message.add_form_title',['form' => __('message.permission')  ]) }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 mb-3">
                <div class="card p-2 shadow-sm border-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-transparent border-right-0"><i class="fa fa-search text-primary"></i></span>
                        </div>
                        <input type="text" id="searchPermission" class="form-control border-left-0" placeholder="Search page, module, or permission (e.g. Order, User, Report, Vehicle, City, Coupon, Settings...)" autocomplete="off" style="font-size: 0.95rem;">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="clearSearch" style="display: none;"><i class="fa fa-times"></i> Clear</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div id="noPermissionFound" class="alert alert-info text-center py-4" style="display: none;">
                    <i class="fa fa-info-circle fa-2x mb-2 text-info d-block"></i>
                    No matching pages or permissions found. Try searching with another keyword.
                </div>

                {{ html()->form('POST', route('permission.store'))->open() }} 
                <div class="accordion cursor" id="permissionList">
                    @foreach($permission as $key => $data)
                        <?php
                            $categoryLabels = [
                                'dashboard' => 'Dashboard & Quick Actions',
                                'country' => 'Country',
                                'city' => 'City',
                                'order' => 'Orders',
                                'users' => 'Users / Clients',
                                'subadmin' => 'Sub Admin',
                                'deliveryman' => 'Delivery Man',
                                'deliverymandocument' => 'Delivery Man Document',
                                'document' => 'Document',
                                'vehicle' => 'Vehicle',
                                'extracharge' => 'Extra Charge',
                                'parcel_type' => 'Parcel Type',
                                'staticdata' => 'Parcel Type',
                                'couriercompanies' => 'Courier Companies',
                                'push notification' => 'Push Notification',
                                'report' => 'Reports',
                                'mail_template' => 'Mail Template',
                                'ordermail' => 'Mail Template',
                                'sms_template' => 'SMS Template',
                                'ordersms' => 'SMS Template',
                                'withdrawrequest' => 'Withdraw Request',
                                'claims' => 'Claims Management',
                                'customersupport' => 'Customer Support',
                                'coupon' => 'Coupon',
                                'emergency' => 'Emergency',
                                'app_language_setting' => 'App Language Setting',
                                'role' => 'Role Settings',
                                'permission' => 'Permission Settings',
                                'website_section' => 'Website Sections',
                                'pages' => 'Pages',
                                'setting' => 'System Settings',
                                'rest_api' => 'REST API',
                            ];
                            $k = $categoryLabels[strtolower($data->name)] ?? ucwords(str_replace('_', ' ', $data->name));
                        ?>
                        <div class="card mb-2 permission-category-card" data-category="{{ strtolower($data->name . ' ' . $k) }}">
                            <div class="card-header d-flex justify-content-between collapsed btn" id="heading_{{$key}}" data-toggle="collapse" data-target="#pr_{{$key}}" aria-expanded="false" aria-controls="pr_{{$key}}">
                                <div class="header-title">
                                    <h6 class="mb-0 text-capitalize font-weight-bold">
                                        <i class="fa fa-plus mr-10 accordion-icon"></i> {{ $k }}
                                    </h6>
                                </div>
                            </div>
                            <div id="pr_{{$key}}" class="collapse bg_light_gray table-container" aria-labelledby="heading_{{$key}}" data-parent="#permissionList">
                                <div class="sticky-header d-flex justify-content-between align-items-center p-2 bg-white border-bottom">
                                    <span class="text-muted small font-italic">Check boxes for the roles you want to grant access to:</span>
                                    <input type="submit" name="Save" value="Save Permissions" class="btn btn-sm btn-primary px-3">
                                </div>
                                <div class="card-body table-responsive p-0">
                                    <table class="table text-center table-bordered bg_white mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="min-width: 140px; text-align: left;" class="pl-3">{{ __('message.name') }}</th>
                                                @foreach($data->subpermission as $p)
                                                    <?php
                                                        $subLabels = [
                                                            'system setting' => 'General Settings',
                                                            'payment-list' => 'Payment Gateway',
                                                            'privacy policy' => 'Privacy Policy',
                                                            'terms condition' => 'Terms & Conditions',
                                                            'dashboard-view' => 'Dashboard',
                                                            'view_site' => 'View Site',
                                                            'dispatch' => 'Dispatch',
                                                            'high_demanding_areas' => 'High Demanding Areas',
                                                            'order_location-add' => 'Order Locations Map',
                                                            'delivery_boy_location-add' => 'Delivery Man Map',
                                                            'bulkimport-list' => 'Bulk Import',
                                                            'ordermail-list' => 'All Mail Templates',
                                                        ];
                                                        $permLabel = $subLabels[strtolower($p->name)] ?? ucwords(str_replace(['-','_'], ' ', $p->name));
                                                    ?>
                                                    <th class="text-capitalize permission-col-header" data-perm="{{ strtolower($p->name . ' ' . $permLabel) }}">{{ $permLabel }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($roles as $role)
                                                <tr>
                                                    <td style="text-align: left; font-weight: 600;" class="pl-3">{{ ucwords(str_replace('_',' ',$role->name)) }}</td>
                                                    @foreach($data->subpermission as $p)
                                                        <td>
                                                            <input class="checkbox no-wh permission_check" 
                                                                id="permission-{{$role->id}}-{{$p->id}}" 
                                                                type="checkbox" 
                                                                name="permission[{{$p->name}}][]" 
                                                                value='{{$role->name}}' 
                                                                {{ (checkRolePermission($role,$p->name)) ? 'checked' : '' }} 
                                                                @if($role->is_hidden) disabled @endif >
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                {{ html()->form()->close() }}
            </div>
        </div>
    </div>
</div>
@section('bottom_script')
    <script>
        (function($) {
            "use strict";
            $(document).ready(function(){
                // Accordion +/- icon toggle
                $(document).on('click', '#permissionList .card-header', function(){
                    var icon = $(this).find('.accordion-icon');
                    var isExpanded = $(this).attr('aria-expanded') === 'true';
                    
                    $('#permissionList .accordion-icon').removeClass('fa-minus').addClass('fa-plus');
                    if (!isExpanded) {
                        icon.removeClass('fa-plus').addClass('fa-minus');
                    } else {
                        icon.removeClass('fa-minus').addClass('fa-plus');
                    }
                });

                // Real-time Permission Search Bar
                $('#searchPermission').on('input keyup', function() {
                    var query = $(this).val().toLowerCase().trim();
                    
                    if (query.length > 0) {
                        $('#clearSearch').show();
                    } else {
                        $('#clearSearch').hide();
                    }

                    var matchCount = 0;

                    $('.permission-category-card').each(function() {
                        var card = $(this);
                        var categoryText = card.data('category') || '';
                        var collapseDiv = card.find('.collapse');
                        var headerIcon = card.find('.accordion-icon');
                        
                        // Check subpermission names in table headers
                        var subPermMatches = false;
                        card.find('.permission-col-header').each(function() {
                            var permName = $(this).data('perm') || $(this).text().toLowerCase();
                            if (permName.indexOf(query) !== -1) {
                                subPermMatches = true;
                            }
                        });

                        if (query === '' || categoryText.indexOf(query) !== -1 || subPermMatches) {
                            card.show();
                            matchCount++;
                            if (query !== '') {
                                // Auto-expand matching cards for instant viewing/editing
                                collapseDiv.addClass('show');
                                headerIcon.removeClass('fa-plus').addClass('fa-minus');
                            }
                        } else {
                            card.hide();
                            collapseDiv.removeClass('show');
                            headerIcon.removeClass('fa-minus').addClass('fa-plus');
                        }
                    });

                    if (query === '') {
                        // When search cleared, collapse all back to neat accordion
                        $('.permission-category-card .collapse').removeClass('show');
                        $('.permission-category-card .accordion-icon').removeClass('fa-minus').addClass('fa-plus');
                    }

                    if (matchCount === 0 && query !== '') {
                        $('#noPermissionFound').show();
                    } else {
                        $('#noPermissionFound').hide();
                    }
                });

                // Clear button
                $('#clearSearch').on('click', function() {
                    $('#searchPermission').val('').trigger('input').focus();
                });
            });
        })(jQuery);
    </script>
@endsection
</x-master-layout>
