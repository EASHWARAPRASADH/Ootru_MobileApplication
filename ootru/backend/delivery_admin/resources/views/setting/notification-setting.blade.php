
<div>
    {!! html()->modelForm($notification_setting_data ?? null, 'POST', route('order-setting-save'))->open() !!}
    {!! html()->hidden('id', isset($notification_setting_data[0]) ? $notification_setting_data[0]['id'] : null) !!}

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                    <div class="card-header">
                        <h4>{{__('message.notification_settings')}}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <table class="table border-less">
                                <tr class="table-active">
                                    <th class="text-left"><b>{{__('message.type')}}</b></th>
                                    <th class="text-right w-50">{{__('message.one_signal')}}</th>
                                    <th class="text-right">{{__('message.firebase')}}</th>
                                </tr>
                                <tr class="border-bottom-0">
                                    <td class="text-left">{{ __('message.active') }}</td>
                                    <td class="text-right">
                                        <div class="custom-checkbox m-2 float-right">
                                            {!! html()->hidden('notification_settings[active][IS_ONESIGNAL_NOTIFICATION]', 0) !!}
                                            {!! html()->checkbox('notification_settings[active][IS_ONESIGNAL_NOTIFICATION]', isset($notification_setting_data['active']['IS_ONESIGNAL_NOTIFICATION']) ? $notification_setting_data['active']['IS_ONESIGNAL_NOTIFICATION'] : null, 1)
                                                ->class('custom-control-input')
                                                ->id('active') !!}
                                            {!! html()->label('<span class="text-danger"></span>', 'active')->class('custom-control-label')->for('active') !!}
                                        </div>
                                    </td>
                                
                                    <td style="text-align: right;">
                                        <div class="custom-checkbox m-2 float-right">
                                            {!! html()->hidden('notification_settings[active][IS_FIREBASE_NOTIFICATION]', 0) !!}
                                            {!! html()->checkbox('notification_settings[active][IS_FIREBASE_NOTIFICATION]', isset($notification_setting_data['active']['IS_FIREBASE_NOTIFICATION']) ? $notification_setting_data['active']['IS_FIREBASE_NOTIFICATION'] : null, 1)
                                                ->class('custom-control-input')
                                                ->id('active1') !!}
                                            {!! html()->label('<span class="text-danger"></span>', 'active1')
                                                ->class('custom-control-label')
                                                ->for('active1') !!}
                                        </div>
                                    </td>
                                </tr>
                                
                                <tr class="border-bottom-0">
                                    <td class="text-left">{{ __('message.cancel') }}</td>
                                    <td class="text-right">
                                        <div class="custom-checkbox m-2 float-right">
                                            {!! html()->hidden('notification_settings[cancel][IS_ONESIGNAL_NOTIFICATION]', 0) !!}
                                            {!! html()->checkbox('notification_settings[cancel][IS_ONESIGNAL_NOTIFICATION]', isset($notification_setting_data['cancel']['IS_ONESIGNAL_NOTIFICATION']) ? $notification_setting_data['cancel']['IS_ONESIGNAL_NOTIFICATION'] : null, 1)
                                                ->class('custom-control-input')
                                                ->id('cancel') !!}
                                            {!! html()->label('<span class="text-danger"></span>', 'cancel')
                                                ->class('custom-control-label')
                                                ->for('cancel') !!}
                                        </div>
                                    </td>
                                
                                    <td style="text-align: right;">
                                        <div class="custom-checkbox m-2 float-right">
                                            {!! html()->hidden('notification_settings[cancel][IS_FIREBASE_NOTIFICATION]', 0) !!}
                                            {!! html()->checkbox('notification_settings[cancel][IS_FIREBASE_NOTIFICATION]', isset($notification_setting_data['cancel']['IS_FIREBASE_NOTIFICATION']) ? $notification_setting_data['cancel']['IS_FIREBASE_NOTIFICATION'] : null, 1)
                                                ->class('custom-control-input')
                                                ->id('cancel1') !!}
                                            {!! html()->label('<span class="text-danger"></span>', 'cancel1')
                                                ->class('custom-control-label')
                                                ->for('cancel1') !!}
                                        </div>
                                    </td>
                                </tr>
                                
                                <tr class="border-bottom-0">
                                    <td class="text-left">{{ __('message.completed') }}</td>
                                    <td class="text-right">
                                        <div class="custom-checkbox m-2 float-right">
                                            {!! html()->hidden('notification_settings[completed][IS_ONESIGNAL_NOTIFICATION]', 0) !!}
                                            {!! html()->checkbox('notification_settings[completed][IS_ONESIGNAL_NOTIFICATION]', isset($notification_setting_data['completed']['IS_ONESIGNAL_NOTIFICATION']) ? $notification_setting_data['completed']['IS_ONESIGNAL_NOTIFICATION'] : null, 1)
                                                ->class('custom-control-input')
                                                ->id('completed') !!}
                                            {!! html()->label('<span class="text-danger"></span>', 'completed')
                                                ->class('custom-control-label')
                                                ->for('completed') !!}
                                        </div>
                                    </td>
                                
                                    <td style="text-align: right;">
                                        <div class="custom-checkbox m-2 float-right">
                                            {!! html()->hidden('notification_settings[completed][IS_FIREBASE_NOTIFICATION]', 0) !!}
                                            {!! html()->checkbox('notification_settings[completed][IS_FIREBASE_NOTIFICATION]', isset($notification_setting_data['completed']['IS_FIREBASE_NOTIFICATION']) ? $notification_setting_data['completed']['IS_FIREBASE_NOTIFICATION'] : null, 1)
                                                ->class('custom-control-input')
                                                ->id('completed1') !!}
                                            {!! html()->label('<span class="text-danger"></span>', 'completed1')
                                                ->class('custom-control-label')
                                                ->for('completed1') !!}
                                        </div>
                                    </td>
                                </tr>
                                
                                <tr class="border-bottom-0">
                                    <td class="text-left">{{ __('message.courier_arrived') }}</td>
                                    <td class="text-right">
                                        <div class="custom-checkbox m-2 float-right">
                                            {!! html()->hidden('notification_settings[courier_arrived][IS_ONESIGNAL_NOTIFICATION]', 0) !!}
                                            {!! html()->checkbox('notification_settings[courier_arrived][IS_ONESIGNAL_NOTIFICATION]', isset($notification_setting_data['courier_arrived']['IS_ONESIGNAL_NOTIFICATION']) ? $notification_setting_data['courier_arrived']['IS_ONESIGNAL_NOTIFICATION'] : null, 1)
                                                ->class('custom-control-input')
                                                ->id('courier_arrived') !!}
                                            {!! html()->label('<span class="text-danger"></span>', 'courier_arrived')
                                                ->class('custom-control-label')
                                                ->for('courier_arrived') !!}
                                        </div>
                                    </td>
                                
                                    <td style="text-align: right;">
                                        <div class="custom-checkbox m-2 float-right">
                                            {!! html()->hidden('notification_settings[courier_arrived][IS_FIREBASE_NOTIFICATION]', 0) !!}
                                            {!! html()->checkbox('notification_settings[courier_arrived][IS_FIREBASE_NOTIFICATION]', isset($notification_setting_data['courier_arrived']['IS_FIREBASE_NOTIFICATION']) ? $notification_setting_data['courier_arrived']['IS_FIREBASE_NOTIFICATION'] : null, 1)
                                                ->class('custom-control-input')
                                                ->id('courier_arrived1') !!}
                                            {!! html()->label('<span class="text-danger"></span>', 'courier_arrived1')
                                                ->class('custom-control-label')
                                                ->for('courier_arrived1') !!}
                                        </div>
                                    </td>
                                </tr>
                                

                                <tr class="border-bottom-0">
                                    <td class="text-left">{{ __('message.courier_assigned') }}</td>
                                    <td class="text-right">
                                        <div class="custom-checkbox m-2 float-right">
                                            {!! html()->hidden('notification_settings[courier_assigned][IS_ONESIGNAL_NOTIFICATION]', 0) !!}
                                            {!! html()->checkbox('notification_settings[courier_assigned][IS_ONESIGNAL_NOTIFICATION]', isset($notification_setting_data['courier_assigned']['IS_ONESIGNAL_NOTIFICATION']) ? $notification_setting_data['courier_assigned']['IS_ONESIGNAL_NOTIFICATION'] : null, 1)
                                                ->class('custom-control-input')
                                                ->id('courier_assigned') !!}
                                            {!! html()->label('<span class="text-danger"></span>', 'courier_assigned')
                                                ->class('custom-control-label')
                                                ->for('courier_assigned') !!}
                                        </div>
                                    </td>
                                
                                    <td class="text-right">
                                        <div class="custom-checkbox m-2 float-right">
                                            {!! html()->hidden('notification_settings[courier_assigned][IS_FIREBASE_NOTIFICATION]', 0) !!}
                                            {!! html()->checkbox('notification_settings[courier_assigned][IS_FIREBASE_NOTIFICATION]', isset($notification_setting_data['courier_assigned']['IS_FIREBASE_NOTIFICATION']) ? $notification_setting_data['courier_assigned']['IS_FIREBASE_NOTIFICATION'] : null, 1)
                                                ->class('custom-control-input')
                                                ->id('courier_assigned1') !!}
                                            {!! html()->label('<span class="text-danger"></span>', 'courier_assigned1')
                                                ->class('custom-control-label')
                                                ->for('courier_assigned1') !!}
                                        </div>
                                    </td>
                                </tr>
                                
                                <tr class="border-bottom-0">
                                    <td class="text-left">{{ __('message.departed_assigned') }}</td>
                                    <td class="text-right">
                                        <div class="custom-checkbox m-2 float-right">
                                            {!! html()->hidden('notification_settings[departed_assigned][IS_ONESIGNAL_NOTIFICATION]', 0) !!}
                                            {!! html()->checkbox('notification_settings[departed_assigned][IS_ONESIGNAL_NOTIFICATION]', isset($notification_setting_data['departed_assigned']['IS_ONESIGNAL_NOTIFICATION']) ? $notification_setting_data['departed_assigned']['IS_ONESIGNAL_NOTIFICATION'] : null, 1)
                                                ->class('custom-control-input')
                                                ->id('departed_assigned') !!}
                                            {!! html()->label('<span class="text-danger"></span>', 'departed_assigned')
                                                ->class('custom-control-label')
                                                ->for('departed_assigned') !!}
                                        </div>
                                    </td>
                                
                                    <td class="text-right">
                                        <div class="custom-checkbox m-2 float-right">
                                            {!! html()->hidden('notification_settings[departed_assigned][IS_FIREBASE_NOTIFICATION]', 0) !!}
                                            {!! html()->checkbox('notification_settings[departed_assigned][IS_FIREBASE_NOTIFICATION]', isset($notification_setting_data['departed_assigned']['IS_FIREBASE_NOTIFICATION']) ? $notification_setting_data['departed_assigned']['IS_FIREBASE_NOTIFICATION'] : null, 1)
                                                ->class('custom-control-input')
                                                ->id('departed_assigned1') !!}
                                            {!! html()->label('<span class="text-danger"></span>', 'departed_assigned1')
                                                ->class('custom-control-label')
                                                ->for('departed_assigned1') !!}
                                        </div>
                                    </td>
                                </tr>
                                
                                <tr class="border-bottom-0">
                                    <td class="text-left">{{ __('message.courier_pickup_up') }}</td>
                                    <td class="text-right">
                                        <div class="custom-checkbox m-2 float-right">
                                            {!! html()->hidden('notification_settings[courier_pickup_up][IS_ONESIGNAL_NOTIFICATION]', 0) !!}
                                            {!! html()->checkbox('notification_settings[courier_pickup_up][IS_ONESIGNAL_NOTIFICATION]', isset($notification_setting_data['courier_pickup_up']['IS_ONESIGNAL_NOTIFICATION']) ? $notification_setting_data['courier_pickup_up']['IS_ONESIGNAL_NOTIFICATION'] : null, 1)
                                                ->class('custom-control-input')
                                                ->id('courier_pickup_up') !!}
                                            {!! html()->label('<span class="text-danger"></span>', 'courier_pickup_up')
                                                ->class('custom-control-label')
                                                ->for('courier_pickup_up') !!}
                                        </div>
                                    </td>
                                
                                    <td class="text-right">
                                        <div class="custom-checkbox m-2 float-right">
                                            {!! html()->hidden('notification_settings[courier_pickup_up][IS_FIREBASE_NOTIFICATION]', 0) !!}
                                            {!! html()->checkbox('notification_settings[courier_pickup_up][IS_FIREBASE_NOTIFICATION]', isset($notification_setting_data['courier_pickup_up']['IS_FIREBASE_NOTIFICATION']) ? $notification_setting_data['courier_pickup_up']['IS_FIREBASE_NOTIFICATION'] : null, 1)
                                                ->class('custom-control-input')
                                                ->id('courier_pickup_up1') !!}
                                            {!! html()->label('<span class="text-danger"></span>', 'courier_pickup_up1')
                                                ->class('custom-control-label')
                                                ->for('courier_pickup_up1') !!}
                                        </div>
                                    </td>
                                </tr>
                                
                                <tr class="border-bottom-0">
                                    <td class="text-left">{{ __('message.courier_transfer') }}</td>
                                    <td class="text-right">
                                        <div class="custom-checkbox m-2 float-right">
                                            {!! html()->hidden('notification_settings[courier_transfer][IS_ONESIGNAL_NOTIFICATION]', 0) !!}
                                            {!! html()->checkbox('notification_settings[courier_transfer][IS_ONESIGNAL_NOTIFICATION]', isset($notification_setting_data['courier_transfer']['IS_ONESIGNAL_NOTIFICATION']) ? $notification_setting_data['courier_transfer']['IS_ONESIGNAL_NOTIFICATION'] : null, 1)
                                                ->class('custom-control-input')
                                                ->id('courier_transfer') !!}
                                            {!! html()->label('<span class="text-danger"></span>', 'courier_transfer')
                                                ->class('custom-control-label')
                                                ->for('courier_transfer') !!}
                                        </div>
                                    </td>
                                
                                    <td class="text-right">
                                        <div class="custom-checkbox m-2 float-right">
                                            {!! html()->hidden('notification_settings[courier_transfer][IS_FIREBASE_NOTIFICATION]', 0) !!}
                                            {!! html()->checkbox('notification_settings[courier_transfer][IS_FIREBASE_NOTIFICATION]', isset($notification_setting_data['courier_transfer']['IS_FIREBASE_NOTIFICATION']) ? $notification_setting_data['courier_transfer']['IS_FIREBASE_NOTIFICATION'] : null, 1)
                                                ->class('custom-control-input')
                                                ->id('courier_transfer1') !!}
                                            {!! html()->label('<span class="text-danger"></span>', 'courier_transfer1')
                                                ->class('custom-control-label')
                                                ->for('courier_transfer1') !!}
                                        </div>
                                    </td>
                                </tr>
                                
                                <tr class="border-bottom-0">
                                    <td class="text-left">{{ __('message.create') }}</td>
                                    <td class="text-right">
                                        <div class="custom-checkbox m-2 float-right">
                                            {!! html()->hidden('notification_settings[create][IS_ONESIGNAL_NOTIFICATION]', 0) !!}
                                            {!! html()->checkbox('notification_settings[create][IS_ONESIGNAL_NOTIFICATION]', isset($notification_setting_data['create']['IS_ONESIGNAL_NOTIFICATION']) ? $notification_setting_data['create']['IS_ONESIGNAL_NOTIFICATION'] : null, 1)
                                                ->class('custom-control-input')
                                                ->id('create') !!}
                                            {!! html()->label('<span class="text-danger"></span>', 'create')
                                                ->class('custom-control-label')
                                                ->for('create') !!}
                                        </div>
                                    </td>
                                
                                    <td class="text-right">
                                        <div class="custom-checkbox m-2 float-right">
                                            {!! html()->hidden('notification_settings[create][IS_FIREBASE_NOTIFICATION]', 0) !!}
                                            {!! html()->checkbox('notification_settings[create][IS_FIREBASE_NOTIFICATION]', isset($notification_setting_data['create']['IS_FIREBASE_NOTIFICATION']) ? $notification_setting_data['create']['IS_FIREBASE_NOTIFICATION'] : null, 1)
                                                ->class('custom-control-input')
                                                ->id('create1') !!}
                                            {!! html()->label('<span class="text-danger"></span>', 'create1')
                                                ->class('custom-control-label')
                                                ->for('create1') !!}
                                        </div>
                                    </td>
                                </tr>
                                
                                <tr class="border-bottom-0">
                                    <td class="text-left">{{ __('message.delayed') }}</td>
                                    <td class="text-right">
                                        <div class="custom-checkbox m-2 float-right">
                                            {!! html()->hidden('notification_settings[delayed][IS_ONESIGNAL_NOTIFICATION]', 0) !!}
                                            {!! html()->checkbox('notification_settings[delayed][IS_ONESIGNAL_NOTIFICATION]', isset($notification_setting_data['delayed']['IS_ONESIGNAL_NOTIFICATION']) ? $notification_setting_data['delayed']['IS_ONESIGNAL_NOTIFICATION'] : null, 1)
                                                ->class('custom-control-input')
                                                ->id('delayed') !!}
                                            {!! html()->label('<span class="text-danger"></span>', 'delayed')
                                                ->class('custom-control-label')
                                                ->for('delayed') !!}
                                        </div>
                                    </td>
                                
                                    <td class="text-right">
                                        <div class="custom-checkbox m-2 float-right">
                                            {!! html()->hidden('notification_settings[delayed][IS_FIREBASE_NOTIFICATION]', 0) !!}
                                            {!! html()->checkbox('notification_settings[delayed][IS_FIREBASE_NOTIFICATION]', isset($notification_setting_data['delayed']['IS_FIREBASE_NOTIFICATION']) ? $notification_setting_data['delayed']['IS_FIREBASE_NOTIFICATION'] : null, 1)
                                                ->class('custom-control-input')
                                                ->id('delayed1') !!}
                                            {!! html()->label('<span class="text-danger"></span>', 'delayed1')
                                                ->class('custom-control-label')
                                                ->for('delayed1') !!}
                                        </div>
                                    </td>
                                </tr>
                                
                                <tr class="border-bottom-0">
                                    <td class="text-left">{{ __('message.failed') }}</td>
                                    <td class="text-right">
                                        <div class="custom-checkbox m-2 float-right">
                                            {!! html()->hidden('notification_settings[failed][IS_ONESIGNAL_NOTIFICATION]', 0) !!}
                                            {!! html()->checkbox('notification_settings[failed][IS_ONESIGNAL_NOTIFICATION]', isset($notification_setting_data['failed']['IS_ONESIGNAL_NOTIFICATION']) ? $notification_setting_data['failed']['IS_ONESIGNAL_NOTIFICATION'] : null, 1)
                                                ->class('custom-control-input')
                                                ->id('failed') !!}
                                            {!! html()->label('<span class="text-danger"></span>', 'failed')
                                                ->class('custom-control-label')
                                                ->for('failed') !!}
                                        </div>
                                    </td>
                                
                                    <td class="text-right">
                                        <div class="custom-checkbox m-2 float-right">
                                            {!! html()->hidden('notification_settings[failed][IS_FIREBASE_NOTIFICATION]', 0) !!}
                                            {!! html()->checkbox('notification_settings[failed][IS_FIREBASE_NOTIFICATION]', isset($notification_setting_data['failed']['IS_FIREBASE_NOTIFICATION']) ? $notification_setting_data['failed']['IS_FIREBASE_NOTIFICATION'] : null, 1)
                                                ->class('custom-control-input')
                                                ->id('failed1') !!}
                                            {!! html()->label('<span class="text-danger"></span>', 'failed1')
                                                ->class('custom-control-label')
                                                ->for('failed1') !!}
                                        </div>
                                    </td>
                                </tr>
                                
                                <tr class="border-bottom-0">
                                    <td class="text-left">{{ __('message.payment_status_message') }}</td>
                                    <td class="text-right">
                                        <div class="custom-checkbox m-2 float-right">
                                            {!! html()->hidden('notification_settings[payment_status_message][IS_ONESIGNAL_NOTIFICATION]', 0) !!}
                                            {!! html()->checkbox('notification_settings[payment_status_message][IS_ONESIGNAL_NOTIFICATION]', isset($notification_setting_data['payment_status_message']['IS_ONESIGNAL_NOTIFICATION']) ? $notification_setting_data['payment_status_message']['IS_ONESIGNAL_NOTIFICATION'] : null, 1)
                                                ->class('custom-control-input')
                                                ->id('payment_status_message') !!}
                                            {!! html()->label('<span class="text-danger"></span>', 'payment_status_message')
                                                ->class('custom-control-label')
                                                ->for('payment_status_message') !!}
                                        </div>
                                    </td>
                                
                                    <td class="text-right">
                                        <div class="custom-checkbox m-2 float-right">
                                            {!! html()->hidden('notification_settings[payment_status_message][IS_FIREBASE_NOTIFICATION]', 0) !!}
                                            {!! html()->checkbox('notification_settings[payment_status_message][IS_FIREBASE_NOTIFICATION]', isset($notification_setting_data['payment_status_message']['IS_FIREBASE_NOTIFICATION']) ? $notification_setting_data['payment_status_message']['IS_FIREBASE_NOTIFICATION'] : null, 1)
                                                ->class('custom-control-input')
                                                ->id('payment_status_message1') !!}
                                            {!! html()->label('<span class="text-danger"></span>', 'payment_status_message1')
                                                ->class('custom-control-label')
                                                ->for('payment_status_message1') !!}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <hr>
                <div class="col-md-12 mt-1 mb-4">
                    {!! html()->submit(__('message.save'))->class('btn btn-md btn-primary float-md-right') !!}
                </div>
            </div>
        </div>
    </div>
    {!! html()->form()->close() !!}
</div>
@section('bottom_script')
    <script>
    </script>
@endsection


