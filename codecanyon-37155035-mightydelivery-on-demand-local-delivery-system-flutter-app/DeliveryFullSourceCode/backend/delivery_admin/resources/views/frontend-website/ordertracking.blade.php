<x-frontand-layout :assets="$assets ?? []">

    <div class="container-fluid trackorder-section">
        <div class="container">
            <div class="row">
                <div class="col-md-6 track-order">
                    <h1 class="headingText">{{ $data['track_order']['track_order_title'] }}</h1>
                    <p class="headingp">{{ $data['track_order']['track_order_subtitle'] }}</p>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-center">
                        <div class="card cardbox">
                            <div class="card-body">
                                <h5 class="card-title trackby">{{ __('message.track_by') }}</h5>
                                <form action="{{ route('orderhistory') }}" method="post">
                                    @csrf
                                    <div class="">
                                        <input type="text" name="milisecond" class="form-control ordertracking-form" id="order" placeholder="{{ __('message.enter_your_track_number') }}">
                                    </div>
                                    <button type="submit"
                                        class="btn w-100 text-white mt-2 fw-bold mb-3 track-order-btn">{{ __('message.trackorder') }}</button>
                                </form>
                                <h4>{{ $data['track_order']['track_page_title'] }}</h4>
                                <p>{{ $data['track_order']['track_page_description'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container orderstatus-section">
        <div class="row">
            <div class="col-lg-12">
                <div class="contentWrapper">
                    <div>
                        @if (isset($data['order_status']['order_status_title']))     
                            <h2 class="headingTextorder">{{ $data['order_status']['order_status_title'] }}</span></h2>
                        @endif
                    </div>
                    <div class="cards ordertracking-ul">
                        <ul class="ula">
                            @if (count($data['order_status']['order_status_section']) > 0)
                                @foreach ( $data['order_status']['order_status_section'] as $values )    
                                    <li class="lia">
                                        <div class="contentcard">
                                            <div class="iconWrap">
                                                <img width="48" height="48" src="{{ getSingleMediaSettingImage($values, $values->type) }}" class="img-fluid" alt="img">
                                            </div>
                                            <div>
                                                <h3>{{ $values->title }}</h3>
                                                <p class="content">{{ $values->description }}</p>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            @endif
                            {{--  <li class="lia">
                                <div class="contentcard">
                                    <div class="iconWrap">
                                        <img width="48" height="48" src="{{ asset('frontend-website/assets/website/t2.svg') }}" class="img-fluid" alt="img">
                                    </div>
                                    <h3>{{ __('message.order_pickup') }}</h3>
                                    <p class="content">{{ __('message.your_order_has_been_pickup_up_by_your_courier_partner') }}</p>
                                </div>
                            </li>
                            <li class="lia">
                                <div class="contentcard">
                                    <div class="iconWrap">
                                        <img width="48" height="48" src="{{ asset('frontend-website/assets/website/t3.svg') }}" class="img-fluid" alt="img">
                                    </div>
                                    <h3>{{ __('message.order_in_transit') }}</h3>
                                    <p class="content">{{ __('message.your_order_is_on_its_way_to_your_customers_address') }}</p>
                                </div>
                            </li>
                            <li class="lia">
                                <div class="contentcard">
                                    <div class="iconWrap">
                                        <img width="48" height="48" src="{{ asset('frontend-website/assets/website/t4.svg') }}" class="img-fluid" alt="img">
                                    </div>
                                    <h3>{{ __('message.out_for_delivery') }}</h3>
                                    <p class="content">{{ __('message.the_courier_executive_is_on_its_way_to_deliver_the_order_at_your_customers_doorstep') }}</p>
                            </li>
                            <li>
                                <div class="contentcard">
                                    <div class="iconWrap">
                                        <img width="48" height="48" src="{{ asset('frontend-website/assets/website/t5.svg') }}" class="img-fluid" alt="img">
                                    </div>
                                    <h3>{{ __('message.reached_destination') }}</h3>
                                    <p class="content">{{ __('message.your_order_has_reached_your_customers_city') }}</p>
                                </div>
                            </li>  --}}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-frontand-layout>
