<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ mighty_language_direction() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ getSingleMedia(appSettingData('get'), 'site_favicon', null) }}">
    <link rel="stylesheet" href="{{ asset('frontend-website/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/@fortawesome/fontawesome-free/css/all.min.css') }}" />
    <link href="{{ asset('frontend-website/assets/css/bootstrap.min.css') }}" rel="stylesheet">
    @if (mighty_language_direction() == 'rtl')
        <link rel="stylesheet" href="{{ asset('css/rtl.css') }}">
    @endif
    <style>
        :root {
            --site-color: {{ $themeColor }};
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6 col-md-6">
                <div class="d-flex justify-content-center align-items-center admin-login-container">
                    <img src="{{ getSingleMediaSettingImage(getSettingFirstData('app_content', 'app_logo_image'), 'app_logo_image') }}"
                        class="rounded-circle p-3 admin-logo-img" width="100">
                </div>
                <div class="mt-3 offset-lg-2 offset-md-1 offset-sm-2 mb-3">
                    <h2>{{ __('message.verify_otp') }}</h2>
                    <p class="mb-1 mt-3">{{ __('message.welcome_to') }}
                        {{ SettingData('app_content', 'app_name') }}<span> 🛵</span></p>
                    <p class="admin-p">{{ __('message.please_enter_otp') }}</p>
                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                </div>
                <div class="offset-lg-2 offset-md-1 offset-sm-2 mt-5">
                    <form method="POST" action="{{ route('verify-otp-post') }}" data-toggle="validator">
                        <input type="hidden" name="admin_login" value="admin_login">
                        {{ csrf_field() }}
                        <div class="mb-3 row">
                            <label for="verifyOtp"
                                class="col-form-label admin-email-label">{{ __('message.otp') }}</label>
                            <div class="col">
                                <input type="text" hidden  id="email" name="email" value="{{ $user_email }}">
                                <input type="text" class="form-control adminlogin-form" id="verifyOtp" name="otp" required autofocus>
                            </div>
                        </div>
                        <div class="mb-3 row admin-login-row">
                            <div class="col offset-2 d-flex justify-content-end mt-4">
                                <button type="submit"
                                    class="btn text-white fw-bold admin-login-btn">{{ __('message.verify_otp') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 vh-100 login-carousel">
                <div id="carouselExampleCaptions" class="carousel slide login-carousel-slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach (count($walkthrough_data) > 0 ? $walkthrough_data : [(object) ['id' => '', 'title' => DummyData('dummy_title'), 'description' => DummyData('dummy_description')]] as $key => $item)
                            <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                             
                                <img src="{{ getSingleMediaSettingImage($item->id != null ? $item : null, 'frontend_data_image', 'walkthrough_image') }}"
                                    alt="slider" class="d-block mx-auto w-75">
                                <div class="text-center text-white">
                                    <h5 class="admin-title">{{ $item->title }}</h5>
                                    <p>{{ $item->description }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if (count($walkthrough_data) > 1)
                        <button class="carousel-control-prev d-flex align-items-end" type="button"
                            data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">{{ __('message.previous') }}</span>
                        </button>
                        <button class="carousel-control-next d-flex  align-items-end" type="button"
                            data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">{{ __('message.next') }}</span>
                        </button>
                    @endif
                </div>
            </div>

        </div>
    </div>
    <script src="{{ asset('frontend-website/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('frontend-website/assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('frontend-website/assets/js/bootstrap.min.js') }}"></script>
</body>

</html>
