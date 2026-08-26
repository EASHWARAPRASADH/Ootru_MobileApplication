<div class="col-lg-12">
    {{-- <div class="card"> --}}
        <div class="card-header">
            <h4>{{__('message.order_settings')}}</h4>
        </div>
            <div class="card-body mt-3 pt-0 pb-0">
                {!! html()->modelForm($setting_value, 'POST',route('order-setting-save'))->open() !!}
                {!! html()->hidden('id',isset($setting_value[0]) ? $setting_value[0]['id'] : NULL) !!}
                    <div class="row">
                        <div class="col-lg-12">
                            @foreach($setting as $key => $value)
                                        <div class="row">
                                            @foreach($value as $sub_keys => $sub_value)
                                                @php
                                                    $data = null;
                                                    foreach($setting_value as $v){
                                                        if($v->key == $sub_keys){
                                                        }
                                                    }
                                                    $class = 'col-md-4';
                                                    $text = 'number';
                                                    $checkbox = 'checkbox';
                                                    $prefix = 'text';
                                                @endphp
                                                <div class="card card-block">
                                                    <div class="row">
                                                        <div class="col-md-12  ml-1 ,mr-2">
                                                            {!! html()->label(__('message.auto_assign_setting') . ' <span class="text-danger">*</span>')->for('auto_assign')->class('form-control-label ml-2 mt-2') !!}
                                                            <div class="form-group row">
                                                                <div class="col-md-6">
                                                                    <div class="custom-control custom-switch custom-switch-color custom-control-inline ml-2">
                                                                        {!! html()->hidden('auto_assign', 0) !!}
                                                                        @if($key == 'ORDER')
                                                                            {!! html()->checkbox('auto_assign', 1)->checked(isset($v) ? $v->auto_assign : false)->class('custom-control-input bg-success float-right')->id('switch_one') !!}
                                                                        @endif
                                                                        {!! html()->label(__('message.auto_order_assign_to_delivery_man'))->for('switch_one')->class('custom-control-label') !!}
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-5" style="margin-top: -26px;">
                                                                    {!! html()->label(__('message.delivery_man_distance_for_auto_assign_order') . ' (' . __('message.' . ($v->distance_unit ?? '')) . ') <span class="text-danger">*</span>')->for('distance')->class('form-control-label') !!}

                                                                    @if($key == 'ORDER')
                                                                        {!! html()->text('distance', isset($v) ? $v->distance : null)
                                                                            ->id($sub_keys)
                                                                            ->class('form-control form-control-lg')
                                                                            ->placeholder(str_replace('_', ' ', $sub_keys))
                                                                            ->attribute('min', 0)
                                                                            ->attribute('onKeyPress', "if(this.value.length==5) return false;") !!}
                                                                    @endif
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card col-md-12">
                                                    {!! html()->label(__('message.distance_unit') . ' <span class="text-danger">*</span>')->for('distance_unit')->class('form-control-label ml-2 mt-3') !!}
                                                    <div class="form-group col-md-12 mt-2">
                                                        <div class="custom-control custom-radio custom-control-inline col-2">
                                                            {!! html()->radio('distance_unit', old('distance_unit', isset($v) ? $v->distance_unit : null) == 'km', 'km')->class('custom-control-input')->id('km_one') !!}
                                                            {!! html()->label(__('message.km'))->for('km_one')->class('custom-control-label') !!}
                                                        </div>
                                                        <div class="custom-control custom-radio custom-control-inline col-2">
                                                            {!! html()->radio('distance_unit', old('distance_unit', isset($v) ? $v->distance_unit : null) == 'mile', 'mile')->class('custom-control-input')->id('mile_one') !!}
                                                            {!! html()->label(__('message.mile'))->for('mile_one')->class('custom-control-label') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- OTP Verification -->
                                                <div class="card col-md-12">
                                                    <div class="form-group col-md-12 mt-3">
                                                        <div class="custom-control custom-switch custom-switch-color custom-control-inline ml-2">
                                                            {!! html()->hidden('otp_verify_on_pickup_delivery', 0) !!}
                                                            @if($key == 'ORDER')
                                                                {!! html()->checkbox('otp_verify_on_pickup_delivery', 1)->checked(isset($v) ? $v->otp_verify_on_pickup_delivery : false)->class('custom-control-input bg-success')->id('switch_two') !!}
                                                            @endif
                                                            {!! html()->label(__('message.otp_verification_for_pickup_and_drop_parcel'))->for('switch_two')->class('custom-control-label') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Vehicle Enable/Disable -->
                                                <div class="card col-md-12">
                                                    <div class="form-group col-md-12 mt-3">
                                                        <div class="custom-control custom-switch custom-switch-color custom-control-inline ml-2">
                                                            {!! html()->hidden('is_vehicle_in_order', 0) !!}
                                                            @if($key == 'ORDER')
                                                                {!! html()->checkbox('is_vehicle_in_order', 1)->checked(isset($v) && $v->is_vehicle_in_order == 1)->class('custom-control-input bg-success')->id('switch_button') !!}
                                                            @endif
                                                            {!! html()->label(__('message.enable_or_disable_vehicle'))->for('switch_button')->class('custom-control-label') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Bidding Enable/Disable -->
                                                <div class="card col-md-12">
                                                    <div class="form-group col-md-12 mt-3">
                                                        <div class="custom-control custom-switch custom-switch-color custom-control-inline ml-2">
                                                            {!! html()->hidden('is_bidding_in_order', 0) !!}
                                                            @if($key == 'ORDER')
                                                                {!! html()->checkbox('is_bidding_in_order', 1)->checked(isset($v) && $v->is_bidding_in_order == 1)->class('custom-control-input bg-success')->id('switch_buttons') !!}
                                                            @endif
                                                            {!! html()->label(__('message.enable_or_disable_bidding_orders'))->for('switch_buttons')->class('custom-control-label') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- SMS Order Enable/Disable -->
                                                <div class="card col-md-12">
                                                    <div class="form-group col-md-12 mt-3">
                                                        <div class="custom-control custom-switch custom-switch-color custom-control-inline ml-2">
                                                            {!! html()->hidden('is_sms_order', 0) !!}
                                                            @if($key == 'ORDER')
                                                                {!! html()->checkbox('is_sms_order', 1)->checked(isset($v) && $v->is_sms_order == 1)->class('custom-control-input bg-success')->id('switch_smsbuttons') !!}
                                                            @endif
                                                            {!! html()->label(__('message.enable_or_disable_sms_orders'))->for('switch_smsbuttons')->class('custom-control-label') !!}
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Order Prefix -->
                                                @if($key == 'ORDER')
                                                    <div class="card col-md-12">
                                                        {!! html()->label(__('message.order_prefix'))->for('order_prefix')->class('control-label mt-2') !!}
                                                        {!! html()->text('prefix', isset($v) ? $v->prefix : null)
                                                            ->id($sub_keys)
                                                            ->class('form-control col-md-6')
                                                            ->placeholder(__('message.order_prefix'))
                                                            ->style('text-transform: uppercase;') !!}
                                                    </div>
                                                @endif
                                                
                                            @endforeach
                                        </div>
                            @endforeach
                            {!! html()->submit(__('message.save'))->class('btn btn-md btn-primary mb-3 mr-3 float-md-right') !!}
                        </div>
                    </div>
                {!! html()->form()->close() !!}
            </div>
</div>

