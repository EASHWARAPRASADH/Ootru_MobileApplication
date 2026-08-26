@php
    $app_settings =  App\Models\AppSetting::first();
@endphp
<button onclick="topFunction()" id="myBtn" title="Go to top"><i class="fas fa-arrow-up"></i></button>

<nav class="navbar navbar-expand-lg navbar-light white-bg-color px-3 fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('frontend-section') }}">
            @if(getSingleMediaSettingImage(getSettingFirstData('app_content','app_logo_image'),'app_logo_image'))
                <img src="{{ getSingleMediaSettingImage(getSettingFirstData('app_content','app_logo_image'),'app_logo_image') }}" height="40" width="40">
            @endif
            <span class="site-color font-weight-500 fs-custom-20 line-height-30 ms-1" id="item_nav1">{{ SettingData('app_content', 'app_name') }}</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="mainNavbar">
            <ul class="navbar-nav align-items-center mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('deliverypartner') }}">
                        <label class="gray-text" id="item_nav1">{{ __('message.become_a_delivery_boy') }}</label>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('about-us') }}">
                        <label class="gray-text me-2" id="item_nav1">{{ __('message.about_us') }}</label>
                    </a>
                </li>
                <li class="nav-item">
                    @if(Auth::check() && Auth::user()->user_type == 'client')
                        <a class="nav-link site-color menu-item" href="{{ route('home') }}">
                            <i class="fa-solid fa-tachometer-alt site-color me-1"></i>
                            <label for="order_track" id="item_nav1">{{ __('message.dashboard') }}</label>
                        </a>
                    @elseif(Auth::check() && Auth::user()->user_type == 'admin')
                        <a class="nav-link site-color menu-item" href="{{ route('home') }}">
                            <i class="fa-solid fa-tachometer-alt site-color me-1"></i>
                            <label for="order_track" id="item_nav1">{{ __('message.dashboard') }}</label>
                        </a>
                    @else
                    {{-- @if($app_settings->admin_login_button == 0)
                        <a class="nav-link site-color menu-item" href="{{ route('admin-login') }}">
                            <i class="fa-solid fa-user site-color me-1"></i>
                            <label for="order_track" id="item_nav">{{ __('message.admin') }}</label>
                        </a>
                    @endif --}}
                    @endif
                </li>

                @if(Auth::check() && Auth::user()->user_type == 'client')
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="javascript:void(0)" class="btn sing-out btn-sm mt-1 ms-2" onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('message.sign_out') }}
                        </a>
                    </form>
                </li>
                @elseif (Auth::check() && Auth::user()->user_type == 'admin')
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="javascript:void(0)" class="btn sing-out btn-sm ms-2" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('message.sign_out') }}
                            </a>
                        </form>
                    </li>
                @else
                    <li class="nav-item">
                        <div class="btn nav-border px-2 py-1 radius-4 d-inline-flex gap-2">
                            <a href="#" class="text-decoration-none text-dark" data-bs-toggle="modal" data-bs-target="#signinModal">{{ __('message.login') }}</a>
                            |
                            <a href="#" class="text-decoration-none text-dark" data-bs-toggle="modal" data-bs-target="#signupModal">{{ __('message.register') }}</a>
                        </div>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
 <!-- END NAVBAR -->

 <!-- START MODAL -->

 <!-- START SIGN IN  -->
 <div class="modal fade " id="signinModal" tabindex="-1" aria-labelledby="signinModalLabel" aria-hidden="true" data-bs-backdrop="static">
     <div class="modal-dialog modal-dialog-centered modal-lg">
         <div class="modal-content signin-modalcontent">
             <div class="modal-header border-bottom-0 signin-modalhead">
                 <div>
                     <h5 class="modal-title mb-0 fw-bold sign-in-h5" id="signinModalLabel">{{ __('message.sign_in') }}
                     </h5>
                     <p class="mt-2 mb-0 sign-in-label">{{ __('message.dont_have_account') }} <a href="#" id="signupModalId"
                             data-bs-toggle="modal" data-bs-target="#signupModal"
                             class="text-decoration-none fw-bold sing-up-p">{{ __('message.sign_up') }}</a>
                     </p>
                 </div>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body signin-modalbody">
                 <form id="signinForm" method="POST" action="{{ route('login.store') }}">
                     {{ csrf_field() }}
                     <input type="hidden" name="signinModal" value="signinModal">
                     <div class="mb-3">
                         <label for="email" class="col-form-label sign-in-label">{{ __('message.email') }}</label>
                         <input type="text" class="form-control signin-form" id="email" name="email">
                     </div>
                     <div class="mb-1">
                         <label for="input-password" class="col-form-label sign-in-label">{{ __('message.password') }}</label>
                         <div class="password-container">
                             <input type="password" class="form-control signin-form input-password password"
                                 name="password">
                             <i class="toggle-password fas fa-eye-slash togglePassword"></i>
                         </div>
                     </div>
                     <div class="mb-1 mt-3 d-flex align-items-center signin-check">
                         <div class="form-check">
                             <input type="checkbox" class="form-check-input signin-check-input" id="customCheck1" name="remember">
                             <label class="form-check-label ms-2 sign-in-label" for="customCheck1">{{ __('message.remember_me') }}</label>
                         </div>
                         <div class="ms-auto">
                             <a href="#" class="text-decoration-none sign-in-label sing-up-p"
                                 id="forgotmodal-link">{{ __('message.forgot_password') }}</a>
                         </div>
                     </div>

                     <div class="mb-3 mt-3 form-check signin-check">
                         <input type="checkbox" class="form-check-input signin-check-input" id="terms_privacy_checkbox"
                             name="checkbox">
                         <label class="form-check-label ms-2" for="terms_privacy_checkbox">{{ __('message.i_agree_the') }} <a
                                 href="{{ route('termofservice') }}"
                                 class="text-decoration-none fw-bold sing-up-p">{{ __('message.terms_of_service') }}</a> & <a
                                 href="{{ route('privacypolicy') }}"
                                 class="text-decoration-none fw-bold sing-up-p">{{ __('message.privacy_policy') }}</a>
                         </label>
                     </div>
                     <button type="submit" class="btn w-100 text-white mt-2 fw-bold sign-in-btn">{{ __('message.sign_in') }}</button>
                 </form>
                 <div class="text-center mt-2 mb-3">
                     <a href="https://accounts.google.com/o/oauth2/v2/auth/oauthchooseaccount?gsiwebsdk=3&client_id=12372904825-t74p1k9i31vtnhkte1k4avots246aa3r.apps.googleusercontent.com&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email&redirect_uri=storagerelay%3A%2F%2Fhttps%2Flocaldeliverysystem.meetmighty.com%3Fid%3Dauth174305&prompt=select_account&response_type=token&include_granted_scopes=true&enable_granular_consent=true&service=lso&o2v=2&theme=mn&ddm=0&flowName=GeneralOAuthFlow"
                         class="d-inline-block text-decoration-none">
                         {{-- <div class="d-flex align-items-center mt-2 sign-in-with-google">
                             <img src="{{ asset('frontend-website/assets/icons/ic_google.png') }}"
                                 class="sign-in-with-google-img" alt="login" width="20">
                             <p class="m-0 ms-2">Sign in with Google</p>
                         </div> --}}
                     </a>
                 </div>
             </div>
         </div>
     </div>
 </div>
 <!-- END SIGN IN  -->

 <!-- START SIGN UP -->
 <div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true"
     data-bs-backdrop="static">
     <div class="modal-dialog modal-dialog-centered modal-lg">
         <div class="modal-content signup-modalcontent">
             <div class="modal-header border-bottom-0 signup-modalhead">
                 <div>
                     <h5 class="modal-title mb-0 fw-bold sign-up-purle" id="signupModalLabel">{{ __('message.sign_up') }}</h5>
                     <p class="mt-2 mb-0 sing-up-font">{{ __('message.sign_up_your_account') }} </p>
                 </div>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body signup-modalbody">
                 <form id="signupForm" method="POST" action="{{ route('client.store') }}">
                     {{ csrf_field() }}
                     <div class="row">
                         <div class="col-lg-6">
                             <div class="mb-3">
                                 <label for="name" class="col-form-label sing-up-font">{{ __('message.name') }}</label>
                                 <span class="text-danger">*</span>
                                 <input type="text" class="form-control signup-form" id="name"
                                     name="name">
                             </div>
                         </div>
                         <div class="col-lg-6">
                             <div class="mb-3">
                                 <label for="username" class="col-form-label sing-up-font">{{ __('message.username') }}</label>
                                 <span class="text-danger">*</span>
                                 <input type="text" class="form-control signup-form" id="username"
                                     name="username">
                             </div>
                         </div>
                     </div>

                     <div class="row">
                         <div class="col-lg-6">
                             <div class="mb-3">
                                 <label for="email" class="col-form-label sing-up-font">{{ __('message.email') }}</label>
                                 <span class="text-danger">*</span>
                                 <input type="text" class="form-control signup-form" name="email">
                             </div>
                         </div>
                         <div class="col-lg-6">
                             <div class="mb-3">
                                 <label for="number" class="col-form-label sing-up-font">{{ __('message.contact_number') }}</label>
                                 <span class="text-danger">*</span>
                                 <div>
                                     <input type="text" class="form-control contact-number" id="phone"
                                         name="contact_number" style="padding-bottom: 15px">
                                 </div>
                             </div>
                         </div>
                     </div>
                     <div class="mb-1">
                         <label for="input-password" class="col-form-label sing-up-font">{{ __('message.password') }}</label>
                         <span class="text-danger">*</span>
                         <div class="password-container">
                             <input type="password" class="form-control signup-form input-password password"
                                 id="exampleInputPassword1" name="password">
                             <i class="toggle-password fas fa-eye-slash togglePassword"></i>
                         </div>
                     </div>
                     <div class="mb-4 mt-3 form-check signup-check ms-2">
                         <input type="checkbox" class="form-check-input signup-check-input" id="exampleCheck1"
                             name="checkbox">
                         <label class="form-check-label11 ms-2" for="exampleCheck1">{{ __('message.i_agree_the') }} <a
                                 href="{{ route('termofservice') }}"
                                 class="text-decoration-none fw-bold sign-up-purle">{{ __('message.terms_of_service') }}</a> & <a
                                 href="{{ route('privacypolicy') }}"
                                 class="text-decoration-none fw-bold sign-up-purle">{{ __('message.privacy_policy') }}</a>
                         </label>
                     </div>
                     <button type="submit" class="btn w-100 text-white mt-2 mb-2 fw-bold sing-up-btn">{{ __('message.sign_up') }}</button>
                 </form>
                 <div class="text-center mt-3 mb-2">
                     <p class="form-check-label ms-2 sing-up-font">{{ __('message.already_have_an_account') }} <a id="signin-link"
                             href="#" class="text-decoration-none fw-bold sign-up-purle">{{ __('message.sign_in') }}</a></p>
                 </div>
             </div>
         </div>
     </div>
 </div>
 <!-- END SIGN UP -->

 <!-- START FORGOT PASSWORD MODAL -->
 <div class="modal fade " id="forgotmodal" tabindex="-1" aria-labelledby="forgotModalLabel" aria-hidden="true"
     data-bs-backdrop="static">
     <div class="modal-dialog modal-dialog-centered modal-lg">
         <div class="modal-content forgot-modalcontent">
             <div class="modal-header border-bottom-0 forgot-modalhead">
                 <div>
                     <h5 class="modal-title mb-0 fw-bold main-page-forgot" id="forgotModalLabel">{{ __('message.forgot_password') }}</h5>
                     <p class="mt-2 mb-0 main-page-forgot-p">{{ __('message.recover_your_password') }}</p>
                 </div>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body forgot-modalbody">
                <form method="POST" action="{{ route('password.email') }}">
                    {{csrf_field()}}
                     <div class="mb-3">
                         <label for="email" class="col-form-label main-page-forgot-label">{{ __('message.email') }}</label>
                         <input type="email" name="email" class="form-control forgot-form">
                     </div>
                     <button type="submit" class="btn w-100 text-white mt-2 mb-3 fw-bold main-page-forgot-btn">{{ __('message.submit') }}</button>
                 </form>

             </div>
         </div>
     </div>
 </div>
 <!-- END FORGOT PASSWORD MODAL -->

 <!-- END MODAL -->
