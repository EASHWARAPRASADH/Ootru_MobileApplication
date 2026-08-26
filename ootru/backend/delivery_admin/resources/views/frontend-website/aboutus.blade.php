<x-frontand-layout :assets="$assets ?? []">

  <main class="padding-top-80">
    <section class="light-bg-color py-5">
      <div class="container">
        <div class="row">
          <div class="col">
            <h4 class="fs-custom-48 main-text font-weight-500 mb-2">{{ __('message.about_us') }}</h4>
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
              <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                  <a class="text-decoration-none gray-text" href="{{ route('frontend-section') }}">{{ __('message.home') }}</a>
                </li>
                <li class="breadcrumb-item active gray-text" aria-current="page">{{ __('message.about_us') }}</li>
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
                <h2 class="font-weight-500 display-5 main-text">{{ $data['about_us']['download_title'] }} <span class="site-color">{{ $data['about_us']['download_subtitle'] }}</span></h2>
                <p class="mt-3 main-text fs-custom-18">
                    {{ $data['about_us']['long_des'] }}
                </p>
                <p class="mt-4 gray-text fs-custom-18">
                    {{ __('message.scan_qr_code_or_click_to_redirect_our_app') }}
                </p>
                <div class="d-flex gap-3">
                    @if (!empty($data['about_us']['appstore_image']))
                        <a href="{{ $data['about_us']['appstore_url']['url'] }}" {{ $data['about_us']['appstore_url']['target'] }} class="text-decoration-none">
                            <img src="{{ $data['about_us']['appstore_image'] }}" alt="App QR">
                        </a>
                    @endif
                    @if (!empty($data['about_us']['playstore_image']))
                        <a href="{{ $data['about_us']['playstore_url']['url'] }}" {{ $data['about_us']['playstore_url']['target'] }} class="text-decoration-none">
                            <img src="{{ $data['about_us']['playstore_image'] }}" alt="Play QR">
                        </a>
                    @endif
                </div>
            </div>
            <div class="col-lg-6 text-center mt-4 mt-lg-0">
                @if (!empty($data['about_us']['about_us_app_ss']))
                  <img src="{{ $data['about_us']['about_us_app_ss'] }}" class="img-fluid radius-20"  alt="Conatct">
              @endif
            </div>
          </div>
      </div>
    </section>
  </main>

</x-frontand-layout>
