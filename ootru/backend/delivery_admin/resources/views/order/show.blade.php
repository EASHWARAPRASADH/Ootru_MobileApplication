<x-master-layout :assets="$assets ?? []">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-block card-stretch">
                        <div class="card-body p-0">
                            <div class="d-flex justify-content-between align-items-center p-3">
                                <h5 class="font-weight-bold">{{__('message.order_detail')}}</h5>
                                @if($data['status'] != 'completed')
                                    <div class="btn-group" style="margin-left:78%">
                                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            {{ __('message.print') }}
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#" onclick="printLabel({{ $id }})">{{ __('message.print_label') }}</a>
                                            <a class="dropdown-item" href="#" onclick="printbarcode({{ $id }})">{{ __('message.print_barcode') }}</a>
                                        </div>
                                    </div>

                                @endif

                                <div class="">
                                    @if(Auth::user()->user_type == 'admin')
                                    <a href="{{ route('order.index') }}" class="float-right ml-1 btn btn-sm btn-primary"><i class="fa fa-angle-double-left"></i> {{ __('message.back') }}</a>
                                    @else
                                    <a href="{{ route('home') }}" class="float-right ml-1 btn btn-sm btn-primary"><i class="fa fa-angle-double-left"></i> {{ __('message.back') }}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($data['status'] == 'reschedule')
                    <div class="card-body mt-0 pt-0 pb-0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-block">
                                    <div class="col-md-12 mb-3 bg-dark-danger">
                                        <h5 class="text-warning mt-2">{{ __('message.order_has_been_rechedule') . ' : ' . dateAgoFormate($data->rescheduledatetime) }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($data['is_shipped'] == 1)
                    <div class="card-body mt-0 pt-0 pb-0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-block">
                                    <div class="col-md-12 mb-3 bg-dark-danger">
                                        {{-- <h5 class="text-warning mt-2">{{ __('message.order_has_been_shipped') . '' .  __('message.via') . '' . $courierCompany->name . ' : ' . dateAgoFormate($data->shipped_verify_at)}}</h5> --}}
                                        <h5 class="text-warning mt-2">
                                            {{ __('message.order_has_been_shipped') . ' ' . __('message.via') . ' ' . optional($data->couriercompany)->name . ' : ' . dateAgoFormate($data->shipped_verify_at) }}
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if($data['bid_type'] == 1)
                <div class="card-body mt-0 pt-0 pb-0">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-block">
                                <div class="col-md-12 mb-3 bg-dark-primary">
                                    <h5 class="text-warning mt-2">{{ __('message.order_has_bids') }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-md-8">
                                <div class="card card-block">
                                    <div class="card-body mt-3 pt-0 pb-0">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h4 class="card-title mb-2 font-weight-bold">{{ __('message.parcel_details') }}</h4>
                                                <div class="card card-block">
                                                    <div class="col-md-12">
                                                        <h6 class="card-title mb-2 mt-2 float-left">{{ __('message.parcel_type') }}</h6>
                                                        <p class="mb-2 mt-2 float-right">{{ isset($data->parcel_type) ? $data->parcel_type : '-' }}</p>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <h6 class="card-title mb-2 mt-2 float-left">{{ __('message.total_weight') }}</h6>
                                                        <p class="mb-2 mt-2 float-right">{{ isset($data->total_weight) ? $data->total_weight : '-' }} {{optional($data->country)->weight_type}}</p>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <h6 class="card-title mb-2 mt-2 float-left">{{ __('message.total_parcel') }}</h6>
                                                        <p class="mb-2 mt-2 float-right">{{ isset($data->total_parcel) ? $data->total_parcel : '-' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h4 class="card-title mb-2 font-weight-bold">{{ __('message.payment_details') }}</h4>
                                                <div class="card card-block">
                                                    <div class="col-md-12">
                                                        @if(isset($data->payment) && !in_array(optional($data->payment)->payment_type, ['wallet', 'cash']))
                                                            <div class="d-flex justify-content-between mb-2">
                                                                <div>
                                                                    <strong>{{ __('message.payment_type') }}:</strong>
                                                                    <span>{{ optional($data->payment)->payment_type ?? '-' }}</span>
                                                                </div> |
                                                                <div>
                                                                    <strong>{{ __('message.txn_id') }}:</strong>
                                                                    <span>{{ optional($data->payment)->txn_id ?? '-' }}</span>
                                                                </div>
                                                            </div>  
                                                        @else
                                                            <h6 class="card-title mb-2 mt-2 float-left">{{ __('message.payment_type') }}</h6>
                                                            <p class="mb-2 mt-2 float-right">
                                                                {{ optional($data->payment)->payment_type ?? ($data->payment_type ?? '-') }}
                                                            </p>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-12">
                                                        <h6 class="card-title mb-2 mt-2 float-left">{{ __('message.payment_status') }}</h6>
                                                        <p class="mb-2 mt-2 float-right">{{ optional($data->payment)->payment_status ?? '-' }}</p>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <h6 class="card-title mb-2 mt-2 float-left">{{ __('message.payment_collect_from') }}</h6>
                                                        <p class="mb-2 mt-2 float-right">{{ isset($data->payment_collect_from) ? __('message.'.$data->payment_collect_from) : '-' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body pb-0 pt-0">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h4 class="card-title mb-2 font-weight-bold">{{ __('message.pickup_address') }}</h4>
                                                <div class="card card-block">
                                                    <div class="col-md-12">
                                                        @if($data['status'] == 'courier_picked_up' || $data['status'] == 'completed' || $data['status'] == 'courier_departed')
                                                            <p class="mb-3 mt-3"><span>{{__('message.picked')}}</span>  {{ dateAgoFormate($data->pickup_datetime) }}</p>
                                                        @endif
                                                        <p class="mb-3 mt-3 d-flex align-items-start">
                                                            <i class="fa fa-location-dot me-2"></i>
                                                            <span class="ml-2">{{ $data->pickup_point['address'] }}</span>
                                                        </p>
                                                        <h6 class=" mb-3 mt-3"> <i class="fas fa-phone-alt"></i>  {{  auth()->user()->hasRole('admin') ? maskSensitiveInfo('contact_number',$data->pickup_point['contact_number']) : maskSensitiveInfo('contact_number',$data->pickup_point['contact_number']) }}</h6>
                                                        @if(!empty($data->pickup_point['name']))
                                                            <p class="mb-2 mt-2"> <i class="fa-solid fa-user"></i> {{ $data->pickup_point['name'] }}</p>
                                                        @endif
                                                        @if(!empty($data->pickup_point['description']))
                                                            <p class="mb-2 mt-2"> <strong>{{ __('message.description') }}:</strong> {{ $data->pickup_point['description'] }}</p>
                                                        @endif
                                                        @if(!empty($data->pickup_point['instruction']))
                                                            <p class="mb-2 mt-2"> <strong>{{ __('message.instruction') }}:</strong> {{ $data->pickup_point['instruction'] }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <h4 class="card-title mb-2 font-weight-bold">{{ __('message.delivery_address') }}</h4>
                                                <div class="card card-block">
                                                    <div class="col-md-12">
                                                        @if($data['status'] == 'completed')
                                                            <p class="mb-3 mt-3"><span>{{__('message.delivered_at')}}</span>  {{  dateAgoFormate($data->delivery_datetime) }}</p>
                                                        @endif
                                                        <p class="mb-3 mt-3 d-flex align-items-start">
                                                            <i class="fa fa-location-dot me-2"></i>
                                                            <span class="ml-2">{{ $data->delivery_point['address'] }}</span>
                                                        </p>
                                                        <h6 class=" mb-3 mt-3"> <i class="fas fa-phone-alt"></i>  {{  auth()->user()->hasRole('admin') ? maskSensitiveInfo('contact_number',$data->delivery_point['contact_number']) : maskSensitiveInfo('contact_number',$data->delivery_point['contact_number']) }}</h6>
                                                        @if(!empty($data->delivery_point['name']))
                                                            <p class="mb-2 mt-2"> <i class="fa-solid fa-user"></i> {{ $data->delivery_point['name'] }}</p>
                                                        @endif
                                                        @if(!empty($data->delivery_point['description']))
                                                            <p class="mb-2 mt-2"> <strong>{{ __('message.description') }}:</strong> {{ $data->delivery_point['description'] }}</p>
                                                        @endif
                                                        @if(!empty($data->delivery_point['instruction']))
                                                        <p class="mb-2 mt-2"> <strong>{{ __('message.instruction') }}:</strong> {{ $data->delivery_point['instruction'] }}</p>
                                                    @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @if($data->packaging_symbols != null)
                                            <h4 class="card-title mb-2 font-weight-bold">{{ __('message.packaging_symbol') }}</h4>
                                            <div class="card">
                                                <div class="card-body pb-1 pt-1">
                                                    <div class="row">
                                                        @php
                                                        $packagingSymbols = json_decode($data->packaging_symbols, true);
                                                        @endphp
                                                        @if (is_array($packagingSymbols))
                                                            @foreach ($packagingSymbols as $symbol)
                                                                @php
                                                                    $icon = '';

                                                                    switch ($symbol['key']) {
                                                                        case 'fragile':
                                                                            $icon = asset('images/fragile.png');
                                                                            break;
                                                                        case 'keep_dry':
                                                                            $icon = asset('images/keep-dry.png');
                                                                            break;
                                                                        case 'this_way_up':
                                                                            $icon = asset('images/up-arrows-couple-sign-for-packaging.png');
                                                                            break;
                                                                        case 'do_not_stack':
                                                                            $icon = asset('images/do-not-stack.png');
                                                                            break;
                                                                        case 'temperature_sensitive':
                                                                            $icon = asset('images/temperature.png');
                                                                            break;
                                                                        case 'recycle':
                                                                            $icon = asset('images/symbols.png');
                                                                            break;
                                                                        case 'do_not_use_hooks':
                                                                            $icon = asset('images/do-not-hook.png');
                                                                            break;
                                                                        case 'explosive_material':
                                                                            $icon = asset('images/flammable.png');
                                                                            break;
                                                                        case 'hazardous_material':
                                                                            $icon = asset('images/hazard.png');
                                                                            break;
                                                                        case 'perishable':
                                                                            $icon = asset('images/ice-cube.png');
                                                                            break;
                                                                        case 'do_not_open_with_sharp_objects':
                                                                            $icon = asset('images/knives.png');
                                                                            break;
                                                                        case 'bike_delivery':
                                                                            $icon = asset('images/fast-delivery.png');
                                                                            break;
                                                                        default:
                                                                            $icon = '';
                                                                            break;
                                                                    }
                                                                @endphp
                                                                @if ($icon)
                                                                    <div class="col-lg-1 mb-1 mt-1">
                                                                        <div class="icon-container text-center">
                                                                            @if (strpos($icon, 'http') === 0)
                                                                                <img src="{{ $icon }}" style="width: 30px; height: auto;">
                                                                            @else
                                                                                {!! $icon !!}
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if($customerSupport->isNotEmpty())
                                        <div class="card">
                                            <div class="card-body pb-1 pt-1">
                                                <div class="row">
                                                    <div class="col-md-12 mb-1 mt-2 bg-dark-success">
                                                        <h4 class="card-title mb-2 font-weight-bold text-success">{{ __('message.customers_supports') }}</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="card-body pb-0 pt-0">
                                        <div class="row">
                                            @if ($data['status'] == 'courier_picked_up' || $data['status'] == 'completed' || $data['status'] == 'courier_departed')
                                            <div class="col-md-6">
                                                <h4 class="card-title mb-1 font-weight-bold">{{ __('message.pickup_signature') }}</h4>
                                                    @if( isset($data)&& optional($data)->hasMedia('pickup_time_signature') )
                                                    <div class="card card-block">
                                                        <div id="image-preview" class="d-flex flex-wrap col-md-12 ">
                                                            @foreach(optional($data)->getMedia('pickup_time_signature') as $media)
                                                                <div class="col-md-4 mb-2 float-left">
                                                                    <a class="magnific-popup-image-gallery" href="{{ $media->getUrl() }}" title="{{ $media->name }}">
                                                                        <img id="{{ $media->id }}_preview" src="{{ $media->getUrl() }}" alt="{{ $media->name }}" class="attachment-image mt-3">
                                                                    </a>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    @else
                                                    <div class="card card-block">
                                                        <div class="col-md-12">
                                                        <img class="img-thumbnail w-50 d-block mx-auto image-fluid mb-2 mt-2 profile_image_preview" src="{{ getSingleMedia($data,'default', null) }}" alt="profile-pic">
                                                        </div>
                                                    </div>
                                                    @endif
                                            </div>
                                            @endif
                                            @if($data['status'] == 'completed')
                                            <div class="col-md-6">
                                                <h4 class="card-title mb-1 font-weight-bold">{{ __('message.delivery_signature') }}</h4>
                                                    @if( isset($data)&& optional($data)->hasMedia('delivery_time_signature') )
                                                    <div class="card card-block">
                                                        <div id="image-preview" class="d-flex flex-wrap col-md-12 ">
                                                            @foreach(optional($data)->getMedia('delivery_time_signature') as $media)
                                                                <div class="col-md-4 mb-2 float-left">
                                                                    <a class="magnific-popup-image-gallery" href="{{ $media->getUrl() }}" title="{{ $media->name }}">
                                                                        <img id="{{ $media->id }}_preview" src="{{ $media->getUrl() }}" alt="{{ $media->name }}" class="attachment-image mt-3">
                                                                    </a>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    @else
                                                    <div class="card card-block">
                                                        <div class="col-md-12">
                                                            <img class="img-thumbnail w-50 d-block mx-auto image-fluid mb-2 mt-2 profile_image_preview" src="{{ getSingleMedia($data,'default', null) }}" alt="profile-pic">
                                                        </div>
                                                    </div>
                                                    @endif
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @if($mediaItems['prof_file'] !=null)
                                    <div class="card-body pb-0 pt-0 mb-3">
                                        <h4 class="card-title mb-1 font-weight-bold">{{ __('message.profofpicture') }}</h4>
                                        
                                        <div class="row">
                                            <div class="col-md-12">
                                                <!-- Images Section -->
                                                <div class="card card-block d-flex flex-row flex-wrap mb-3">
                                                    @foreach ($mediaItems['prof_file'] as $file)
                                                        @if(str_contains($file->mime_type, 'image'))
                                                            <div class="ml-2 mb-2">
                                                                <a href="{{ $file->getUrl() }}" target="_blank">
                                                                    <img src="{{ $file->getUrl() }}" alt="{{ $file->file_name }}" width="100" class="img-fluid rounded avatar-100">
                                                                </a>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            
                                                <!-- Videos Section -->
                                                <div class="card card-block d-flex flex-row flex-wrap">
                                                    @foreach ($mediaItems['prof_file'] as $file)
                                                        @if(str_contains($file->mime_type, 'video'))
                                                            <div class="ml-2 mb-2">
                                                                <a href="{{ $file->getUrl() }}" target="_blank">
                                                                    <video width="200" controls class="rounded">
                                                                        <source src="{{ $file->getUrl() }}" type="{{ $file->mime_type }}">
                                                                        Your browser does not support the video tag.
                                                                    </video>
                                                                </a>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                            
                                            
                                        </div>
                                    </div>
                                    @endif
                                    
                                    @if($data['status'] == 'cancelled')
                                    <div class="card-body mt-0 pt-0 pb-0">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h4 class="card-title mb-2 font-weight-bold">{{ __('message.cancelled_reason') }}</h4>
                                                <div class="card card-block">
                                                    <div class="col-md-12 mb-3 bg-dark-danger">
                                                        <h5 class="text-danger mt-2">{{($data->reason)}}</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @if ($data['status'] == 'cancelled' && $data['status'] == 'return')
                                    <div class="card-body mt-0 pt-0 pb-0">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h4 class="card-title mb-2 font-weight-bold">{{ __('message.cancelled_reason') }}</h4>
                                                <div class="card card-block">
                                                    <div class="col-md-12 mb-3 bg-dark-danger">
                                                        <h5 class="text-danger mt-2">{{($data->reason)}}</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @if($data['reason'] != null && $data['status'] != 'cancelled')
                                        <div class="card-body mt-0 pt-0 pb-0">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h4 class="card-title mb-2 font-weight-bold">{{ __('message.return_reason') }}</h4>
                                                    <div class="card card-block">
                                                        <div class="col-md-12 mb-3 bg-dark-danger">
                                                            <h5 class="text-danger mt-2">{{ $data['reason'] }}</h5>
                                                             <a href="{{ route('order-view.show', $data['parent_order_id']) }}">{{ __('message.view_old_order')}}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if($complate_data != null)
                                        <div class="card-body mt-0 pt-0 pb-0">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h4 class="card-title mb-2 font-weight-bold">{{ __('message.return_reason') }}</h4>
                                                    <div class="card card-block">
                                                        <div class="col-md-12 mb-3 bg-dark-danger">
                                                            <h5 class="text-danger mt-2">{{ $complate_data->reason }}</h5>
                                                                <a href="{{ route('order-view.show', $complate_data->id) }}">{{ __('message.view_new_order')}}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="card-body pt-0 pb-0">
                                        <div class="row">
                                            <div class="col-md-12 ">
                                                <div class="card card-block pl-2 pr-2">
                                                  <table style="border-style:none" class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>{{ __('message.description') }}</th>
                                                                <th>{{ __('message.sub_total') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>{{ __('message.delivery_charges') }}</td>
                                                                <td>{{ $data->currency }}{{ number_format( (float) $data->fixed_charges, 2,'.','') }}</td>
                                                            </tr>
                                                            @if ($data->weight_charge != null)
                                                                <tr>
                                                                    <td>{{ __('message.weight_charge') }}</td>
                                                                    <td>{{ $data->currency }}{{ number_format( (float) $data->weight_charge , 2,'.','') }}</td>
                                                                </tr>
                                                            @endif
                                                            @if ($data->vehicle_charge != null)
                                                                <tr>
                                                                    <td>{{ __('message.vehicle_charge') }}</td>
                                                                    <td>{{ $data->currency }}{{ number_format( (float) $data->vehicle_charge , 2,'.','') }}</td>
                                                                </tr>
                                                            @endif
                                                            @if ($data['distance_charge'] != '0')
                                                                <tr>
                                                                    <td>{{ __('message.distance_charge') }}</td>
                                                                    <td>{{ $data->currency }}{{ number_format( (float) $data->distance_charge,2,'.','') }}</td>
                                                                </tr>
                                                            @endif
                                                            @if ($data['insurance_charge'] != '0')
                                                                <tr>
                                                                    <td>{{ __('message.insurance_charge') }}</td>
                                                                    <td>{{ $data->currency }}{{ number_format( (float) $data->insurance_charge,2,'.','') }}</td>
                                                                </tr>
                                                            @endif
                                                            @if ($data['discount_amount'] != '0')
                                                              <tr>
                                                                <td>{{ __('message.discount') }}</td>
                                                                  <td style="color: red;">
                                                                    {{ $data->currency }} {{ number_format( (float) $data->discount_amount,2,'.','') }}
                                                                  </td>
                                                               </tr>
                                                            @endif
                                                            @if($data->extra_charges != null)
                                                                <tr>
                                                                    <th>{{ __('message.extra_charges') }}</th>
                                                                    <th></th>
                                                                </tr>
                                                            @endif
                                                            @if(!empty($data->extra_charges))
                                                                @php
                                                                    if (is_string($data->extra_charges)) {
                                                                        $data->extra_charges = json_decode($data->extra_charges, true);
                                                                    }

                                                                    $extra_charges_texts = [];
                                                                    $extra_charges_values = [];
                                                                    $totall = $data->fixed_charges + $data->distance_charge + $data->weight_charge + $data->vehicle_charge + $data->insurance_charge;

                                                                    if (is_array($data->extra_charges)) {
                                                                        foreach ($data->extra_charges as $item) {
                                                                            if (isset($item['value_type'])) {
                                                                                $formatted_value = ($item['value_type'] == 'percentage') ? $item['value'] . '%' : $data->currency  . '' . number_format( (float) $item['value'],2,'.','' );
                                                                                if ($item['value_type'] == 'percentage') {
                                                                                    $data_value = $totall * $item['value'] / 100;
                                                                                    $key = str_replace('_', ' ', ucfirst($item['key']));
                                                                                    $extra_charges_texts[] = $key . ' (' . $formatted_value . ')';
                                                                                    $extra_charges_values[] =  $data->currency  . '' . number_format( (float) $data_value,2,'.','');
                                                                                } else {
                                                                                    $key = str_replace('_', ' ', ucfirst($item['key']));
                                                                                    $extra_charges_texts[] = $key . ' (' . $formatted_value . ')';
                                                                                    $extra_charges_values[] = $formatted_value;
                                                                                }
                                                                            }
                                                                        }
                                                                        if(isset($item['value_type'])){
                                                                            $values = [];
                                                                            $countFixed = 0;
                                                                            foreach ($data->extra_charges as $item) {
                                                                                if (in_array($item['value_type'], ['percentage', 'fixed'])) {
                                                                                    if ($item['value_type'] == 'percentage') {
                                                                                        $values[] = $totall * $item['value'] / 100;
                                                                                    } elseif ($item['value_type'] == 'fixed') {
                                                                                        $values[] = $item['value'];
                                                                                    }
                                                                                }
                                                                            }
                                                                            $totalextracharge = array_sum($values);
                                                                        }
                                                                    }
                                                                    $extra_charges_text_with_br = implode('<br>', $extra_charges_texts);
                                                                    $extra_charges_text_values = implode('<br>', $extra_charges_values);
                                                                @endphp
                                                                @foreach ($extra_charges_texts as $index => $text)
                                                                    <tr>
                                                                        <td>{{ $text }}</td>
                                                                        <td>{{ $extra_charges_values[$index] }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            @endif
                                                            <tr>
                                                                    @php
                                                                   if (isset($data->extra_charges))
                                                                   {
                                                                        $totalextracharge = 0;
                                                                        if (isset($values)) {
                                                                            $totalextracharge = array_sum($values);
                                                                        }
                                                                        $total_amount = $data->fixed_charges + $data->distance_charge + $data->weight_charge + $data->vehicle_charge + $data->insurance_charge + $totalextracharge;

                                                                    } else {
                                                                        $total_amount = $data->fixed_charges + $data->distance_charge + $data->weight_charge + $data->vehicle_charge + $data->insurance_charge;
                                                                    }

                                                                    if($data['bid_type'] == 1){
                                                                        $total_amount = $data->total_amount;
                                                                       
                                                                    }
                                                                    @endphp
                                                                    @if($data['status'] != 'cancelled')
                                                                        <td class="font-weight-bold">{{ __('message.total') }}</td>
                                                                        <td class="font-weight-bold">{{$data->currency}}{{ number_format( (float) $total_amount,2,'.','') }}</td>
                                                                    @endif
                                                                    </tr>
                                                            @if($data['status'] == 'cancelled')
                                                                @php
                                                                    $totalextracharge = $totalextracharge ?? 0;
                                                                    $original_total_amount = $data->fixed_charges + $data->distance_charge + $data->weight_charge + $data->vehicle_charge + $data->insurance_charge + $totalextracharge;

                                                                    $cancel_charges = optional($data->payment)->cancel_charges;
                                                                    $total_with_cancel_charges =  $cancel_charges;
                                                                @endphp
                                                                <td class="font-weight-bold">{{ __('message.total') }}</td>
                                                                <td class="font-weight-bold">
                                                                    <del>{{$data->currency}}{{ number_format( (float) $original_total_amount,2,'.','') }}</del>
                                                                </td>
                                                                <tr>
                                                                    <td class="font-weight-bold">{{ __('message.cancel_charges') }}</td>
                                                                    <td class="font-weight-bold">{{ number_format( (float) $total_with_cancel_charges,2,'.','') }}</td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @if($data->status != 'cancelled' && $data->status != 'completed')
                                        <a href="{{ route('cance.order',$data->id) }}" class="loadRemoteModel float-right ml-2 mr-2 btn btn-sm btn-danger mb-2">
                                            {{ __('message.cancel_order') }}
                                        </a>
                                    @endif
                                </div>
                        </div>
                        <div class="col-md-4">
                            @if(auth()->user()->user_type == 'client')
                            @else
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-0"> {{ __('message.about_user') }}</h4><hr>
                                    <div class="mt-2">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <img class="rounded-circle d-block mx-auto image-fluid mb-3 profile_image_preview" src="{{ getSingleMedia($data->client,'profile_image', null) }}" alt="profile-pic" width="80px" height="80px">
                                            </div>
                                            <div class="col-md-8">
                                                <a href="{{ route('users.show',$data->client_id ?? '') }}" class="text-auto mt-1" >{{ optional($data->client)->name ?? ''  }}</a>
                                                <h6 class="mt-1 ">{{ maskSensitiveInfo('email',optional($data->client)->email) }}</h6>
                                                <h6 class="mt-1 ">{{ maskSensitiveInfo('contact_number',optional($data->client)->contact_number) }}</h6>
                                                {{-- <div class="mt-3">
                                                    <h6>{{ optional($data->client)->address ?? '' }}</h6>`
                                                </div> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if($data['delivery_man_id'] != null)
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title mb-0"> {{ __('message.about_delivery_man') }}</h4><hr>
                                        <div class="mt-2">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <img class="rounded-circle d-block mx-auto image-fluid mb-3 profile_image_preview" src="{{ getSingleMedia($data->delivery_man,'profile_image', null) }}" alt="profile-pic" width="80px" height="80px">
                                                </div>
                                                <div class="col-md-8">
                                                    <a href="{{ route('deliveryman.show',$data->delivery_man_id ?? '') }}" class="text-auto mt-1"> {{ optional($data->delivery_man)->name ?? '' }}</a>
                                                    <h6 class="mt-1">{{ maskSensitiveInfo('email',optional($data->delivery_man)->email) }}</h6>
                                                    <h6 class="mt-1">{{ maskSensitiveInfo('contact_number',optional($data->delivery_man)->contact_number) }}</h6>
                                                    <div class="mt-1">
                                                        <h6>{{ optional($data->user)->address ?? '' }}</h6>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mt-3 text-center">
                                                    <a href="{{ route('ordervehicleinfo-vehicle', $data->id) }}" class="d-inline-block loadRemoteModel">{{ __('message.view_vehicleinformation')}}</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                            @endif

                            @if( isset($data) && optional($data->vehicle)->hasMedia('vehicle_image') )
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h4 class="mb-2 flex-wrap col-md-12 text-start">{{ __('message.vehicle') }}</h4><hr>
                                            </div>
                                            <div id="image-preview" class="d-flex flex-wrap col-md-12">
                                                @foreach(optional($data->vehicle)->getMedia('vehicle_image') as $media)
                                                    <div class="col-md-6 mb-0 float-left">
                                                        <a class="image-popup-vertical-fit" href="{{ $media->getUrl() }}" title="{{ $media->name }}">
                                                            <img id="{{ $media->id }}_preview" src="{{ $media->getUrl() }}" alt="{{ $media->name }}" class="attachment-image mt-1 w-100">
                                                        </a>
                                                    </div>
                                                @endforeach
                                                <div id="image-preview" class="d-flex flex-wrap col-md-6 ">
                                                    <h6 class="mb-2 flex-wrap  text-left">{{ __('message.vehicle_name') }}</h6>
                                                    <p class="mb-2 flex-wrap col-md-6 text-left">{{optional($data->vehicle)->title}}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if($data['status'] == 'completed'  && $data['is_shipped'] == 1)
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h4 class="mb-2 flex-wrap col-md-12 text-start">{{ __('message.courier_companies') }}</h4><hr>
                                            </div>
                                            <div class="col-md-12  pr-0 pl-0">
                                                <div class="col-md-3 float-left">
                                                    <h6 class="fw-bold">{{__('message.name')}} : </h6>
                                                    <h6 class="mt-2 fw-bold">{{__('message.link')}} : </h6>
                                                </div>
                                                <div class="col-md-9 float-right pr-0 pl-0">
                                                    <li class="fw-normal "style="list-style-type: none; padding: 0;">{{ $courierCompany->name ?? '-' }}</li>
                                                    <li class="fw-normal mt-2" style="list-style-type: none; padding: 0;">
                                                        <a href="{{ $courierCompany->link ?? '#' }}" target="_blank" rel="noopener noreferrer">
                                                            {{ $courierCompany->link ?? '-' }}
                                                        </a>
                                                    </li>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if(in_array($data['status'], ['courier_departed', 'courier_picked_up']))
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h4 class="mb-2 flex-wrap col-md-12 text-start">{{ __('message.courier_companies') }}</h4><hr>
                                            </div>
                                            <div class="col-md-12">
                                                {!! html()->form('PUT', route('courier.couriercompany',$data->id))->id('couriercompany-form')->open() !!}
                                                {!! html()->hidden('is_shipped', 0) !!}
                                                {!! html()->hidden('status', 'courier_departed') !!}
                                                {!! html()->hidden('status', 'courier_departed') !!}
                                                @php
                                                    $disabled = isset($data->couriercompany_id) && $data->couriercompany_id ? 'disabled' : null;
                                                @endphp
                                            <div class="form-group col-md-12">
                                                {!! html()->label(__('message.courier_companies') . ' <span class="text-danger">*</span>')->class('form-control-label')->for('couriercompany_id') !!}
                                                {!! html()->select('couriercompany_id', isset($data) ? [optional($data->couriercompany)->id => optional($data->couriercompany)->name] : [])
                                                    ->class('select2js form-group couriercompany_id')
                                                    ->attribute('data-placeholder', __('message.courier_companies'))
                                                    ->attribute('data-ajax--url', route('ajax-list', ['type' => 'couriercompany-list']))
                                                    ->attributeIf($disabled, 'disabled') !!}
                                            </div>
                                            @php
                                                $readonly = isset($data->couriercompany_id) && $data->couriercompany_id ? 'readonly' : null;
                                            @endphp
                                            <div class="form-group col-md-12 mt-2" id="couriercompany_tracking_id" style="{{ $data->couriercompany_id ? '' : 'display: none;' }}">
                                                {!! html()->label(__('message.tracking_id') . ' <span class="text-danger">*</span>')->class('form-control-label')->for('tracking_id') !!}
                                                {!! html()->number('tracking_id', old('tracking_id', $trackingId))
                                                    ->class('form-control')
                                                    ->placeholder(__('message.enter_ids'))
                                                    ->attributeIf($readonly, 'readonly') !!}
                                                
                                                <span class="text-danger" id="form_validation_tracking_id"></span>
                                            </div>

                                            <div class="form-group col-md-12 mt-2" id="tracking_details" style="{{ $data->tracking_details ? '' : 'display: none;' }}">
                                                {!! html()->label(__('message.tracking_details') . ' <span class="text-danger">*</span>')->class('form-control-label')->for('tracking_details') !!}
                                                {!! html()->text('tracking_details', old('tracking_details'))
                                                    ->class('form-control')
                                                    ->placeholder(__('message.tracking_details'))
                                                    ->required() !!}
                                                
                                                <span class="text-danger" id="form_validation_tracking_details"></span>
                                            </div>

                                            <div class="form-group col-md-12 mt-2" id="tracking_number" style="{{ $data->tracking_number ? '' : 'display: none;' }}">
                                                {!! html()->label(__('message.tracking_number') . ' <span class="text-danger">*</span>')->class('form-control-label')->for('tracking_number') !!}
                                                {!! html()->number('tracking_number', old('tracking_number'))
                                                    ->id('insurance_text')
                                                    ->class('form-control')
                                                    ->placeholder(__('message.tracking_number'))
                                                    ->attribute('min', 0)
                                                    ->attributeIf($disabled, 'disabled') !!}
                                                
                                                <span class="text-danger" id="form_validation_tracking_number"></span>
                                            </div>
                                            @php
                                                $readonly = isset($data->tracking_details) && $data->tracking_details ? 'readonly' : null;
                                            @endphp
                                            <div class="form-group col-md-12 mt-2" id="shipping_provider" style="{{ $data->tracking_details ? '' : 'display: none;' }}">
                                                {!! html()->label(__('message.shipping_provider') . ' <span class="text-danger">*</span>')->class('form-control-label')->for('shipping_provider') !!}
                                                {!! html()->text('shipping_provider', old('shipping_provider'))
                                                    ->class('form-control')
                                                    ->placeholder(__('message.shipping_provider'))
                                                    ->attributeIf($readonly, 'readonly') !!}
                                                
                                                <span class="text-danger" id="form_validation_shipping_provider"></span>
                                            </div>

                                            <div class="form-group col-md-12 mt-2" id="date_shipped" style="{{ $data->date_shipped ? '' : 'display: none;' }}">
                                                {!! html()->label(__('message.date_shipped') . ' <span class="text-danger">*</span>')->class('form-control-label')->for('date_shipped') !!}
                                                {!! html()->input('datetime-local', 'date_shipped', old('date_shipped'))
                                                    ->class('form-control datetimepicker')
                                                    ->placeholder(__('message.date'))
                                                    ->attributeIf($readonly, 'readonly') !!}
                                                
                                                <span class="text-danger" id="form_validation_date_shipped"></span>
                                            </div>

                                            @if(!$data->couriercompany_id)
                                                <div class="form-group col-md-12 mt-2 d-flex justify-content-end">
                                                    {!! html()->submit(__('message.shipped'))->class('btn btn-sm btn-primary float-right') !!}
                                                </div>
                                            @endif

                                        {!! html()->form()->close() !!}

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h4 class="mb-2 flex-wrap col-md-12 text-first">{{ __('message.orderhistory') }}</h4><hr>
                                            @if(count($data->orderhistory) > 0)
                                                <div class="card card-block">
                                                    <div class="card-body">
                                                        <div class="mm-timeline0 m-0 d-flex align-items-center justify-content-between position-relative">
                                                            <ul class="list-inline p-0 m-0 ">
                                                                @foreach($data->orderhistory as $history)
                                                                    @php
                                                                        switch ($history->history_type) {
                                                                            case 'create':
                                                                                $iconClass = 'fa-solid fa-file-pen';
                                                                                break;
                                                                            case 'courier_assigned':
                                                                                $iconClass = 'fa-solid fa-file-signature';
                                                                                break;
                                                                            case 'courier_arrived':
                                                                                $iconClass = 'fa-solid fa-file-arrow-down';
                                                                                break;
                                                                            case 'courier_picked_up':
                                                                                $iconClass = 'fa-solid fa-truck-fast';
                                                                                break;
                                                                            case 'courier_departed':
                                                                                $iconClass = 'fa fa-plane-departure';
                                                                                break;
                                                                            case 'payment_status_message':
                                                                                $iconClass = 'fa fa-credit-card';
                                                                                break;
                                                                            case 'courier_transfer':
                                                                                $iconClass = 'fas fa-box';
                                                                                break;
                                                                            case 'completed':
                                                                                $iconClass = 'fa fa-square-check';
                                                                                break;
                                                                            case 'return':
                                                                                $iconClass = "fa fa-rotate";
                                                                                break;
                                                                            case 'courier_auto_assign_cancelled':
                                                                                $iconClass = "fa-solid fa-ban";
                                                                                break;
                                                                            case 'cancelled':
                                                                                $iconClass = "fa-solid fa-xmark";
                                                                                break;
                                                                            case 'isrechedule':
                                                                                $iconClass = "fa-solid fa-calendar-day";
                                                                                break;
                                                                            case 'shipped_order':
                                                                                $iconClass = "fa-solid fa-ship";
                                                                                break;
                                                                            case 'bid_placed':
                                                                                $iconClass = "fa-solid fa-bell";
                                                                                break;
                                                                            case 'reject_bid':
                                                                                $iconClass = "fa-solid fa-right-from-bracket";
                                                                                break;
                                                                            case 'active':
                                                                                $iconClass = "fa-solid fa-car-rear";
                                                                                break;
                                                                            default:
                                                                                $iconClass = 'fa-solid fa-question-circle';
                                                                                break;
                                                                        }
                                                                    @endphp
                                                                    <li>
                                                                        <div class="timeline-dots1 list-inline-item float-right">
                                                                            <i class="{{ $iconClass }}"></i>
                                                                        </div>
                                                                        
                                                                        @php
                                                                             $deliveryManId = $history->history_data['delivery_man_id'] ?? null;
                                                                        @endphp
                                                                        
                                                                    <h6 class="float-left mb-1">{{ __('message.' . $history->history_type)}}</h6>
                                                                        <small class="float-right mt-1">{{ dateAgoFormate($history->created_at) }}</small>
                                                                        <div class="d-inline-block w-100">
                                                                            <p>
                                                                                {{ $history->history_message }}
                                                                                @if($deliveryManId)
                                                                                    <a href="{{ route('deliveryman-view.show', $deliveryManId) }}" class="link-success">{{ __('message.view_more') }}</a>
                                                                                @endif
                                                                            </p>                                                                            
                                                                        </div>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
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
            $(document).ready(function() {
                $('.select2js').on('change', function() {
                    var selectedValue = $(this).val();
                    if (selectedValue) {
                        $('#couriercompany_tracking_id').show();
                        $('#tracking_details').show();
                        $('#tracking_number').show();
                        $('#shipping_provider').show();
                        $('#date_shipped').show();
                    } else {
                        $('#couriercompany_tracking_id').hide();
                        $('#tracking_details').hide();
                        $('#tracking_number').hide();
                        $('#shipping_provider').hide();
                        $('#date_shipped').hide();
                    }
                });
                $('#couriercompany-form').validate({
                    rules: {
                        tracking_id: { required: true },
                        tracking_details: { required: true },
                        tracking_number: { required: true },
                        shipping_provider: { required: true },
                        date_shipped: { required: true },
                    },
                    messages: {
                        tracking_id: { required: "{{ __('message.tracking_id_required') }}" },
                        tracking_details: { required: "{{ __('message.tracking_details_required') }}" },  
                        tracking_number: { required: "{{ __('message.shipping_provider_required') }}" },  
                        shipping_provider: { required: "{{ __('message.date_shipped_required') }}"},
                        date_shipped: { required: "{{ __('message.tracking_number_required') }}"} ,     
                    },
                    errorPlacement: function (error, element) {
                        error.addClass('text-danger');
                        if (element.attr("name") == "tracking_id") {
                            $('#form_validation_tracking_id').prepend(error);
                        } else {
                            error.insertAfter(element);
                        }
                    },
                    highlight: function (element) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function (element) {
                        $(element).removeClass('is-invalid');
                    }
                });
            });
            document.addEventListener("DOMContentLoaded", function() {
                const urlParams = new URLSearchParams(window.location.search);
                const type = urlParams.get('type');

                if (type === 'vehicle_information') {
                    const vehicleTab = document.querySelector('#vehicle-information-tab');
                    if (vehicleTab) {
                        vehicleTab.click();
                    }
                }
            });
        </script>
    @endsection
</x-master-layout>


