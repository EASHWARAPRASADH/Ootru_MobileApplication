<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3">
                            <h5 class="font-weight-bold">{{ $pageTitle }}</h5>

                            <a href="{{ route('deliveryman.index') }}" class="float-right btn btn-sm btn-primary"><i class="fa fa-angle-double-left"></i> {{ __('message.back') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a href="{{ route('deliveryman-view.show', $data->id) }}" class="nav-link {{ $type == 'detail' ? 'active': '' }}"> {{ __('message.profile') }} </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('deliveryman-view.show', [ 'id' => $data->id, 'type' => 'wallethistory']) }}" class="nav-link {{ $type == 'wallethistory' ? 'active': '' }}"> {{ __('message.wallet') }} </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('deliveryman-view.show', [ 'id' => $data->id, 'type' => 'orderhistory']) }}" class="nav-link {{ $type == 'orderhistory' ? 'active': '' }}"> {{ __('message.order') }} </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('deliveryman-view.show', [ 'id' => $data->id, 'type' => 'withdrawrequest']) }}" class="nav-link {{ $type == 'withdrawrequest' ? 'active': '' }}"> {{ __('message.withdrawrequest') }} </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('deliveryman-view.show', [ 'id' => $data->id, 'type' => 'document']) }}" class="nav-link {{ $type == 'document' ? 'active': '' }}"> {{ __('message.document') }} </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('deliveryman-view.show', [ 'id' => $data->id, 'type' => 'vehicle_information']) }}" class="nav-link {{ $type == 'vehicle_information' ? 'active': '' }}"> {{ __('message.vehicle_information') }} </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('deliveryman-view.show', [ 'id' => $data->id, 'type' => 'rating']) }}" class="nav-link {{ $type == 'rating' ? 'active': '' }}"> {{ __('message.rating') }} </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="row">
                                @if( $type == 'detail' )
                                    <div class="col-lg-4">
                                        <div class="card card-block p-card">
                                            <div class="profile-box">
                                                <div class="profile-card rounded">
                                                    <img class="rounded-circle avatar-100 d-block mx-auto image-fluid mb-3 profile_image_preview" src="{{ getSingleMedia($data,'profile_image', null) }}" alt="profile-pic">
                                                    <h3 class="font-600 text-white text-center mb-0">{{ optional($data)->name }}</h3>
                                                    <p class="text-white text-center mb-5">
                                                        @php
                                                            $status = 'warning';
                                                            $status_name = 'inactive';
                                                            switch ($data->status) {
                                                                case 0:
                                                                    $status = 'warning';
                                                                    $status_name = __('message.inactive');
                                                                    break;
                                                                case 1:
                                                                    $status = 'success';
                                                                    $status_name = __('message.active');
                                                                    break;
                                                            }
                                                        @endphp
                                                        <span class="text-capitalize badge bg-{{$status}}">{{$status_name}}</span>
                                                    </p>
                                                </div>
                                                <div class="pro-content rounded">
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="p-icon mr-3">
                                                            <i class="fas fa-envelope"></i>
                                                        </div>
                                                        <p class="mb-0 eml">{{ auth()->user()->hasRole('admin') ? maskSensitiveInfo('email',$data->email) : maskSensitiveInfo('email',$data->email) }}</p>
                                                    </div>
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="p-icon mr-3">
                                                            <i class="fas fa-phone-alt"></i>
                                                        </div>
                                                        <p class="mb-0">{{ auth()->user()->hasRole('admin') ? maskSensitiveInfo('contact_number',$data->contact_number) : maskSensitiveInfo('contact_number',$data->contact_number) }}</p>
                                                    </div>
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="p-icon mr-3">
                                                            <i class="fas fa-map"></i>
                                                        </div>
                                                        <p class="mb-0">{{ auth()->user()->hasRole('admin') ? optional($data->city)->name : maskSensitiveInfo('city',optional($data->city)->name) }}</p> , <p class="mb-0">{{ auth()->user()->hasRole('admin') ? optional($data->country)->name : maskSensitiveInfo('country',optional($data->country)->name) }}</p>
                                                    </div>
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="p-icon mr-3">
                                                            <i class="fa fa-code-branch"></i>
                                                        </div>
                                                        <p class="mb-0">{{ __('message.app_version') . ' : ' . (auth()->user()->hasRole('admin') ? ($data->app_version ? : '0') : '0')}}</p>
                                                    </div>
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="p-icon mr-3">
                                                            <i class="fas fa-osi"></i>
                                                        </div>
                                                        <p class="mb-0">{{ __('message.app_source') . '  : ' . (auth()->user()->hasRole('admin') ? ($data->app_source ? : 'N/A') : 'N/A')}}</p>
                                                    </div>
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="p-icon mr-3">
                                                            <i class="fa-solid fa-clock-rotate-left"></i>
                                                        </div>
                                                        <p class="mb-0">{{ __('message.last_active') . '  : ' . (auth()->user()->hasRole('admin') ? (dateAgoFormate($data->last_actived_at) ? : 'N/A') : 'N/A')}}</p>
                                                    </div>
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="p-icon mr-3">
                                                            <i class="fa-solid fa-truck"></i>
                                                        </div>
                                                        <p class="mb-0">{{ __('message.vehicle') . ' : ' . (auth()->user()->hasRole('admin') ? (optional($data->vehicle)->title ? : 'N/A') : 'N/A')}}</p>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="card card-block mr-3">
                                                <div class="header-title ml-3 mt-3 d-flex justify-content-between align-items-center">
                                                    <h4 class="card-title mb-0">{{ __('message.verification_detail')}}</h4>
                                                </div>
                                                <hr>
                                                <div class="row col-md-12">
                                                    <div class="card-body p-0">
                                                        <div class="table-responsive">
                                                            <table id="verification-table" class="table mb-0 table-bordered text-center mb-2 ml-1" role="grid">
                                                                <thead>
                                                                    <tr>
                                                                        <th scope='col'>{{ __('message.type') }}</th>
                                                                        <th scope='col'>{{ __('message.is_auto_verified') }}</th>
                                                                        <th scope='col'>{{ __('message.verified_date') }}</th>
                                                                        <th scope='col'>{{ __('message.action') }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>{{__('message.email')}}</td>
                                                                        <td>{{ ($user->is_autoverified_email == 1 ? 'yes' :'no') }}</td>
                                                                        <td>{{ dateAgoFormate($user->email_verified_at) }}</td>
                                                                        <td>
                                                                            @if($user->email_verified_at !=null)
                                                                                <button type="button" class="btn btn-sm btn-primary update-verification" data-type="email" data-id="{{ $user->id }}">{{__('message.re_verification')}}</button>
                                                                            @else
                                                                                {{'-'}}
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>{{__('message.mobile')}}</td>
                                                                        <td>{{ ($user->is_autoverified_mobile == 1 ? 'yes' :'no') }}</td>
                                                                        <td>{{ dateAgoFormate($user->otp_verify_at) }}</td>
                                                                        <td>
                                                                            @if($user->otp_verify_at !=null)
                                                                                <button type="button" class="btn btn-sm btn-primary update-verification" data-type="mobile" data-id="{{ $user->id }}">{{__('message.re_verification')}}</button>
                                                                            @else
                                                                                {{'-'}}
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>{{__('message.document')}}</td>
                                                                        <td>{{ ($user->is_autoverified_document == 1 ? 'yes' :'no') }}</td>
                                                                        <td>{{ dateAgoFormate($user->document_verified_at) }}</td>
                                                                        <td>
                                                                            @if($requiredDocumentIds)
                                                                                @if($user->document_verified_at !=null)
                                                                                    <button type="button" class="btn btn-sm btn-primary update-verification" data-type="document" data-id="{{ $user->id }}">{{__('message.re_verification')}}</button>
                                                                                @else
                                                                                    {{'-'}}
                                                                                @endif
                                                                            @else
                                                                             {{__('message.document_not_req')}}
                                                                            @endif

                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <form id="update-form" action="{{ route('update-verification', ['user' => $user]) }}" method="POST" style="display: none;">
                                                                @csrf
                                                                <input type="hidden" name="type" id="update-type">
                                                                <input type="hidden" name="id" id="update-id">
                                                                <input type="hidden" name="confirm" id="update-confirm">
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="card card-block mr-3">
                                                <div class="header-title ml-3 mt-3 d-flex justify-content-between align-items-center">
                                                    <h4 class="card-title mb-0">{{ __('message.last_location')}}</h4>
                                                    <a href="https://www.google.com/maps/search/?api=1&query={{ $data->latitude }},{{ $data->longitude }}" target="_blank" class="location-link">
                                                      <div class="mr-2">
                                                        {{__('message.view_map')}}
                                                      </div>
                                                    </a>
                                                </div>
                                                <hr>
                                                <div class="row col-md-12">
                                                    <div class="col-md-4">
                                                        <div class="card card-block">
                                                            <div class="card-body">
                                                                <div class="top-block-one">
                                                                    <p class="mb-1">{{ __('message.latitude') }}</p>
                                                                    <h5>{{ ($data->latitude ? : '0') }}</h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="card card-block">
                                                            <div class="card-body">
                                                                <div class="top-block-one">
                                                                    <p class="mb-1">{{ __('message.longitude') }}</p>
                                                                    <h5>{{ ($data->longitude ? : '0') }}</h5>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="card card-block">
                                                            <div class="card-body">
                                                                <div class="top-block-one">
                                                                    <p class="mb-1">{{ __('message.last_active') }}</p>
                                                                    <p>{{ dateAgoFormate($data->last_actived_at) ? : null }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="card card-block mr-3">
                                                <div class="header-title ml-3 mt-3">
                                                    <h4 class="card-title">{{ __('message.bank_details')}}</h4>
                                                </div>
                                                <hr>
                                                @foreach ($bank_detail  as $value)
                                                    <div class="row">
                                                        <div class="form-group col-md-3 ">
                                                            {{ html()->label(__('message.bank_name'))->for('bank_name')->class('form-control-label text-secondary ml-2') }}
                                                            <h6 class="ml-2">{{ optional($value)->bank_name }}</h6>
                                                        </div>

                                                        <div class="form-group col-md-3">
                                                            {{ html()->label(__('message.bank_account_holder_name'))->for('bank_account_holder_name')->class('form-control-label text-secondary ml-2') }}

                                                            <h6 class="ml-2"> {{optional($value)->account_holder_name }}</h6>
                                                        </div>

                                                        <div class="form-group col-md-3">
                                                            {{ html()->label(__('message.account_number'))->for('account_number')->class('form-control-label text-secondary ml-2') }}

                                                            <h6 class="ml-2"> {{optional($value)->account_number }}</h6>
                                                        </div>

                                                        <div class="form-group col-md-3">
                                                            {{ html()->label(__('message.bank_ifsc_code'))->for('bank_ifsc_code')->class('form-control-label text-secondary ml-2') }}

                                                            <h6 class="ml-2"> {{optional($value)->bank_code }}</h6>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="row col-md-12">
                                                            <div class="form-group col-md-3">
                                                                {{ html()->label(__('message.bank_address'))->for('bank_address')->class('form-control-label text-secondary ml-2') }}

                                                                <h6 class="ml-2">{{optional($value)->bank_address }}</h6>
                                                            </div>

                                                            <div class="form-group col-md-3">
                                                                {{ html()->label(__('message.routing_number'))->for('routing_number')->class('form-control-label text-secondary ml-2') }}

                                                                <h6 class="ml-2"> {{optional($value)->routing_number }}</h6>
                                                            </div>

                                                            <div class="form-group col-md-3">
                                                                {{ html()->label(__('message.bank_iban'))->for('bank_iban')->class('form-control-label text-secondary ml-2') }}

                                                                <h6 class="ml-2"> {{optional($value)->bank_iban }}</h6>
                                                            </div>

                                                            <div class="form-group col-md-3">
                                                                {{ html()->label(__('message.bank_swift'))->for('bank_swift')->class('form-control-label text-secondary ml-2') }}

                                                                <h6 class="ml-2"> {{optional($value)->bank_swift }}</h6>
                                                            </div>
                                                                <hr>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                @if( $type == 'wallethistory' )
                                    <div class="row col-md-8">
                                        <div class="col-md-4">
                                            <div class="card card-block">
                                                <div class="card-body">
                                                    <div class="top-block-one">
                                                        <p class="mb-1">{{ __('message.wallet_total_balance') }}</p>
                                                        <h5>{{ getPriceFormat($earning_detail->user_wallet_sum_total_amount) ?? 0 }} </h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card card-block">
                                                <div class="card-body">
                                                    <div class="top-block-one">
                                                        <p class="mb-1">{{ __('message.total_withdrawal') }}</p>
                                                        <p></p>
                                                        <h5>{{ getPriceFormat($earning_detail->user_wallet_sum_total_withdrawn) ?? 0 }}</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card card-block">
                                                <div class="card-body">
                                                    <div class="top-block-one">
                                                        <p class="mb-1">{{ __('message.admin_commision') }}</p>
                                                        <p></p>
                                                        <h5>{{getPriceFormat ($earning_detail->get_payment_sum_admin_commission)  ?? 0 }} </h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card card-block">
                                                <div class="card-body">
                                                    <div class="top-block-one">
                                                        <p class="mb-1">{{ __('message.earning_balance') }}</p>
                                                        <p></p>
                                                        <h5>{{getPriceFormat ($earning_detail->delivery_man_commission)  ?? 0 }} </h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card card-block">
                                                <div class="card-body">
                                                    <div class="top-block-one">
                                                        <p class="mb-1">{{ __('message.total_order') }}</p>
                                                        <p></p>
                                                        <h5> {{optional($earning_detail)->total_order }} </h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card card-block">
                                                <div class="card-body">
                                                    <div class="top-block-one">
                                                        <p class="mb-1">{{ __('message.paid_order') }}</p>
                                                        <p></p>
                                                        <h5>{{ optional($earning_detail)->paid_order  }} </h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row col-md-4 ml-4 mb-3">
                                        <div class="card card-block">
                                            <div class="card-header d-flex justify-content-between">
                                                <div class="header-title">
                                                    <h4 class="card-title mb-0">{{ __('message.add_form_title', [ 'form' => __('message.wallet') ]) }}</h4>
                                                </div>
                                            </div>
                                            <div class="card-body mr-4">
                                                {{ html()->form('POST', route('savewallet-save', $data->id))->open() }}
                                                    <div class="row">
                                                        <div class="form-group col-md-6">
                                                            {{ html()->label(__('message.type').' <span class="text-danger">*</span>')->class('form-control-label') }}
                                                            {{ html()->select('type', ['credit' => __('message.credit'), 'debit' => __('message.debit')], old('type'))
                                                                ->class('form-control select2js')
                                                                ->attribute('required', true) }}
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            {{ html()->label(__('message.amount').' <span class="text-danger">*</span>')->class('form-control-label') }}
                                                            {{ html()->number('amount', old('amount'))
                                                                ->class('form-control')
                                                                ->attribute('min', 0)
                                                                ->attribute('step', 'any')
                                                                ->attribute('required', true)
                                                                ->placeholder(__('message.amount')) }}
                                                        </div>
                                                    </div>
                                                    {{ html()->submit(__('message.save'))->class('btn btn-md btn-primary float-right') }}
                                                {{ html()->form()->close() }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card card-block ml-1 mr-2">
                                                    <div class="card-header d-flex justify-content-between flex-wrap">
                                                        <div class="header-title">
                                                            <h4 class="card-title">{{ __('message.wallethistory')}}</h4>
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-0">
                                                        <div class="table-responsive mt-4">
                                                            <table id="" class="table mb-0 table-bordered text-center" role="grid">
                                                                <thead>
                                                                    <tr>
                                                                        <th scope='col'>{{ __('message.order_id') }}</th>
                                                                        <th scope='col'>{{ __('message.transaction_type') }}</th>
                                                                        <th scope='col'>{{ __('message.amount') }}</th>
                                                                        <th scope='col'>{{ __('message.date') }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @if(count($wallet_history) > 0)
                                                                        @foreach ( $wallet_history  as $value)
                                                                            <tr>
                                                                                <td><a href="{{ route('order-view.show', $value->order_id) }}">{{ optional($value)->order_id ?? null }}</a></td>
                                                                                @php
                                                                                    $key = str_replace('_', ' ', ucwords($value->transaction_type ?? '-', '_'));
                                                                                @endphp
                                                                                <td>{{$key}}</td>
                                                                                <td>
                                                                                    <span class="
                                                                                        @if(optional($value)->type == 'credit')
                                                                                            text-success
                                                                                        @elseif(optional($value)->type == 'debit')
                                                                                            text-danger
                                                                                        @endif
                                                                                    ">{{ getPriceFormat($value->amount) ?? '-' }}</span>
                                                                                </td>
                                                                                <td>{{dateAgoFormate($value->created_at) ?? '-' }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @else
                                                                        <tr>
                                                                            <td colspan="9">{{ __('message.no_record_found') }}</td>
                                                                        </tr>
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card card-block ml-2 mr-2">
                                                    <div class="card-header d-flex justify-content-between flex-wrap">
                                                        <div class="header-title">
                                                            <h4 class="card-title">{{ __('message.earning_history')}}</h4>
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-0">
                                                        <div class="table-responsive mt-4">
                                                            <table id="" class="table mb-0 table-bordered text-center" role="grid">
                                                                <thead>
                                                                    <tr>
                                                                        <th scope='col'>{{ __('message.order_id') }}</th>
                                                                        <th scope='col'>{{ __('message.earning') }}</th>
                                                                        <th scope='col'>{{ __('message.admin_commission') }}</th>
                                                                        <th scope='col'>{{ __('message.payment_type') }}</th>
                                                                        <th scope='col'>{{ __('message.date') }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @if( count($earning_detail_items) > 0)
                                                                        @foreach ( $earning_detail_items  as $value)
                                                                            <tr>
                                                                                <td><a href="{{ route('order.show', $value->order_id) }}">{{ optional($value)->order_id }}</a></td>
                                                                                <td>{{ getPriceFormat($value->delivery_man_commission) ?? '-' }}</td>
                                                                                <td>{{ getPriceFormat($value->admin_commission) ?? '-' }}</td>
                                                                                <td>{{ $value->payment_type ?? '-' }}</td>
                                                                                <td>{{ dateAgoFormate($value->created_at) ?? '-' }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @else
                                                                        <tr>
                                                                            <td colspan="9">{{ __('message.no_record_found') }}</td>
                                                                        </tr>
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if( $type == 'orderhistory')
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="card card-block mr-1 ml-1">
                                                    <div class="card-body p-2 mr-2">
                                                        <table id="basic-table" class="table mb-3  text-center">
                                                            <thead>
                                                                <tr>
                                                                    <th scope='col'>{{ __('message.order_id') }}</th>
                                                                    <th scope='col'>{{ __('message.pickup_address') }}</th>
                                                                    <th scope='col'>{{ __('message.delivery_address') }}</th>
                                                                    <th scope='col'>{{ __('message.pickup_date') }}</th>
                                                                    <th scope='col'>{{ __('message.delivery_date') }}</th>
                                                                    <th scope='col'>{{ __('message.invoice') }}</th>
                                                                    <th scope='col'>{{ __('message.created_at') }}</th>
                                                                    <th scope='col'>{{ __('message.status') }}</th>
                                                                    <th scope='col'>{{ __('message.is_return') }}</th>
                                                                    <th scope='col'>{{ __('message.assign') }}</th>
                                                                    <th scope='col'>{{ __('message.action') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @if($order->count() > 0)
                                                                    @foreach ( $order as $orders )
                                                                        @php
                                                                            $status = 'primary';
                                                                                $order_status = $orders->status;
                                                                            switch ($order_status) {
                                                                                case 'draft':
                                                                                $status = 'light';
                                                                                $status_name = __('message.draft');
                                                                                break;
                                                                            case 'create':
                                                                                $status = 'primary';
                                                                                $status_name = __('message.create');
                                                                                break;
                                                                            case 'completed':
                                                                                $status = 'success';
                                                                                $status_name = __('message.completed');
                                                                                break;
                                                                            case 'courier_assigned':
                                                                                $status = 'warning';
                                                                                $status_name = __('message.courier_assigned');
                                                                                break;
                                                                            case 'active':
                                                                                $status = 'info';
                                                                                $status_name = __('message.active');
                                                                                break;
                                                                            case 'courier_departed':
                                                                                $status = 'info';
                                                                                $status_name = __('message.courier_departed');
                                                                                break;
                                                                            case 'courier_picked_up':
                                                                                $status = 'warning';
                                                                                $status_name = __('message.pickup');
                                                                                break;
                                                                            case 'courier_arrived':
                                                                                $status = 'warning';
                                                                                $status_name = __('message.arrived');
                                                                                break;
                                                                            case 'cancellled':
                                                                                $status = 'danger';
                                                                                $status_name = __('message.cancellled');
                                                                                break;
                                                                            case 'delayed':
                                                                                $status = 'secondary';
                                                                                $status_name = __('message.delayed');
                                                                                break;
                                                                            case 'failed':
                                                                                $status = 'danger';
                                                                                $status_name = __('message.failed');
                                                                                break;
                                                                            }
                                                                        @endphp

                                                                        <tr>
                                                                            <td><a href="{{ route('order.show', $orders) }}">{{ optional($orders)->id }}</a></td>
                                                                            <td>
                                                                                <?php
                                                                                    $pickup_address = $orders->pickup_point['address'];
                                                                                    echo (isset($pickup_address)) ?'<span data-toggle="tooltip" title="'.$pickup_address.'">'.stringLong($pickup_address, 'title',20).'</span>' : '-';
                                                                                ?>
                                                                            </td>
                                                                            <td>
                                                                                <?php
                                                                                    $delivery_address = $orders->delivery_point['address'];
                                                                                    echo (isset($delivery_address)) ?'<span data-toggle="tooltip" title="'.$delivery_address.'">'.stringLong($delivery_address, 'title',20).'</span>' : '-';
                                                                                ?>
                                                                            </td>
                                                                            <td>{{ dateAgoFormate($orders->pickup_datetime) ?? '-' }}</td>
                                                                            <td>{{ dateAgoFormate($orders->delivery_datetime) ?? '-' }}</td>
                                                                            <td>
                                                                                @if(optional($orders)->status == 'completed')
                                                                                    <a href="{{ route('order-invoice', $orders->id) }}"><i class="fa fa-download"></i></a>
                                                                                @else
                                                                                N/A
                                                                                @endif
                                                                            </td>
                                                                            <td>{{ dateAgoFormate($orders->created_at) ?? '-' }}</td>
                                                                            <td><span class="badge bg-{{$status}}">{{ __('message.'.$order_status) }}</span></td>
                                                                            <td>
                                                                                @php
                                                                                $parentOrderIds = $orders->pluck('parent_order_id')->toArray();
                                                                                @endphp
                                                                                    @if (in_array($orders->id, $parentOrderIds))
                                                                                        <i class="fa-solid fa-right-left text-primary"></i>
                                                                                    @else
                                                                                       {{'-'}}
                                                                                    @endif
                                                                            </td>
                                                                            <td>
                                                                                @if($orders->deleted_at)
                                                                                    <span style="color: red">{{ __('message.order_deleted') }}</span>
                                                                                @elseif($orders->status === 'cancelled')
                                                                                    <span class='text-primary'>{{ __('message.order_cancelled') }}</span>
                                                                                @elseif($orders->status === 'draft')
                                                                                    <span class='text-primary'>{{ __('message.order_draft') }}</span>
                                                                                @elseif($orders->status === 'completed')
                                                                                    <span class='text-primary'>{{ __('message.order_completed') }}</span>
                                                                                @elseif($orders->delivery_man_id === null)
                                                                                    <a href="{{ route('order-assign', ['id' => $orders->id]) }}" class="btn btn-sm btn-outline-primary loadRemoteModel">{{ __('message.assign') }}</a>
                                                                                @else
                                                                                    <a href="{{ route('order-assign', ['id' => $orders->id]) }}" class="btn btn-sm btn-outline-primary loadRemoteModel">{{ __('message.transfer') }}</a>
                                                                                @endif
                                                                            </td>
                                                                            <td>
                                                                                <div class="d-flex justify-content-end align-items-center">
                                                                                    {{ html()->form('DELETE', route('order.destroy', $orders->id))->attribute('data--submit', 'order' . $orders->id)->open()}}
                                                                                        <a class="mr-2 text-danger" href="javascript:void(0)" data--submit="order{{$orders->id}}"
                                                                                            data--confirmation='true' data-title="{{ __('message.delete_form_title',['form'=> __('message.order') ]) }}"
                                                                                            title="{{ __('message.delete_form_title',['form'=>  __('message.order') ]) }}"
                                                                                            data-message='{{ __("message.delete_msg") }}'>
                                                                                            <i class="fas fa-trash-alt"></i>
                                                                                        </a>
                                                                                    {{ html()->form()->close() }}
                                                                                    @if( auth()->user()->hasRole('admin') && $orders->status != 'draft' )
                                                                                        <a class="mr-2" href="{{ route('order.show',$orders->id) }}"><i class="fas fa-eye text-secondary"></i></a>
                                                                                    @endif
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                @else
                                                                    <tr>
                                                                        <td colspan="9">{{ __('message.no_record_found') }}</td>
                                                                    </tr>
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if ($type == 'withdrawrequest')
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="card card-block mr-1 ml-1">
                                                    <div class="card-body p-2 mr-2">
                                                        <table id="basic-table" class="table mb-3  text-center">
                                                            <thead>
                                                                <tr>
                                                                    <th scope='col'>{{ __('message.no') }}</th>
                                                                    <th scope='col'>{{ __('message.amount') }}</th>
                                                                    <th scope='col'>{{ __('message.available_balnce') }}</th>
                                                                    <th scope='col'>{{ __('message.request_at') }}</th>
                                                                    <th scope='col'>{{ __('message.action_at') }}</th>
                                                                    <th scope='col'>{{ __('message.bank_details') }}</th>
                                                                    <th scope='col'>{{ __('message.status') }}</th>
                                                                    <th scope='col'>{{ __('message.action') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @if($withdraw->count() > 0)
                                                                    @php
                                                                        $counter = 1;
                                                                    @endphp
                                                                    @foreach($withdraw as $value)
                                                                        <tr>
                                                                            <td>{{ $counter }}</td>
                                                                            <td>{{ getPriceFormat($value->amount) ?? 0 }}</td>
                                                                            <td>
                                                                                {{ $value->status == 'requested' ? ($wallte ? getPriceFormat($wallte->total_amount) : 0) : '-' }}
                                                                            </td>
                                                                            <td>{{ dateAgoFormate($value->created_at) ?? '-' }}</td>
                                                                            <td>{{ dateAgoFormate($value->updated_at) ?? '-' }}</td>
                                                                            <td>
                                                                                <a class="mr-2 loadRemoteModel" href="{{ route('withdrawrequest.show',$value->user_id) }}"><i class="fas fa-eye text-secondary"></i></a>
                                                                            </td>
                                                                            <td>
                                                                                @php
                                                                                    $status = 'danger';
                                                                                    $status_name = 'requested';
                                                                                    switch ($value->status) {
                                                                                        case 'requested':
                                                                                            $status = 'indigo';
                                                                                            $status_name = __('message.pending');
                                                                                            break;
                                                                                        case 'decline':
                                                                                            $status = 'danger';
                                                                                            $status_name = __('message.declined');
                                                                                            break;
                                                                                        case 'approved':
                                                                                            $status = 'success';
                                                                                            $status_name = __('message.approved');
                                                                                            break;
                                                                                    }
                                                                                @endphp
                                                                                <span class="text-capitalize badge bg-{{$status}}">{{$status_name  ?? ''}} </span>
                                                                            </td>
                                                                            <td>
                                                                                @if($value->status == 'requested')
                                                                                    <a class="mr-2" href="{{ route('approvedWithdrawRequest', ['id' => $value->id]) }}"><i class="fas fa-check"></i></a>
                                                                                    <a class="mr-2" href="{{ route('declineWithdrawRequest', ['id' => $value->id]) }}"><i class="fa fa-times text icon-color"></i></a>
                                                                                @else
                                                                                    {{'-'}}
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                        @php
                                                                            $counter++;
                                                                        @endphp
                                                                    @endforeach
                                                                @else
                                                                    <tr>
                                                                        <td colspan="9">{{ __('message.no_record_found') }}</td>
                                                                    </tr>
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if ($type == 'document')
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="card card-block mr-1 ml-1">
                                                    <div class="card-body p-2 mr-2">
                                                        <table id="basic-table" class="table mb-3  text-center">
                                                            <thead>
                                                                <tr>
                                                                    <th scope='col'>{{ __('message.no') }}</th>
                                                                    <th scope='col'>{{ __('message.document_name') }}</th>
                                                                    <th scope='col'>{{ __('message.document') }}</th>
                                                                    <th scope='col'>{{ __('message.status') }}</th>
                                                                    <th scope='col'>{{ __('message.created_at') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @if($documents->count() > 0)
                                                                    @php
                                                                        $counter = 1;
                                                                    @endphp
                                                                    @foreach($documents as $value)
                                                                        <tr>
                                                                            <td>{{ $counter }}</td>
                                                                            <td>{{ $value->document->name ?? '' }}</td>
                                                                            <td>
                                                                                <a href="javascript:void(0)" class="image-popup-vertical-fit">
                                                                                    <img src="{{ getSingleMedia($value,'delivery_man_document', null) }}" alt="delivery_man_document" width="40" height="40">
                                                                                </a>
                                                                            </td>
                                                                            @php
                                                                                $status_name = '';
                                                                                switch ($value->is_verified) {
                                                                                    case 1:
                                                                                        $status_name = __("message.approve");
                                                                                        break;
                                                                                    case 2:
                                                                                        $status_name = __("message.reject");
                                                                                        break;
                                                                                    case 0:
                                                                                        $status_name = __("message.pending");
                                                                                        break;
                                                                                }
                                                                            @endphp
                                                                            <td>{{ $status_name }}</td>
                                                                            <td>{{ dateAgoFormate($value->created_at) ?? '-' }}</td>
                                                                        </tr>
                                                                        @php
                                                                            $counter++;
                                                                        @endphp
                                                                    @endforeach
                                                                @else
                                                                    <tr>
                                                                        <td colspan="9">{{ __('message.no_record_found') }}</td>
                                                                    </tr>
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if ($type == 'vehicle_information')
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="card card-block mr-1 ml-1">
                                                    <div class="card-body p-2 mr-2">
                                                        <table id="basic-table" class="table mb-3  text-center">
                                                            <thead>
                                                                <tr>
                                                                    <th scope='col'>{{ __('message.id') }}</th>
                                                                    <th scope='col'>{{ __('message.start_date') }}</th>
                                                                    <th scope='col'>{{ __('message.end_date') }}</th>
                                                                    <th scope='col'>{{ __('message.is_active') }}</th>
                                                                    <th scope='col'>{{ __('message.vehicle_info') }}</th>
                                                                    <th scope='col'>{{ __('message.is_active') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ( $deliverymanvehicle as $value )
                                                                    @php
                                                                        $status = 'primary';
                                                                        $status_name = '';
                                                                        switch ($value->is_active) {
                                                                            case 0:
                                                                                $status = 'warning';
                                                                                $status_name = __('message.inactive');
                                                                                break;
                                                                            case 1:
                                                                                $status = 'primary';
                                                                                $status_name = __('message.active');
                                                                                break;
                                                                        }
                                                                    @endphp
                                                                    <tr>
                                                                        <td>{{ $value->id ?? '-'}}</td>
                                                                        <td>{{ dateAgoFormate($value->start_datetime) ?? '-' }}</td>
                                                                        <td>{{ dateAgoFormate($value->end_datetime) ?? '-' }}</td>
                                                                        <td><span class="badge bg-{{ $status }}">{{ $status_name }}</span></td>

                                                                        <td><a href="{{ route('vehicleinfo-vehicle', $value->id) }}" class="btn btn-sm btn-outline-primary loadRemoteModel">{{ __('message.vehicle_info') }}</a></td>
                                                                        <td>
                                                                            <form method="POST" action="{{ route('deliveryman.vehicle.status.update') }}">
                                                                                @csrf
                                                                                <input type="hidden" name="id" value="{{ $value->id }}">                                                                                
                                                                                <div class="custom-switch custom-switch-text custom-switch-color custom-control-inline">
                                                                                    <div class="custom-switch-inner">
                                                                                        <input type="checkbox"
                                                                                            class="custom-control-input bg-success"
                                                                                            id="checkbox-{{ $value->id }}"
                                                                                            name="is_active"
                                                                                            value="1"
                                                                                            onchange="this.form.submit()"
                                                                                            {{ $value->is_active == 1 ? 'checked' : '' }}>
                                                                                        <label class="custom-control-label"
                                                                                            for="checkbox-{{ $value->id }}"
                                                                                            data-on-label="Yes"
                                                                                            data-off-label="No">
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </form>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if( $type == 'rating' )
                                    <div class="card card-block">
                                        <div class="card-body">
                                            {{ $dataTable->table(['class' => 'table  w-100'],false) }}
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
    @section('bottom_script')
    {{ in_array($type,['rating']) ? $dataTable->scripts() : '' }}
        <script>
             $("#basic-table").DataTable({
                "dom": '<"row align-items-center"<"col-md-2"><"col-md-6" B><"col-md-4" f>><"table-responsive my-3" rt><"d-flex"<"flex-grow-1" l><"" i><"" p>><"clear">',
                "order": [[0, "desc"]]
            });
            $(document).ready(function() {
                $('.update-verification').click(function() {
                    var type = $(this).data('type');
                    var id = $(this).data('id');
                    var message = 'Are you sure you want to re-verify ' + type + '?';
                    Swal.fire({
                        title: message,
                        showDenyButton: true,
                        confirmButtonText: 'Yes',
                        denyButtonText: 'No'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#update-type').val(type);
                            $('#update-id').val(id);
                            $('#update-confirm').val('yes');
                            $('#update-form').submit();
                        } else if (result.isDenied) {
                            $('#update-confirm').val('no');
                        }
                    });
                });
            });
        </script>
    @endsection
    </x-master-layout>
