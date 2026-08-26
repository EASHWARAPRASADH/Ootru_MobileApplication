<x-master-layout :assets="$assets ?? []">
    <?php $id = $id ?? null;?>
    @if(isset($id))
        {{ html()->modelForm($data, 'PATCH', route('order.update', $id))->attribute('enctype', 'multipart/form-data')->attribute('id', 'order_form')->open() }}
    @else
        {{ html()->form('POST', route('order.store'))->attribute('enctype', 'multipart/form-data')->attribute('id', 'order_form')->open() }} 
    @endif
        {{html()->hidden('client_id', auth()->user()->id) }}
        {{html()->hidden('status', 'create') }}
     <input type="hidden" id="fixed_charges" name="fixed_charges">
     <input type="hidden" id="total_amount" name="total_amount">
     <input type="hidden" id="total_distance" name="total_distance">
     <input type="hidden" id="weight_charge" name="weight_charge">
     <input type="hidden" id="distance_charge" name="distance_charge">
     <input type="hidden" id="extra_charges" name="extra_charges">
     <input type="hidden" id="vehicle_charge" name="vehicle_charge">
     <input type="hidden" id="package_value" name="package_value">
     <input type="hidden" id="insurance_charge" name="insurance_charge">
     <input type="hidden" id="bid_type" name="bid_type">

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <div class="header-title">
                <h4 class="card-title">{{ $pageTitle }}</h4>
            </div>
        </div>
        <div class="row ml-4">
            <a id="deliverNowBtn" class="btn btn-sm btn-primary m-2 mt-1 p-1 fs-4" style="font-size: larger;color: white;"><i class="fa fa-clock"></i> {{ __('message.deliver_now') }}</a>
            <a id="calendarBtn" class="btn btn-sm m-2 mt-1 p-1" style="font-size: larger"><i class="fa fa-calendar"></i> {{ __('message.schedule') }}</a>
            @if(appSettingcurrency('is_bidding_in_order'))
            <a id="bidBtn" class="btn btn-sm m-2 mt-1 p-1" style="font-size: larger" data-state="1"><i class="fa-solid fa-hand-holding-hand"></i> {{ __('message.bid') }}</a>                      
            @else
            @endif
        </div>
        <div class="card-body">
            <div class="new-user-info">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="card card-block">
                                        <div class="card-body mt-3 pt-0 pb-0">
                                            <div class="form-group" id="scheduleSection" style="display: none;">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h4 class="card-title">{{ __('message.pick_time') }}</h4>
                                                            <div class="card card-block">
                                                                <div class="card-header">
                                                                    <div class="form-group row">
                                                                        <div class="col-md-12">
                                                                            {{ html()->date('pickup_date', old('pickup_date'))
                                                                                ->placeholder(__('message.date'))
                                                                                ->class('form-control min-datepicker_tomorrow') }}
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <div class="col-md-6">
                                                                            {{ html()->time('pickup_start_time', old('start_time'))
                                                                                ->placeholder(__('message.from'))
                                                                                ->class('form-control min-timerange-picker')
                                                                                ->id('pickup_start_time') }}
                                                                            <span class="text-danger" id="form_validation_pickup_start_time"></span>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            {{ html()->time('pickup_end_time', old('end_time'))
                                                                                ->placeholder(__('message.to'))
                                                                                ->class('form-control min-timerange-picker')
                                                                                ->id('pickup_end_time') }}
                                                                            <span class="text-danger" id="form_validation_pickup_end_time"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <h4 class="card-title">{{ __('message.deliver_time') }}</h4>
                                                            <div class="card card-block">
                                                                <div class="card-header">
                                                                    <div class="form-group row">
                                                                        <div class="col-md-12">
                                                                            {{ html()->date('delivery_date', old('date'))
                                                                                ->placeholder(__('message.date'))
                                                                                ->class('form-control min-datepicker_tomorrow') }}
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <div class="col-md-6">
                                                                            {{ html()->time('delivery_start_time', old('time'))
                                                                                ->placeholder(__('message.from'))
                                                                                ->class('form-control min-timerange-picker')
                                                                                ->id('delivery_start_time') }}
                                                                            <span class="text-danger" id="form_validation_delivery_start_time"></span>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            {{ html()->time('delivery_end_time', old('time'))
                                                                                ->placeholder(__('message.to'))
                                                                                ->class('form-control min-timerange-picker')
                                                                                ->id('delivery_end_time') }}
                                                                            <span class="text-danger" id="form_validation_delivery_end_time"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-4" id="parcel_type">
                                                    {{ html()->label(__('message.parcel_type') . ' <span class="text-danger">*</span>', 'parcel_type')->class('form-control-label') }}
                                                    {{ html()->text('parcel_type', old('parcel_type'))
                                                        ->id('parcel_type_text')
                                                        ->placeholder(__('message.name'))
                                                        ->class('form-control')
                                                        ->attribute('required', true) }}
                                                    <div class="row ml-3" id="parcel_type_container" style="display: none;">
                                                        @foreach ($staticData as $key)
                                                            <div class="m-1 border border-secondary pl-1 pr-1 mr-1 default-hidden rounded" onclick="setParcelType('{{ $key->label }}')" style="cursor: pointer;">{{ $key->label }}</div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <div class="form-group col-md-4">
                                                    {{ html()->label(__('message.weight') . ' <span class="text-danger">*</span>', 'total_weight')->class('form-control-label') }}
                                                    {{ html()->number('total_weight', $data->total_weight ?? null)
                                                        ->class('form-control')
                                                        ->attribute('min', 0)
                                                        ->attribute('step', 'any')
                                                        ->placeholder(__('message.weight'))
                                                        ->attribute('required', true) }}
                                                </div>

                                                <div class="form-group col-md-4">
                                                    {{ html()->label(__('message.number_of_parcels') . ' <span class="text-danger">*</span>', 'total_parcel')->class('form-control-label') }}
                                                    {{ html()->number('total_parcel', $data->total_distance ?? null)
                                                        ->class('form-control')
                                                        ->attribute('min', 0)
                                                        ->attribute('step', 'any')
                                                        ->placeholder(__('message.number_of_parcels'))
                                                        ->attribute('required', true) }}
                                                </div>

                                                <div class="form-group col-md-12">
                                                    {{ html()->label(__('message.parcel_description'), 'parcel_description')->class('form-control-label') }}
                                                    {{ html()->textarea('description', $data->description ?? null)
                                                        ->class('form-control textarea')
                                                        ->rows(3)
                                                        ->placeholder(__('message.parcel_description')) }}
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <!-- {{ html()->label(__('message.country') . ' <span class="text-danger">*</span>', 'country_id')->class('form-control-label') }}
                                                    {{ html()->select('country_id', isset($data->country) ? [$data->country->id => $data->country->name] : [], old('country_id'))
                                                        ->class('select2js form-group country_id')
                                                        ->data('placeholder', __('message.country'))
                                                        ->data('ajax--url', route('ajax-list', ['type' => 'country-list'])) }} -->
                                                    {{ html()->label(__('message.country') . ' <span class="text-danger">*</span>')->class('form-control-label') }}
                                                    {{ html()->select('country', isset($duration) ? [ $duration[0] => $duration[0] ] : [], old('country'))
                                                        ->class('form-control select2js')
                                                        ->id('country_id')
                                                        ->attribute('data-placeholder', __('message.select_name', ['select' => __('message.country')]))
                                                        ->attribute('data-ajax--url', route('ajax-list', ['type' => 'country-list'])) }} 
                                                </div>

                                                <div class="form-group col-md-6">
                                                    {{ html()->label(__('message.city') . ' <span class="text-danger">*</span>', 'city_id')->class('form-control-label') }}
                                                    {{ html()->select('city_id', isset($data->city) ? [$data->city->id => $data->city->name] : [], old('city_id'))
                                                        ->class('select2js form-group city_id')
                                                        ->data('placeholder', __('message.city')) }}
                                                </div>

                                                <div class="col-md-6 mt-3">
                                                    <h4 class="mb-3 ml-3">{{ __('message.pickup_information') }}</h4>
                                                    <div class="form-group col-md-12">
                                                        {{ html()->hidden('pickup_point[start_time]', null)->id('combined_pickup_start_time') }}
                                                        {{ html()->hidden('pickup_point[end_time]', null)->id('combined_pickup_end_time') }}
                                                        {{ html()->label(__('message.pickup_location') . ' <span class="text-danger">*</span>', 'pickup_location')->class('form-control-label') }}
                                                        {{ html()->text('pickup_point[address]', old('pickup_location'))
                                                            ->class('form-control')
                                                            ->id('pickup_location')
                                                            ->attribute('required', true) }}
                                                    </div>
                                                    {{ html()->hidden('pickup_point[latitude]', null)->id('pickup_lat') }}
                                                    {{ html()->hidden('pickup_point[longitude]', null)->id('pickup_lng') }}
                                                    <div class="form-group col-md-12">
                                                        {{ html()->label(__('message.pickup_contact_number') . ' <span class="text-danger">*</span>', 'pickup_contact_number')->class('form-control-label') }}
                                                        {{ html()->text('pickup_point[contact_number]', old('pickup_contact_number'))
                                                            ->placeholder(__('message.pickup_contact_number'))
                                                            ->class('form-control')
                                                            ->id('contact_nbr')
                                                            ->attribute('required', true) }}
                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        {{ html()->label(__('message.pickup_person_name') . ' <span class="text-danger">*</span>', 'pickup_person_name')->class('form-control-label') }}
                                                        {{ html()->text('pickup_point[name]', old('pickup_person_name'))
                                                            ->placeholder(__('message.pickup_person_name'))
                                                            ->class('form-control')
                                                            ->attribute('required', true) }}
                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        {{ html()->label(__('message.pickup_description') . ' <span class="text-danger">*</span>', 'pickup_description')->class('form-control-label') }}
                                                        {{ html()->textarea('pickup_point[description]', null)
                                                            ->class('form-control textarea')
                                                            ->rows(1)
                                                            ->placeholder(__('message.pickup_description')) }}
                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        {{ html()->label(__('message.pickup_instruction'), 'pickup_instruction')->class('form-control-label') }}
                                                        {{ html()->textarea('pickup_point[instruction]', null)
                                                            ->class('form-control textarea')
                                                            ->rows(1)
                                                            ->placeholder(__('message.pickup_instruction')) }}
                                                    </div>
                                                </div>

                                                <div class="col-md-6 mt-3">
                                                    <h4 class="mb-3 ml-3">{{ __('message.deliver_information') }}</h4>
                                                    <div class="form-group col-md-12">
                                                        {{ html()->hidden('delivery_point[start_time]', null)->id('combined_delivery_start_time') }}
                                                        {{ html()->hidden('delivery_point[end_time]', null)->id('combined_delivery_end_time') }}
                                                        {{ html()->label(__('message.delivery_location') . ' <span class="text-danger">*</span>', 'delivery_location')->class('form-control-label') }}
                                                        {{ html()->text('delivery_point[address]', old('delivery_location'))
                                                            ->class('form-control')
                                                            ->id('delivery_location')
                                                            ->attribute('required', true) }}
                                                    </div>
                                                    {{ html()->hidden('delivery_point[latitude]', null)->id('delivery_lat') }}
                                                    {{ html()->hidden('delivery_point[longitude]', null)->id('delivery_lng') }}
                                                    <div class="form-group col-md-12">
                                                        {{ html()->label(__('message.delivery_contact_number') . ' <span class="text-danger">*</span>', 'delivery_contact_number')->class('form-control-label') }}
                                                        {{ html()->text('delivery_point[contact_number]', old('delivery_contact_number'))
                                                            ->placeholder(__('message.delivery_contact_number'))
                                                            ->class('form-control')
                                                            ->id('phone')
                                                            ->attribute('required', true) }}
                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        {{ html()->label(__('message.deliver_person_name') . ' <span class="text-danger">*</span>', 'deliver_person_name')->class('form-control-label') }}
                                                        {{ html()->text('delivery_point[name]', old('deliver_person_name'))
                                                            ->placeholder(__('message.deliver_person_name'))
                                                            ->class('form-control')
                                                            ->attribute('required', true) }}
                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        {{ html()->label(__('message.delivery_description') . ' <span class="text-danger">*</span>', 'delivery_description')->class('form-control-label') }}
                                                        {{ html()->textarea('delivery_point[description]', null)
                                                            ->class('form-control textarea')
                                                            ->rows(1)
                                                            ->placeholder(__('message.delivery_description')) }}
                                                    </div>
                                                    <div class="form-group col-md-12">
                                                        {{ html()->label(__('message.delivery_instruction'), 'delivery_instruction')->class('form-control-label') }}
                                                        {{ html()->textarea('delivery_point[instruction]', null)
                                                            ->class('form-control textarea')
                                                            ->rows(1)
                                                            ->placeholder(__('message.delivery_instruction')) }}
                                                    </div>
                                                </div>

                                                <div class="form-group col-md-6">
                                                    {{ html()->label(__('message.payment_collect_form') . ' <span class="text-danger">*</span>', 'payment_collect_from')->class('form-control-label') }}
                                                    {{ html()->select('payment_collect_from', ['on_pickup' => __('message.pickup_location'), 'on_delivery' => __('message.delivery_location')], old('payment_collect_from'))
                                                        ->class('form-control select2js') }}
                                                </div>

                                                @if(isset($is_vehicle_in_order) && $is_vehicle_in_order == 1)
                                                <div class="form-group col-md-6">
                                                    {{ html()->label(__('message.vehicle'), 'vehicle_id')->class('form-control-label') }}
                                                    {{ html()->select('vehicle_id', isset($data->vehicle) ? [$data->vehicle->id => $data->vehicle->title] : [], old('vehicle_id'))
                                                        ->class('select2js form-group vehicle_id')
                                                        ->data('placeholder', __('message.vehicle')) }}
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <h4 class="card-title">{{ __('message.charge_details') }}</h4>
                                    @if($is_insurance_percentage != null)
                                        <div class="custom-control custom-checkbox m-2">
                                            {{ html()->checkbox('add_courier_insurance', false, 1)
                                                ->class('custom-control-input')
                                                ->id('add_courier_insurance') }}
                                            {{ html()->label(__('message.add_courier_insurance'), 'add_courier_insurance')
                                                ->class('custom-control-label')
                                                ->for('add_courier_insurance') }}
                                        </div>
                                        <div class="form-group col-md-12" id="insurance_container" style="display:none;">
                                            {{ html()->label(__('message.parcel_value'). ' <span class="text-danger">*</span>', 'package_value')
                                                ->class('form-control-label') }}
                                            {{ html()->number('package_value', old('package_value'))
                                                ->id('insurance_text')
                                                ->placeholder(__('message.charges'))
                                                ->class('form-control')
                                                ->required()
                                                ->attribute('min', 0) }}
                                        </div>
                                    @endif
                                    <div class="card card-block mt-4 mr-0 ml-0">
                                        <table style="border-style:none" class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('message.charges_type') }}</th>
                                                    <th>{{ __('message.amount') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ __('message.delivery_charges') }}</td>
                                                    <td id="display_fixed_charges"></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('message.weight_charge') }}</td>
                                                    <td id="display_weight_charge"></td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('message.distance_charge') }}</td>
                                                    <td id="display_distance_charge"></td>
                                                </tr>
                                                @if(isset($is_vehicle_in_order) && $is_vehicle_in_order == 1)
                                                    <tr>
                                                        <td>{{ __('message.vehicle_charge') }}</td>
                                                        <td id="display_vehicle_charge"></td>
                                                    </tr>
                                                @endif
                                                @if ($is_insurance_percentage != null)
                                                    <tr class="insurance_charge_col">
                                                        <td>{{ __('message.insurance_charge') }}</td>
                                                        <td id="display_insurance_charge"></td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <td class="font-weight-bold">{{__('message.extracharges')}}</td>
                                                    <td></td>
                                                </tr>
                                                <tbody id="dynamic_charges">
                                                </tbody>
                                                <tr>
                                                    <td class="font-weight-bold">{{ __('message.total_amount')}}</td>
                                                    <td id="display_total_amount" class="font-weight-bold"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    {{ html()->submit(__('message.save'))->class('btn btn-md btn-primary float-right') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ html()->form()->close() }}
    @section('bottom_script')
        <script>
            $(document).ready(function() {
                $('#deliverNowBtn, #calendarBtn, #bidBtn').click(function() {
                    if ($(this).attr('id') === 'deliverNowBtn') {
                        $('#scheduleSection').hide();
                        $('#deliverNowBtn').addClass('btn-primary').css('color', 'white');
                        $('#calendarBtn, #bidBtn').removeClass('btn-primary').css('color', '');
                    } else if ($(this).attr('id') === 'calendarBtn') {
                        $('#scheduleSection').show();
                        $('#calendarBtn').addClass('btn-primary').css('color', 'white');
                        $('#deliverNowBtn, #bidBtn').removeClass('btn-primary').css('color', '');
                    } else if ($(this).attr('id') === 'bidBtn') {
                        $('#bidBtn').addClass('btn-primary').css('color', 'white');
                        $('#deliverNowBtn, #calendarBtn').removeClass('btn-primary').css('color', '');
                        let newState = $(this).data('state');
                            $('#bid_type').val(newState);
                    }
                });

                $('#order_form').validate({
                    rules: {
                        pickup_start_time: {
                            required: true
                        },
                        pickup_end_time: {
                            required: true,
                            greaterThanEqual: '#pickup_start_time'
                        },
                        delivery_start_time: {
                            required: true
                        },
                        delivery_end_time: {
                            required: true,
                            greaterThanEqual: '#delivery_start_time'
                        },
                        parcel_type: { required: true },
                        total_weight: { required: true },
                        total_parcel: { required: true },
                        parcel_description: { required: true },
                        country_id: { required: true },
                        city_id: { required: true },
                        'pickup_point[address]': { required: true },
                        'pickup_point[description]': { required: true },
                        'pickup_point[contact_number]': { required: true },
                        'delivery_point[address]': { required: true },
                        'delivery_point[contact_number]': { required: true },
                        'delivery_point[description]': { required: true },
                    },
                    messages: {
                        pickup_start_time: {
                            required: "{{ __('message.from_date_required') }}"
                        },
                        pickup_end_time: {
                            required: "{{ __('message.to_date_required') }}",
                            greaterThanEqual: "{{ __('message.to_time_must_be_greater_than_from_time') }}"
                        },
                        delivery_start_time: {
                            required: "{{ __('message.from_date_required') }}"
                        },
                        delivery_end_time: {
                            required: "{{ __('message.to_date_required') }}",
                            greaterThanEqual: "{{ __('message.to_time_must_be_greater_than_from_time') }}"
                        },
                        parcel_type: { required: "{{__('message.please_select_parcel_type')}}"},
                        total_weight: { required: "{{__('message.please_enter_total_weight')}}"},
                        total_parcel: { required: "{{__('message.please_enter_total_parcel')}}"},
                        parcel_description: { required: "{{__('message.please_enter_parcel_description')}}"},
                        country_id: { required: "{{__('message.please_select_country')}}"},
                        city_id: { required: "{{__('message.please_select_city')}}"},
                        'pickup_point[address]': { required: "{{__('message.please_enter_pickup_address')}}" },
                        'pickup_point[contact_number]': { required: "{{__('message.please_enter_pickup_contact_number')}}" },
                        'pickup_point[description]': { required: "{{__('message.please_enter_pickup_description')}}" },
                        'delivery_point[address]': { required: "{{__('message.please_enter_delivery_address')}}" },
                        'delivery_point[contact_number]': { required: "{{__('message.please_enter_delivery_contact_number')}}" },
                        'delivery_point[description]': { required: "{{__('message.please_enter_delivery_description')}}" },
                    },
                    errorPlacement: function(error, element) {
                        error.addClass('text-danger');
                        if (element.attr("name") == "pickup_start_time") {
                            $('#form_validation_pickup_start_time').prepend(error);
                        } else if (element.attr("name") == "pickup_end_time") {
                            $('#form_validation_pickup_end_time').prepend(error);
                        } else if(element.attr("name") == "delivery_start_time"){
                            $('#form_validation_delivery_start_time').prepend(error);
                        } else if(element.attr("name") == "delivery_end_time"){
                            $('#form_validation_delivery_end_time').prepend(error);
                        } else {
                            error.insertAfter(element);
                        }
                    },
                    highlight: function(element) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function(element) {
                        $(element).removeClass('is-invalid');
                    }
                });

                $('.select2js').on('change', function() {
                    $(this).valid();
                });

                $.validator.addMethod('greaterThanEqual', function(value, element, param) {
                    var startTime = $(param).val();
                    if (!value || !startTime) {
                        return true;
                    }
                    return new Date('1970/01/01 ' + value) > new Date('1970/01/01 ' + startTime);
                });

                $(document).on('change', '#country_id', function() {
                    var country_id = $(this).val();
                    $('#city_id').empty().append('<option value="" selected disabled>' + $('#city_id').data('placeholder') + '</option>');
                    cityList(country_id);
                });

                function initAutocomplete() {
                    var pickupInput = document.getElementById('pickup_location');
                    var deliveryInput = document.getElementById('delivery_location');

                    var pickupAutocomplete = new google.maps.places.Autocomplete(pickupInput);
                    var deliveryAutocomplete = new google.maps.places.Autocomplete(deliveryInput);

                    pickupAutocomplete.addListener('place_changed', function() {
                        var place = pickupAutocomplete.getPlace();
                        document.getElementById('pickup_lat').value = place.geometry.location.lat();
                        document.getElementById('pickup_lng').value = place.geometry.location.lng();
                        calculateDistance();
                    });

                    deliveryAutocomplete.addListener('place_changed', function() {
                        var place = deliveryAutocomplete.getPlace();
                        document.getElementById('delivery_lat').value = place.geometry.location.lat();
                        document.getElementById('delivery_lng').value = place.geometry.location.lng();
                        calculateDistance();
                    });
                }

                var MILES_PER_KM = 0.621371;

                function calculateDistance() {
                    var pickupLat = parseFloat(document.getElementById('pickup_lat').value);
                    var pickupLng = parseFloat(document.getElementById('pickup_lng').value);
                    var deliveryLat = parseFloat(document.getElementById('delivery_lat').value);
                    var deliveryLng = parseFloat(document.getElementById('delivery_lng').value);

                    var pickup = new google.maps.LatLng(pickupLat, pickupLng);
                    var delivery = new google.maps.LatLng(deliveryLat, deliveryLng);

                    var service = new google.maps.DistanceMatrixService();
                    service.getDistanceMatrix({
                        origins: [pickup],
                        destinations: [delivery],
                        travelMode: 'DRIVING',
                    }, function(response, status) {
                        if (status === 'OK') {
                            var distanceInKm = response.rows[0].elements[0].distance.value / 1000;
                            var totalDistanceMiles = MILES_PER_KM * distanceInKm;
                            var duration = response.rows[0].elements[0].duration.text;
                            document.getElementById('total_distance').value = distanceInKm.toFixed(2);

                            distanceCharge(distanceInKm);
                        } else {
                            console.error('Error:', status);
                        }
                    });
                }
                var minKm = 0;
                var perKmCharge = 0;
                var price = 0;
                var vehicle_charge = 0;

                $('#vehicle_id').on('select2:select', function(e) {
                    var selectedData = e.params.data;
                    var minKm = parseFloat(selectedData.min_km) || 0;
                    var perKmCharge = parseFloat(selectedData.per_km_charge) || 0;
                    var price = parseFloat(selectedData.price) || 0;
                    var distanceInKm = parseFloat(document.getElementById('total_distance').value) || 0;

                    vehicle(distanceInKm, minKm, perKmCharge, price);
                });

                function vehicle(distanceInKm, minKm, perKmCharge, price) {
                    var vehicle_charge;
                    var is_vehicle_in_order = {{ $is_vehicle_in_order }};

                    if (is_vehicle_in_order != 0) {
                        if (minKm === 0) {
                            vehicle_charge = price;
                        } else if (distanceInKm > minKm) {
                            vehicle_charge = (distanceInKm - minKm) * perKmCharge;
                        } else {
                            vehicle_charge = price;
                        }
                    } else {
                        vehicle_charge = price;
                    }

                    $('#vehicle_charge').val(vehicle_charge.toFixed(2));
                    $('#display_vehicle_charge').text(vehicle_charge.toFixed(2));

                    recalculateTotal(distanceInKm);
                }

                initAutocomplete();
                $('form').submit(function() {
                    var pickupDate = $('[name="pickup_date"]').val();
                    var pickupTime = $('[name="pickup_start_time"]').val();
                    var pickupEndTime = $('[name="pickup_end_time"]').val();

                    var combinedDateTime = pickupDate + ' ' + pickupTime;
                    var combinedDateEndTime = pickupDate + ' ' + pickupEndTime;

                    $('#combined_pickup_start_time').val(combinedDateTime);
                    $('#combined_pickup_end_time').val(combinedDateEndTime);

                    var deliveryDate = $('[name="delivery_date"]').val();
                    var deliveryTime = $('[name="delivery_start_time"]').val();
                    var deliveryEndTime = $('[name="delivery_end_time"]').val();

                    combinedDateTime = deliveryDate + ' ' + deliveryTime;
                    combinedDateEndTime = deliveryDate + ' ' + deliveryEndTime;

                    $('#combined_delivery_start_time').val(combinedDateTime);
                    $('#combined_delivery_end_time').val(combinedDateEndTime);

                    return true;
                });
            });

            function cityList(country_id) {
                console.log(country_id);
                
                var section_class_route = "{{ route('ajax-list', ['type' => 'country_base_city', 'country_id' => '']) }}" + country_id;
                section_class_route = section_class_route.replace('amp;', '');

                $.ajax({
                    url: section_class_route,
                    success: function(result) {
                        $('#city_id').select2({
                            width: '100%',
                            placeholder: "{{ __('message.select_name',['select' => __('message.city')]) }}",
                            data: result.results
                        });
                    }
                });
            }
            var distanceInKm = parseFloat(document.getElementById('total_distance').value) || 0;

            const $checkbox = $('#add_courier_insurance');
            const $insuranceContainer = $('#insurance_container');
            const $insurancecolumn = $('.insurance_charge_col');

            function toggleInsurance() {
                if ($checkbox.is(':checked')) {
                    $insuranceContainer.show();
                    $insurancecolumn.show();
                } else {
                    $insuranceContainer.hide();
                    $insurancecolumn.hide();
                }
            }
            toggleInsurance();
            $checkbox.on('change', toggleInsurance);

            $('#add_courier_insurance').on('change', function() {
                var currency = {!! json_encode($currency) !!};
                var vehicle_charge = parseFloat($('#vehicle_charge').val()) || 0;
                var fixed_charges = parseFloat($('#fixed_charges').val()) || 0;
                var weight_charge = parseFloat($('#weight_charge').val()) || 0;
                var distance_charge = parseFloat($('#distance_charge').val()) || 0;


                var totalAmount = fixed_charges + weight_charge + distance_charge + vehicle_charge;
                var extraCharges = JSON.parse($('#extra_charges').val() || '[]');
                    var extraChargeTotal = 0;
                    let tableBody = '';
                    let totalExtraCharge = 0;

                    extraCharges.forEach(function(charge) {
                        if (charge.status == 1) {
                            let chargeValue = 0;

                            if (charge.charges_type === 'fixed') {
                                chargeValue = parseFloat(charge.charges) || 0;
                            } else if (charge.charges_type === 'percentage') {
                                chargeValue = (totalAmount * (parseFloat(charge.charges) || 0) / 100);
                            }

                            totalAmount += chargeValue;

                            tableBody += '<tr><td>' + (charge.title ? charge.title : 'N/A') + '</td><td>$' + chargeValue.toFixed(2) + '</td></tr>';
                        }
                    });


                $('#dynamic_charges').html(tableBody);

                if ($(this).is(':checked')) {
                    $('#display_total_amount').text(currency+(totalAmount + is_insurance_percentage).toFixed(2));

                } else {
                    $('#display_total_amount').text( currency+(totalAmount).toFixed(2));
                    $('#display_insurance_charge').text('0.00');
                    $('#insurance_text').val('');
                }


            });
            $('#insurance_text').on('keyup', function() {
                if ($('#add_courier_insurance').is(':checked')) {
                recalculateTotal();
            }
            });

            function recalculateTotal() {
                var currency = {!! json_encode($currency) !!};
                var vehicle_charge = parseFloat($('#vehicle_charge').val()) || 0;
                var fixed_charges = parseFloat($('#fixed_charges').val()) || 0;
                var weight_charge = parseFloat($('#weight_charge').val()) || 0;
                var is_insurance_percentage = {{ $is_insurance_percentage  ?? 0}};
                var distance_charge = parseFloat($('#distance_charge').val()) || 0;
                var totalAmount = fixed_charges + weight_charge + distance_charge + vehicle_charge;
                var insurance_value = parseFloat($('#insurance_text').val()) || 0;
                var display_insurance_charge_sum = parseFloat($('#display_insurance_charge_sum').val()) || 0;
                var insurance_charge = (insurance_value * is_insurance_percentage) / 100;
                var grandTotal = totalAmount + insurance_charge;

                var extraCharges = JSON.parse($('#extra_charges').val() || '[]');
                var extraChargeTotal = 0;
                let tableBody = '';
                let totalExtraCharge = 0;

                extraCharges.forEach(function(charge) {
                    if (charge.status == 1) {
                        let chargeValue = 0;

                        if (charge.charges_type === 'fixed') {
                            chargeValue = parseFloat(charge.charges) || 0;
                        } else if (charge.charges_type === 'percentage') {
                            chargeValue = (grandTotal * (parseFloat(charge.charges) || 0) / 100);
                        }

                        totalExtraCharge += chargeValue;

                        tableBody += '<tr><td>' + (charge.title ? charge.title : 'N/A') + '</td><td>' + currency + chargeValue.toFixed(2) + '</td></tr>';
                    }
                });


                $('#dynamic_charges').html(tableBody);

                $('#insurance_charge').val( (insurance_charge).toFixed(2));
                $('#total_amount').val((totalAmount + insurance_charge + totalExtraCharge).toFixed(2));

                updateDisplayValues();
            }

            $('#city_id').on('select2:select', function(e) {
                var selectedData = e.params.data;
                var section_class_route = "{{ route('ajax-list', ['type' => 'vehicle-list', 'city_id' => '']) }}" + selectedData.id;
                section_class_route = section_class_route.replace('amp;', '');
                $('#vehicle_id').empty().append('<option value="" selected disabled>' + $('#vehicle_id').data('placeholder') + '</option>');

                $.ajax({
                    url: section_class_route,
                    success: function(result) {
                        $('#vehicle_id').select2({
                            width: '100%',
                            placeholder: "{{ __('message.select_name',['select' => __('message.vehicle')]) }}",
                            data: result.results
                        });
                    }
                });

                var currency = {!! json_encode($currency) !!};

                var extraCharges = selectedData.extra_charges || [];
                var extraChargesStatus = extraCharges.filter(charge => charge.status === 1);
                var fixedCharges = parseFloat(selectedData.fixed_charges) || 0;
                        var perWeightCharges = parseFloat(selectedData.per_weight_charges) || 0;
                        var min_weight = parseFloat(selectedData.min_weight) || 0;
                        var min_distance = parseFloat(selectedData.min_distance) || 0;
                        var perDistanceCharges = parseFloat(selectedData.per_distance_charges) || 0;

                var extracharges = [
                            { 'title': 'fixed_charges', 'charges': fixedCharges},
                            { 'title': 'per_weight_charges', 'charges': perWeightCharges},
                            { 'title': 'min_weight', 'charges': min_weight},
                            { 'title': 'min_distance', 'charges': min_distance},
                            { 'title': 'per_distance_charges', 'charges': perDistanceCharges}
                        ];

                var extrachargemerge = extraChargesStatus.concat(extracharges);
                $('#extra_charges').val(JSON.stringify(extrachargemerge));
                calculateCharges(selectedData);

            });
            $('#total_weight').on('input', function() {
                var selectedData = $('#city_id').select2('data')[0];
                if (selectedData) {
                    calculateCharges(selectedData);
                }
            });
            $('#pickup_location, #delivery_location').on('change', function() {
                var selectedData = $('#city_id').select2('data')[0];
                if (selectedData) {
                    calculateCharges(selectedData);
                    distanceCharge(selectedData);
                }
            });

            function calculateCharges(selectedData) {
                var total_weight = parseFloat($('#total_weight').val()) || 0;
                var weight_charge = 0;
                if (total_weight > (parseFloat(selectedData.min_weight) || 0)) {
                    weight_charge = (total_weight - parseFloat(selectedData.min_weight)) * (parseFloat(selectedData.per_weight_charges) || 0);
                }


                var distance_charge = (distanceInKm > selectedData.min_distance)? (distanceInKm - selectedData.min_distance) * selectedData.per_distance_charges: 0;


                    $('#fixed_charges').val((parseFloat(selectedData.fixed_charges) || 0).toFixed(2));
                    $('#weight_charge').val(weight_charge.toFixed(2));
                    //  $('#distance_charge').val(distance_charge.toFixed(2));
                    //  $('#display_distance_charge').text((parseFloat($('#distance_charge').val()) || 0).toFixed(2));

                    recalculateTotal();

            }
            function distanceCharge(distanceInKm){
                var currency = {!! json_encode($currency) !!};
                var selectedData = $('#city_id').select2('data')[0];
                var distance_charge = (distanceInKm > selectedData.min_distance)? (distanceInKm - selectedData.min_distance) * selectedData.per_distance_charges: 0;
                $('#distance_charge').val(distance_charge.toFixed(2));
            $('#display_distance_charge').text(currency + (parseFloat($('#distance_charge').val()) || 0).toFixed(2));
                recalculateTotal();
            }


            $('#vehicle_id').on('select2:select', function(e) {
                var currency = {!! json_encode($currency) !!};
                var selectedcity = $('#city_id').select2('data')[0];
                var distance_charge = 0;
                var selectedData = e.params.data;
                var distanceInKm = parseFloat($('#vehicle_charge').val()) || 0;
                $('#distance_charge').val(distance_charge.toFixed(2));
            $('#display_distance_charge').text(currency + (parseFloat($('#distance_charge').val()) || 0).toFixed(2));
                recalculateTotal();
            });

            function updateDisplayValues() {
                var currency = {!! json_encode($currency) !!};
                var is_insurance_percentage = {{ $is_insurance_percentage  ?? 0}};
                var insurance_value = parseFloat($('#insurance_text').val()) || 0;
                var insurance_charge = (insurance_value * is_insurance_percentage) / 100;
                $('#display_fixed_charges').text(currency + (parseFloat($('#fixed_charges').val()) || 0).toFixed(2));
                $('#display_weight_charge').text(currency + (parseFloat($('#weight_charge').val()) || 0).toFixed(2));
                $('#display_distance_charge').text(currency + (parseFloat($('#distance_charge').val()) || 0).toFixed(2));
                $('#display_vehicle_charge').text(currency + (parseFloat($('#vehicle_charge').val()) || 0).toFixed(2));
                $('#display_insurance_charge').text(currency +(insurance_charge).toFixed(2));
                $('#display_total_amount').text(currency + (parseFloat($('#total_amount').val()) || 0).toFixed(2));
            }

            recalculateTotal();
            updateDisplayValues();

            function setParcelType(label) {
                document.getElementById('parcel_type_text').value = label;
            }

            $("#parcel_type").mouseover(function() {
                $("#parcel_type_container").show();
            });

            $("#parcel_type").mouseleave(function() {
                $("#parcel_type_container").hide();
            })

        </script>
    @endsection
</x-master-layout>
