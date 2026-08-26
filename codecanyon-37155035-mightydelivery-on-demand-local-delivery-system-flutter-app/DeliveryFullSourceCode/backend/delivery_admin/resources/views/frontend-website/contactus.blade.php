<x-frontand-layout :assets="$assets ?? []">
    
    <main class="padding-top-80">
        <section class="light-bg-color py-5">
          <div class="container">
            <div class="row">
              <div class="col">
                <h4 class="fs-custom-48 main-text font-weight-500 mb-2">{{ __('message.contact_us') }}</h4>
                <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                  <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                      <a class="text-decoration-none gray-text" href="{{ route('frontend-section') }}">{{ __('message.home') }}</a>
                    </li>
                    <li class="breadcrumb-item active gray-text" aria-current="page">{{ __('message.contact_us') }}</li>
                  </ol>
                </nav>
              </div>
            </div>
          </div>
        </section>
    
        <section>
          <div class="container py-5">
              <div class="row align-items-center mt-4">
                <div class="col-lg-6">
                    <h2 class="font-weight-500 display-5 main-text">{{ $data['contact_us']['contact_title'] }} <span class="site-color">{{ $data['contact_us']['contact_subtitle'] }}</span></h2>
                    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between radius-12 px-4 py-2 my-3 contactus-section">
                        <div class="d-flex align-items-center mb-2 mb-md-0 me-md-4">
                            <svg class="me-2" width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.5 15C2.5 10.286 2.5 7.92893 3.96447 6.46447C5.42893 5 7.78595 5 12.5 5H17.5C22.214 5 24.5711 5 26.0355 6.46447C27.5 7.92893 27.5 10.286 27.5 15C27.5 19.714 27.5 22.0711 26.0355 23.5355C24.5711 25 22.214 25 17.5 25H12.5C7.78595 25 5.42893 25 3.96447 23.5355C2.5 22.0711 2.5 19.714 2.5 15Z" stroke="#303030" stroke-width="1.875"/>
                                <path d="M7.5 10L10.1986 12.2489C12.4944 14.162 13.6423 15.1186 15 15.1186C16.3577 15.1186 17.5056 14.162 19.8014 12.2488L22.5 10" stroke="#303030" stroke-width="1.875" stroke-linecap="round"/>
                            </svg>
                            <a class="main-text text-decoration-none"
                                href="{{ $data['app_setting_data']->support_email ?? 'javascript:void(0)' }}"
                                {{ $data['app_setting_data']->support_email != null ? 'target="_blank"' : '' }}>
                                {{ $data['app_setting_data']->support_email ?? '' }}
                            </a>
                        </div>
                        <div class="border-start px-3 d-flex align-items-center">
                            <svg class="me-2" width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12.547 6.64521L13.3583 8.09888C14.0904 9.41073 13.7965 11.1317 12.6434 12.2848C12.6434 12.2848 11.2447 13.6834 13.7806 16.2194C16.3165 18.7552 17.7152 17.3566 17.7152 17.3566C18.8683 16.2035 20.5893 15.9096 21.9011 16.6417L23.3548 17.453C25.3357 18.5585 25.5696 21.3366 23.8285 23.0777C22.7822 24.124 21.5005 24.9381 20.0836 24.9918C17.6985 25.0822 13.6478 24.4786 9.58463 20.4154C5.52141 16.3521 4.91777 12.3015 5.00819 9.91636C5.0619 8.4995 5.876 7.21779 6.92226 6.17154C8.66344 4.43035 11.4415 4.6643 12.547 6.64521Z" stroke="#303030" stroke-width="1.875" stroke-linecap="round"/>
                            </svg>
                            <a href="{{ $data['app_setting_data']->support_number ? 'tel:' . $data['app_setting_data']->support_number : 'javascript:void(0)' }}" {{ $data['app_setting_data']->support_number ? 'target="_blank"' : '' }} class="main-text text-decoration-none">
                                {{ $data['app_setting_data']->support_number ?? '' }}
                            </a>
                        </div>
                    </div>
                    <p class="mt-2 gray-text fs-custom-18">
                        {{ __('message.scan_qr_code_or_click_to_redirect_our_app') }}
                    </p>
                    <div class="d-flex gap-3">
                        @if (!empty($data['contact_us']['appstore_image']))
                            <a href="{{ $data['contact_us']['appstore_url']['url'] }}" {{ $data['contact_us']['appstore_url']['target'] }} class="text-decoration-none">
                                <img src="{{ $data['contact_us']['appstore_image'] }}" alt="App QR">
                            </a>
                        @endif
                        @if (!empty($data['contact_us']['playstore_image']))
                            <a href="{{ $data['contact_us']['playstore_url']['url'] }}" {{ $data['contact_us']['playstore_url']['target'] }} class="text-decoration-none">
                                <img src="{{ $data['contact_us']['playstore_image'] }}" alt="Play QR">
                            </a>
                        @endif
                    </div>
                </div>
                <div class="col-lg-6 text-center mt-4 mt-lg-0">
                    @if (!empty($data['contact_us']['contact_us_app_ss']))
                        <img src="{{ $data['contact_us']['contact_us_app_ss'] }}" class="img-fluid radius-20"  alt="Conatct">
                    @endif
                </div>
              </div>
          </div>
        </section>
      </main>

</x-frontand-layout>
