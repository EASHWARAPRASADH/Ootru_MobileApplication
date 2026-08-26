<!-- Modal -->
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{ $pageTitle }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="form-group">
                        <div>
                            <h5>{{ __('message.help_section_title_one') }} <span class="font-weight-bold">"{{ __('message.order_data')  }}" </span>{{ __('message.help_section_title_two') }}</h5>
                        </div>
                        <div class="mt-2">
                            <div class="row">
                                <div class="col-md-12">
                                    <ul class="">
                                        <li class="mt-3">{{ __('message.help_section_title_three') }}</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <ol class="ml-4">
                                        <li>{{ __('message.parcel_type') }}</li>
                                        <li>{{ __('message.weight') }}</li>
                                        <li>{{ __('message.number_of_parcels') }}</li>
                                        <li>{{ __('message.parcel_description') }}</li>
                                        <li>{{ __('message.country') }}</li>
                                        <li>{{ __('message.city') }}</li>
                                        <li>{{ __('message.pickup_point_start_time') }}</li>
                                        <li>{{ __('message.pickup_point_end_time') }}</li>
                                        <li>{{ __('message.pickup_point_address') }}</li>
                                        <li>{{ __('message.pickup_point_latitude') }}</li>
                                        <li>{{ __('message.pickup_point_longitude') }}</li>
                                        <li>{{ __('message.pickup_point_contact_number') }}</li>
                                        <li>{{ __('message.pickup_point_description') }}</li>
                                        <li>{{ __('message.pickup_point_instruction') }}</li>
                                        <li>{{ __('message.delivery_start_time') }}</li>
                                        <li>{{ __('message.delivery_end_time') }}</li>
                                        <li>{{ __('message.delivery_address') }}</li>
                                        <li>{{ __('message.delivery_latitude') }}</li>
                                        <li>{{ __('message.delivery_longitude') }}</li>
                                        <li>{{ __('message.delivery_contact_number') }}</li>
                                        <li>{{ __('message.delivery_description') }}</li>
                                        <li>{{ __('message.delivery_instruction') }}</li>
                                        <li>{{ __('message.vehicle') }}</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <ul class="">
                                        <li class="mt-2">{{ __('message.help_section_title_four') }} <span class="font-weight-bold">"{{ __('message.order_data')  }}" </span> {{ __('message.help_section_title_five') }}</li>
                                        <li class="mt-2">{{ __('message.help_section_title_one') }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <h6 class="mb-2">{{ __('message.help_section_title_notes') }} <span class="font-weight-bold">"{{ __('message.order_data')  }}" </span> {{ __('message.help_section_title_eight') }}
                                    </h6>
                                    <h5>{{ __('message.help_section_title_notes') }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mt-2" style="width: 100%; overflow-x: auto;">
                                    <img src="{{ asset('images/orderimport.png') }}" alt="help" class="mt-1" style="width: 100%;">
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
    </div>
</div>