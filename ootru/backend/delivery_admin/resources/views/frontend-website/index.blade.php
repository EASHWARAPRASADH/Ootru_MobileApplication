<x-frontand-layout :assets="$assets ?? []">
    
    <section class="py-5 py-md-5 light-bg-color">
        <div class="container">
            <div class="row align-items-center mt-0 mt-md-5">
                <div class="col-lg-6">
                    <h1 class="display-5 main-text mt-5 font-weight-500 mb-1">{{ $data['app_content']['app_title'] }}</h1>
                    <h2 class="display-5 font-weight-500 site-color mb-4">{{ $data['app_content']['app_subtitle'] }}</h2>
                    <div class="card radius-12 p-4 mt-5 card-border">
                        <h4 class="main-text fs-custom-35 mb-3">{{ __('message.track_your_shipment') }}</h4>
                        <form action="{{ route('orderhistory') }}" method="post">
                            @csrf
                            <div class="input-group radius-4 light-bg-color mt-3">
                                <input type="text" name="milisecond" class="form-control border-0 bg-transparent" id="order" placeholder="Type your tracking number here" aria-label="Tracking Number">
                                <div class="p-2">
                                    <button type="submit" class="btn white-color d-flex align-items-center gap-1 px-3 py-3 site-bg-color">{{ __('message.track') }}
                                        <svg width="24" height="24" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6 18.5L18 6.5M18 6.5H9M18 6.5V15.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </form> 
                        <div class="d-flex align-items-center mt-4">
                            <div class="flex-grow-1 card-border line-opacity"></div>
                            <div class="px-3 gray-text">{{ __('message.or_create_a_new_order') }}</div>
                            <div class="flex-grow-1 card-border line-opacity"></div>
                        </div>
                
                        <div class="d-grid mb-3 mt-4">
                            @if(Auth::check() && Auth::user()->user_type == 'client')
                                <a class="btn site-bg-color white-color p-3" href="{{ route('order.create')}}" role="button">{{ __('message.create_order') }}
                                    <svg width="24" height="24" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6 18.5L18 6.5M18 6.5H9M18 6.5V15.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                            @elseif(Auth::check() && Auth::user()->user_type == 'admin')
                                <a class="btn site-bg-color white-color p-3" href="{{ route('order.create')}}" role="button">{{ __('message.create_order') }}
                                    <svg width="24" height="24" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6 18.5L18 6.5M18 6.5H9M18 6.5V15.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                            @elseif(Auth::check() && Auth::user())
                                <a class="btn site-bg-color white-color p-3" href="{{ route('order.create')}}" role="button">{{ __('message.create_order') }}
                                    <svg width="24" height="24" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6 18.5L18 6.5M18 6.5H9M18 6.5V15.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                            @else
                                <a class="btn site-bg-color white-color p-3" href=" {{ route('admin-login') }}"data-bs-toggle="modal" data-bs-target="#signinModal" role="button">{{ __('message.create_order') }}
                                    <svg width="24" height="24" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6 18.5L18 6.5M18 6.5H9M18 6.5V15.5" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>            
                            @endif
                        </div>

                        <div class="d-flex align-items-center mt-4">
                            <p class="mb-2 scan-qr fs-custom-18 main-text me-3">{{ __('message.scan_qr_code_and_download_our_app') }}</p>
                            <div class="justify-content-center gap-3 scan-qr-section gap-3">
                                @if (!empty($data['download_app']['appstore_image']))
                                    <a href="{{ $data['download_app']['appstore_url']['url'] }}" {{ $data['download_app']['appstore_url']['target'] }} class="text-decoration-none">
                                        <img src="{{ $data['download_app']['appstore_image'] }}" alt="App QR" alt="iOS QR" width="120" height="120">
                                    </a>
                                @endif
                                @if (!empty($data['download_app']['playstore_image']))
                                    <a href="{{ $data['download_app']['playstore_url']['url'] }}" {{ $data['download_app']['playstore_url']['target'] }} class="text-decoration-none">
                                        <img src="{{ $data['download_app']['playstore_image'] }}" alt="Play QR" alt="Android QR" width="120" height="120">
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    @if (!empty($data['app_content']['delivery_man_image']))
                        <img src="{{ $data['app_content']['delivery_man_image'] }}" class="img-fluid rounded-4"  alt="Delivery Person">
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if (!empty($why_delivery) && count($why_delivery) > 0)
        <section class="py-0 py-md-5">
            <div class="container py-5">
                <div class="text-center mb-5">
                    <h2 class="font-weight-500 display-5 main-text">{{ $data['why_choose']['title'] }} <span class="site-color">{{ $data['app_content']['app_name'] }}</span></h2>
                    <p class="mx-auto fs-custom-18 gray-text mt-3 paragraph-maxwidth">
                        {{ $data['why_choose']['description'] }}
                    </p>
                </div>

                <div class="row g-4">
                    @foreach ($why_delivery as $item)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm text-center p-3 light-bg-color radius-20">
                                <img src="{{ getSingleMediaSettingImage($item->id != null ? $item : null ,'frontend_data_image','why_delivery_image') }}" alt="mightydelivery" class="img-fluid mx-auto why-delivery-img">
                                <div class="card-body">
                                    <h5 class="card-title main-text fs-custom-24 text-truncate">{{ $item->title }}</h5>
                                    <p class="card-text gray-text multiline-truncate">{{ $item->subtitle }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="py-5 light-bg-color text-center">
        <div class="container py-5">
            <h2 class="font-weight-500 display-5 main-text">{{ __('message.how') }} <span class="site-color">{{ SettingData('app_content', 'app_name') }} {{ __('message.works') }}</span> </h2>
            <div class="row justify-content-center align-items-start text-center mt-5">
                <div class="col-12 col-md-6 col-lg-3 mb-4 step-box">
                    <small class="main-text d-block mb-2">{{ __('message.step_1') }}</small>
                    <div class="mb-3 mt-4">
                        <img src="{{ asset('frontend-website/assets/website/icon.png') }}" alt="Create Order" class="img-fluid" width="50">
                    </div>
                    <h4 class="main-text fs-custom-24 mt-4">{{ __('message.create_an_order') }}</h4>
                    <p class="gray-text centered-paragraph">
                       {{ __('message.enter_the_pickup_and_drop-off_details') }}
                    </p>  
                </div>
    
                <div class="col-12 col-md-6 col-lg-3 mb-4 step-box">
                    <small class="main-text d-block mb-2">{{ __('message.step_2') }}</small>
                    <div class="mb-3 mt-4">
                        <img src="{{ asset('frontend-website/assets/website/icon-1.png') }}" alt="Assigned Order" class="img-fluid" width="50">
                    </div>
                    <h4 class="main-text fs-custom-24 mt-4">{{ __('message.assigned_order') }}</h4>
                    <p class="gray-text centered-paragraph">{{ __('message.assigns_a_nearby_delivery_partner_to_pick_up_your_package') }}</p>
                </div>
    
                <div class="col-12 col-md-6 col-lg-3 mb-4 step-box">
                    <small class="main-text d-block mb-2">{{ __('message.step_3') }}</small>
                    <div class="mb-3 mt-4">
                        <img src="{{ asset('frontend-website/assets/website/icon-2.png') }}" alt="Pick Order" class="img-fluid" width="50">
                    </div>
                    <h4 class="main-text fs-custom-24 mt-4">{{ __('message.pick_order') }}</h4>
                    <p class="gray-text centered-paragraph">{{ __('message.courier_collects_your_parcel_and_starts_the_journey') }}</p>
                </div>
    
                <div class="col-12 col-md-6 col-lg-3 mb-4 step-box">
                    <small class="main-text d-block mb-2">{{ __('message.step_4') }}</small>
                    <div class="mb-3 mt-4">
                        <img src="{{ asset('frontend-website/assets/website/icon-3.png') }}" alt="Delivered Order" class="img-fluid" width="50">
                    </div>
                    <h4 class="main-text fs-custom-24 mt-4">{{ __('message.delivered_order') }}</h4>
                    <p class="gray-text centered-paragraph">{{ __('message.track_your_package_in_real_time') }}</p>
                </div>
            </div>
        </div>
    </section>


    @if (!empty($data['app_overview']['title']) || !empty($data['app_overview']['subtitle']) )
        <section class="pt-5 mb-5">
            <div class="px-3">
                <div class="container">
                <div class="text-center mb-5">
                    <h2 class="display-5 main-text mt-5 font-weight-500 mb-1">
                    <span>{{ $data['app_overview']['title'] }}</span> <span class="site-color ">{{ $data['app_overview']['subtitle'] }}</span>
                    </h2>
                </div>
            </div>
        </section>
    @endif
    
    @foreach($data['sections'] as $index => $section)
        <section class="pt-3 mb-5">
            <div class="px-3">
                <div class="container">
                    <div class="row align-items-center pt-5 px-4 radius-20 {{ $index % 2 == 0 ? 'section-bg-color' : 'section-bg-color-two' }}">

                        @if($index % 2 == 0)
                            <div class="col-md-7 mb-4 mb-md-0">
                                <h3 class="site-color fs-custom-40 font-weight-500">{{ $section->title }}</h3>
                                <h4 class="site-color fs-custom-40 font-weight-500 mb-4">{{ $section->subtitle }}</h4>
                                @foreach($section->websitesectiontitles as $item)
                                    <ul class="fs-5 section-li">
                                        <li class="mb-3 fs-custom-22">{{ $item->title }}</li>
                                    </ul>
                                @endforeach
                            </div>
                        
                            <div class="col-md-5 text-center">
                                @if(getSingleMediaSettingImage($section, 'section_image'))
                                    <img src="{{ getSingleMediaSettingImage($section, 'section_image') }}" class="img-fluid section-img" alt="App">
                                @endif
                            </div>
                        @else
                            <div class="col-md-7 mb-4 mb-md-0">
                                <h3 class="site-color fs-custom-40 font-weight-500">{{ $section->title }}</h3>
                                <h4 class="site-color fs-custom-40 font-weight-500 mb-4">{{ $section->subtitle }}</h4>
                                
                                @foreach($section->websitesectiontitles as $item)
                                    <ul class="fs-5 section-li">
                                        <li class="mb-3 fs-custom-22">{{ $item->title }}</li>
                                    </ul>
                                @endforeach
                                </div>
                        
                                <div class="col-md-5 text-center">
                                @if(getSingleMediaSettingImage($section, 'section_image'))
                                    <img src="{{ getSingleMediaSettingImage($section, 'section_image') }}" class="img-fluid section-img" alt="App">
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endforeach

    {{-- START MOBILE SECTION --}}
    @if (!empty($data['download_app']['download_title']) || !empty($data['download_app']['download_subtitle']) || !empty($data['download_app']['download_description']) || !empty($data['download_app']['download_app_logo']) || !empty($data['download_app']['appstore_image']) || !empty($data['download_app']['playstore_image']))
        <section class="py-0 py-md-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 d-flex justify-content-center">
                        <img src="{{ $data['download_app']['download_app_logo'] }}" class="img-fluid mobile-img">
                    </div>
                    <div class="col-lg-6 my-auto mt-5 py-4">
                        <span class="display-5 font-weight-500">{{ $data['download_app']['download_title'] }}</span>
                        <span class="display-5 font-weight-500 site-color">{{ $data['download_app']['download_subtitle'] }}</span>
                        <p class="mt-3 fs-custom-18 gray-text">{{ $data['download_app']['download_description'] }}</p>
                        <div class="d-flex gap-3">
                            @if (!empty($data['download_app']['appstore_image']))
                                <a href="{{ $data['download_app']['appstore_url']['url'] }}" {{ $data['download_app']['appstore_url']['target'] }} class="text-decoration-none">
                                    <img src="{{ $data['download_app']['appstore_image'] }}" alt="App QR">
                                </a>
                            @endif
                            @if (!empty($data['download_app']['playstore_image']))
                                <a href="{{ $data['download_app']['playstore_url']['url'] }}" {{ $data['download_app']['playstore_url']['target'] }} class="text-decoration-none">
                                    <img src="{{ $data['download_app']['playstore_image'] }}" alt="Play QR">
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
    {{-- END MOBILE SECTION  --}}
      
    {{-- START CLIENT REVIEW SECTION --}}
    <section class="client-review light-bg-color">
        <div class="container overflow-hidden py-5">
            <div class="row">
                <div class="col-md-6">
                    <div>
                        <h3 class="display-5 font-weight-500 main-text">{{ $data['client-testimonial']['title'] }} 
                            <span class="display-5 font-weight-500 site-color">{{ $data['client-testimonial']['subtitle'] }}</span>
                        </h3>
                    </div>
                    <div class="d-flex row">
                        <div class="col-sm-4 col-lg-4 d-flex align-items-center rating-section">
                            <div class="mt-3 rating-content">
                                {!! renderStars($data['client-testimonial']['appstore_review'] ?? 0) !!}
                                <h6 class="mt-2 review">{{ $data['client-testimonial']['appstore_review'] }}/5 - {{ $data['client-testimonial']['playstore_totalreview'] }}+ {{ __('message.reviews') }}</h6>
                            </div>
                            <a class="text-decoration-none me-lg-5" href="{{ $data['download_app']['appstore_url']['url'] }}" {{ $data['download_app']['appstore_url']['target'] }}>
                                <img src="{{ asset('frontend-website/assets/website/app.png') }}" alt="App Store" class="img-fluid">
                            </a>
                        </div>
                        <div class="col-sm-4 col-lg-4 d-flex align-items-center rating-section">
                            <div class="mt-3 rating-content">
                                {!! renderStars($data['client-testimonial']['playstore_review'] ?? 0) !!}
                                <h6 class="mt-2 review">{{ $data['client-testimonial']['playstore_review'] }}/5 - {{ $data['client-testimonial']['appstore_totalreview'] }}+ {{ __('message.reviews') }}</h6>
                            </div>
                            <a class="text-decoration-none me-lg-5" href="{{ $data['download_app']['playstore_url']['url'] }}" {{ $data['download_app']['playstore_url']['target'] }}>
                                <img src="{{ asset('frontend-website/assets/website/play.png') }}" alt="Play Store" class="img-fluid">
                            </a>
                        </div>
                        <div class="col-sm-4 col-lg-4 d-flex align-items-center rating-section">
                            <div class="mt-3 rating-content">
                                {!! renderStars($data['client-testimonial']['trustpilot_review'] ?? 0) !!}
                                <h6 class="mt-2 review">{{ $data['client-testimonial']['trustpilot_totalreview'] }}+ {{ __('message.reviews') }}</h6>
                            </div>
                            <a class="text-decoration-none me-lg-5" href="{{ $data['download_app']['trustpilot_url']['url'] }}" {{ $data['download_app']['trustpilot_url']['target'] }}>
                                <img src="{{ asset('frontend-website/assets/website/trust.png') }}" alt="Trust Pilot" class="img-fluid">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div id="client-slider" class="owl-carousel">
                    @foreach ($client_testimonial as $index => $client_review)
                        <div class="card mb-3 border-0 fixed-card">
                            <div class="row g-0">
                                <div class="col-md-3 d-flex justify-content-center align-items-center position-relative client-img-card">
                                    <img src="{{ getSingleMediaSettingImage($client_review->id != null ? $client_review : null ,'frontend_data_image','client_testimonial_image') }}" alt="mightydelivery" class="client-img">
                                    <div class="d-md-block d-none vertical-line"></div>
                                </div>
                                <div class="d-md-none client-slider-line">
                                    <div class="horizontal-line"></div>
                                </div>
                                <div class="col-md-9">
                                    <div class="card-body client-card-body mb-1">
                                        <h5 class="card-title mb-1 title-color text-truncate">{{ $client_review->title }}</h5>
                                        <h6 class="title-color">{{ $client_review->subtitle }}</h6>
                                        <p class="card-text mt-3">{{ $client_review->description }} </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    {{-- SATRT NEWSLETTER SECTION --}}


    @if (!empty($data['courier_recruitment']['courier_title']) || !empty($data['courier_recruitment']['courier_description']) )
        <section class="py-5 py-md-5 px-2">
            <div class="container site-bg-color text-white radius-20">
                <div class="row align-items-center courier-section">
                    <div class="col-12 col-md-4 col-lg-3 text-center mb-3 mb-md-0 ">
                        <img src="{{ $data['courier_recruitment']['courier_image'] }}" class="img-fluid courier-image">
                    </div>
                    
                    <div class="col-12 col-md-6 col-lg-7 text-content">
                        <h2 class="fs-custom-40 mb-3 mb-md-4">
                            {{ $data['courier_recruitment']['courier_title'] }}
                        </h2>
                        <p class="mb-0 fs-6 lh-base"> {{ $data['courier_recruitment']['courier_description'] }}</p>
                    </div>
                    
                    <div class="col-12 col-md-2 col-lg-2 text-center mt-3 mt-md-0">
                        <a href="{{ route('deliverypartner') }}" class="btn btn-light site-color w-100 fs-custom-400">
                            {{ __('message.im_in') }}
                            <svg width="20" height="20" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 18.5L18 6.5M18 6.5H9M18 6.5V15.5" stroke="var(--site-color)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endif
    
    
</x-frontand-layout>
