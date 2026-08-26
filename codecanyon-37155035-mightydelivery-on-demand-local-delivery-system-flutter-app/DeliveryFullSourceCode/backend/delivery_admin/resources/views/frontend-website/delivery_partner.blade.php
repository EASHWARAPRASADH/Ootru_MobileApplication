<x-frontand-layout :assets="$assets ?? []">

    <section class="light-bg-color">
        <div class="container">
            <div class="row align-items-center mt-5">
                <div class="col-lg-6 text-center text-lg-start mb-4 mb-lg-0 py-4">
                    <h2 class="font-weight-500 display-5 main-text">{{ $data['delivery_job']['title'] }} <span class="site-color">{{ $data['delivery_job']['subtitle'] }}</span></h2>
                    <p class="gray-text fs-custom-18 mt-3">{{ $data['delivery_partner']['description'] }}</p>
                    <div class="d-flex flex-wrap justify-content-center justify-content-lg-start mt-4 gap-4 align-items-center">
                        @if ((!empty($data['download_app']['playstore_url']['url']) && $data['download_app']['playstore_url']['url'] !== 'javascript:void(0)') || (!empty($data['download_app']['appstore_url']['url']) && $data['download_app']['appstore_url']['url'] !== 'javascript:void(0)'))
                            <div class="d-flex flex-column align-items-center gap-3">
                                @if (!empty($data['download_app']['playstore_url']['url']) && $data['download_app']['playstore_url']['url'] !== 'javascript:void(0)')
                                    <a href="{{ $data['download_app']['playstore_url']['url'] }}"
                                        {{ $data['download_app']['playstore_url']['target'] ?? '' }}>
                                        <img class="social-icon" src="{{ asset('frontend-website/assets/website/ic_play_store.png') }}" alt="Google Play" height="45">
                                    </a>
                                @endif

                                @if (!empty($data['download_app']['appstore_url']['url']) && $data['download_app']['appstore_url']['url'] !== 'javascript:void(0)')
                                    <a href="{{ $data['download_app']['appstore_url']['url'] }}"
                                        {{ $data['download_app']['appstore_url']['target'] ?? '' }}>
                                        <img class="social-icon"src="{{ asset('frontend-website/assets/website/ic_app_store.png') }}" alt="App Store" height="45">
                                    </a>
                                @endif
                            </div>
                        @endif

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
                <div class="col-lg-6 text-center">
                    <div class="position-relative">
                        @if (!empty($data['delivery_job']['delivery_job_image']))
                            <img src="{{ getSingleMediaSettingImage(getSettingFirstData('delivery_job', 'delivery_job_image'), 'delivery_job_image') }}" class="img-fluid w-100 delivery-job-img" alt="Delivery Partner">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if (!empty($delivery_data) && count($delivery_data) > 0)
        <section class="py-5 text-center">
            <div class="container">
                <h2 class="font-weight-500 display-5 main-text">{{ $data['delivery_partner']['title'] }} <span class="site-color">{{ $data['delivery_partner']['subtitle'] }}</span></h2>
                <p class="mb-5 gray-text fs-custom-18 text-center mx-auto delivery-partner-p">
                    {{ $data['delivery_partner']['description'] }}
                </p>
                <div class="row g-4">
                    @foreach ($delivery_data as $item)
                        <div class="col-sm-6 col-lg-4">
                            <div class="p-4 light-bg-color h-100 radius-20">
                                @if ($item->getFirstMedia('frontend_data_image'))
                                    <img src="{{ $item->getFirstMedia('frontend_data_image')->getUrl() }}" alt="Delivery Image" height="245px" width="245px">
                                @endif
                                <h4 class="main-text">{{ $item->title }}</h4>
                                <p class="gray-text">{{ $item->subtitle }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if (!empty($document_verification) && count($document_verification) > 0)
        <section class="py-5 text-center light-bg-color">
            <div class="container">
                <h2 class="font-weight-500 display-5 main-text">{{ $data['document_verification']['title'] }}
                    <span class="site-color">{{ $data['document_verification']['subtitle'] }}</span>
                </h2>
                <p class="mx-auto fs-custom-18 gray-text mt-4 paragraph-maxwidth">
                    {{ $data['document_verification']['description'] }}
                </p>
                <div class="row g-4 justify-content-center mt-4">
                    @foreach ($document_verification as $item)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 light-bg-color">
                                <div class="card-body text-center">
                                    <div class="d-flex align-items-center justify-content-center mb-3 gap-2">
                                        <div class="step-circle d-flex justify-content-center align-items-center white-bg-color main-text">
                                            {{ $loop->iteration ?? '' }}
                                        </div>
                                        <div class="fs-custom-24 main-text">
                                            {{ $item->title }}
                                        </div>
                                    </div>
                                    <img src="{{ getSingleMediaSettingImage($item->id != null ? $item : null, 'frontend_data_image', 'doucment_image') }}" alt="mightydelivery" class="img-fluid rounded">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if (!empty($data['delivery_man_section']['title']) || !empty($data['delivery_man_section']['subtitle']) )
        <section class="pt-5 mb-5">
            <div class="px-3">
                <div class="container">
                <div class="text-center mb-5">
                    <h2 class="display-5 main-text mt-5 font-weight-500 mb-1">
                    <span>{{ $data['delivery_man_section']['title'] }}</span> <span class="site-color">{{ $data['delivery_man_section']['subtitle'] }}</span>
                    </h2>
                </div>
            </div>
        </section>
    @endif

    @foreach ($data['sections'] as $index => $section)
        <section class="pt-3 mb-5">
            <div class="px-3">
                <div class="container">
                    <div class="row align-items-center pt-5 px-4 radius-20 {{ $index % 2 == 0 ? 'section-bg-color' : 'section-bg-color-two' }}">
                        @if ($index % 2 == 0)
                            <div class="col-md-6 mb-4 mb-md-0">
                                <h3 class="site-color fs-custom-40 font-weight-500">{{ $section->title }}</h3>
                                <h4 class="site-color fs-custom-40 font-weight-500 mb-4">{{ $section->subtitle }}</h4>
                                @foreach ($section->deliverymansectiontitles as $item)
                                    <ul class="fs-5 list-unstyled section-li">
                                        <li class="mb-3 fs-custom-22">{{ $item->title }}</li>
                                    </ul>
                                @endforeach
                            </div>

                            <div class="col-md-6 text-center">
                                @if (getSingleMediaSettingImage($section, 'delivery_man_section_image'))
                                    <img src="{{ getSingleMediaSettingImage($section, 'delivery_man_section_image') }}" class="img-fluid section-img" alt="App">
                                @endif
                            </div>
                        @else
                            <div class="col-md-6 text-center">
                                @if (getSingleMediaSettingImage($section, 'delivery_man_section_image'))
                                    <img src="{{ getSingleMediaSettingImage($section, 'delivery_man_section_image') }}" class="img-fluid section-img" alt="App">
                                @endif
                            </div>
                            <div class="col-md-6 mb-4 mb-md-0">
                                <h3 class="site-color fs-custom-40 font-weight-500">{{ $section->title }}</h3>
                                <h4 class="site-color fs-custom-40 font-weight-500 mb-4">{{ $section->subtitle }}</h4>

                                @foreach ($section->deliverymansectiontitles as $item)
                                    <ul class="fs-5 list-unstyled section-li">
                                        <li class="mb-3 fs-custom-22">{{ $item->title }}</li>
                                    </ul>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endforeach

    @if (!empty($deliver_your_way) && count($deliver_your_way) > 0)
        <section class="py-0 py-md-5 text-center bg-light">
            <div class="container py-5">
                <h2 class="font-weight-500 display-5 main-text">
                    {{ $data['deliver_your_way']['title'] }}
                    <span class="site-color">{{ $data['deliver_your_way']['subtitle'] }}</span>
                </h2>
                <p class="mx-auto fs-custom-18 gray-text mt-4 paragraph-maxwidth">
                    {{ $data['deliver_your_way']['description'] }}
                </p>
                <div class="row g-4 justify-content-center mt-3">
                    @foreach ($deliver_your_way as $item)
                        <div class="col-12 col-md-4 col-lg-3">
                            <div class="white-bg-color radius-20 d-flex justify-content-center align-items-center delivery-div">
                                <img src="{{ getSingleMediaSettingImage($item->id != null ? $item : null ,'frontend_data_image') }}" alt="mightydelivery" class="img-fluid deliver-img">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
    
    {{-- START MOBILE SECTION --}}
    @if (!empty($data['download_app']['download_title']) || !empty($data['download_app']['download_subtitle']) || !empty($data['download_app']['download_description']) || !empty($data['download_app']['download_app_logo']) || !empty($data['download_app']['appstore_image']) || !empty($data['download_app']['playstore_image']))
        <section class="py-0 py-md-5">
            <div class="container py-5">
                <div class="row">
                    <div class="col-lg-6 d-flex justify-content-center">
                        <img src="{{ $data['download_app']['download_app_logo'] }}" class="img-fluid mobile-img">
                    </div>
                    <div class="col-lg-6 my-auto mt-5 py-4">
                        <span class="display-5 font-weight-500">{{ $data['download_app']['download_title'] }}</span>
                        <span class="display-5 font-weight-500 site-color">{{ $data['download_app']['download_subtitle'] }}</span>
                        <p class="mt-3 fs-custom-18 gray-text">{{ $data['download_app']['download_description'] }}</p>
                        @if (!empty($data['download_app']['appstore_image']))
                            <a href="{{ $data['download_app']['appstore_url']['url'] }}" {{ $data['download_app']['appstore_url']['target'] }} class="text-decoration-none">
                                <img src="{{ $data['download_app']['appstore_image'] }}" class="mt-3 me-2" alt="App QR">
                            </a>
                        @endif
                        @if (!empty($data['download_app']['playstore_image']))
                            <a href="{{ $data['download_app']['playstore_url']['url'] }}" {{ $data['download_app']['playstore_url']['target'] }} class="text-decoration-none">
                                <img src="{{ $data['download_app']['playstore_image'] }}" class="mt-3 mr-2" alt="Play QR">
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endif
    {{-- END MOBILE SECTION  --}}

</x-frontand-layout>
