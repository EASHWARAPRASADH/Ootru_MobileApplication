@php
    $app_settings = App\Models\AppSetting::first();
    $pages = App\Models\Pages::where('status', '1')->get();
@endphp

<!-- START  FOOTER SECTION -->
<footer class="footer mt-auto">
    <section class="py-4 mt-4 border-top border-light footer">
        <div class="container overflow-hidden">
            <div class="row gy-4 gy-lg-0 justify-content-xl-between">
                <div class="col-12 col-md-6 col-lg-5 col-xl-5">
                    <div>
                        @if(getSingleMediaSettingImage(getSettingFirstData('app_content','app_logo_image'),'app_logo_image'))
                            <img src="{{ getSingleMediaSettingImage(getSettingFirstData('app_content', 'app_logo_image'), 'app_logo_image') }}" height="50" width="50" class="me-2">
                        @endif
                        <span class="font-weight-500 main-text fs-custom-26">{{ SettingData('app_content', 'app_name') }}</span>
                        <p class="mb-3 mt-3 gray-text fs-custom-18">
                            {{ SettingData('download_app', 'download_footer_content') ?? '' }}
                        </p>
                        <a class="text-decoration-none" href="{{ SettingData('app_content', 'play_store_link') ?? 'javascript:void(0)' }}"
                            {{ SettingData('app_content', 'play_store_link') != null ? 'target="_blank"' : '' }}>
                            <img src="{{ asset('frontend-website/assets/website/ic_play_store.png') }}" alt="play_store" class="me-2 mb-2" style="width: 154px;">
                        </a>
                        <a href="{{ SettingData('app_content', 'app_store_link') ?? 'javascript:void(0)' }}"
                            {{ SettingData('app_content', 'app_store_link') != null ? 'target="_blank"' : '' }}>
                            <img src="{{ asset('frontend-website/assets/website/ic_app_store.png') }}" alt="app_store" class="mb-2" style="width: 154px;">
                        </a>
                        <h5 class="mt-4 main-text fs-custom-22">{{ __('message.experience') }} {{ SettingData('app_content', 'app_name') }} {{ __('message.app_on_mobile') }}</h5>

                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3 col-xl-2">
                    <div>
                        <h5 class="mb-4 main-text fs-custom-22">{{ SettingData('app_content', 'app_name') }}</h5>
                        <p class="mb-3 footer-p">
                            <a href="{{ route('frontend-section') }}" class="gray-text text-decoration-none">{{ __('message.home') }}</a>
                        </p>
                        <p class="mb-3 footer-p">
                            <a href="{{ route('deliverypartner') }}" class="gray-text text-decoration-none">{{ __('message.delivery_boy') }}</a>
                        </p>
                        <p class="mb-3 footer-p">
                            <a href="{{ route('about-us') }}" class="gray-text text-decoration-none">{{ __('message.about_us') }}</a>
                        </p>
                        <p class="mb-3 footer-p">
                            <a href="{{ route('contactus') }}" class="gray-text text-decoration-none">{{ __('message.contact_us') }}</a>
                        </p>
                    </div>
                    @if (count($pages) > 0)
                        <ul class="list-unstyled footer-p">
                            @foreach ($pages as $page)
                                <li class="mb-3">
                                    <a href="{{ isset($page->slug) && $page->slug != null ? route('pages', ['slug' => $page->slug]) : 'javascript:void(0)' }}"
                                        class="footer-pages-content gray-text text-decoration-none">
                                        {{ ucwords($page->title) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                    <div>
                        <h5 class="mb-4 main-text fs-custom-22">{{ __('message.contact_us') }}</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <a href="{{ $app_settings->site_email ? 'mailto:' . $app_settings->site_email : 'javascript:void(0)' }}" {{ $app_settings->site_email ? 'target="_blank"' : '' }} class="gray-text text-decoration-none footer-p">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2 12C2 8.22876 2 6.34315 3.17157 5.17157C4.34315 4 6.22876 4 10 4H14C17.7712 4 19.6569 4 20.8284 5.17157C22 6.34315 22 8.22876 22 12C22 15.7712 22 17.6569 20.8284 18.8284C19.6569 20 17.7712 20 14 20H10C6.22876 20 4.34315 20 3.17157 18.8284C2 17.6569 2 15.7712 2 12Z" stroke="#848484" stroke-width="1.5"/>
                                        <path d="M6 8L8.1589 9.79908C9.99553 11.3296 10.9139 12.0949 12 12.0949C13.0861 12.0949 14.0045 11.3296 15.8411 9.79908L18 8" stroke="#848484" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                    {{ $app_settings->site_email ?? '' }}
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="{{ $app_settings->support_number ? 'tel:' . $app_settings->support_number : 'javascript:void(0)' }}" {{ $app_settings->support_number ? 'target="_blank"' : '' }} class="gray-text text-decoration-none footer-p">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.0376 5.31617L10.6866 6.4791C11.2723 7.52858 11.0372 8.90533 10.1147 9.8278C10.1147 9.8278 8.99578 10.9467 11.0245 12.9755C13.0532 15.0042 14.1722 13.8853 14.1722 13.8853C15.0947 12.9628 16.4714 12.7277 17.5209 13.3134L18.6838 13.9624C20.2686 14.8468 20.4557 17.0692 19.0628 18.4622C18.2258 19.2992 17.2004 19.9505 16.0669 19.9934C14.1588 20.0658 10.9183 19.5829 7.6677 16.3323C4.41713 13.0817 3.93421 9.84122 4.00655 7.93309C4.04952 6.7996 4.7008 5.77423 5.53781 4.93723C6.93076 3.54428 9.15317 3.73144 10.0376 5.31617Z" stroke="#848484" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                    {{ $app_settings->support_number ?? '' }}
                                </a>
                            </li>
                            <li class="mb-2">
                                <a class="gray-text text-decoration-none footer-p d-flex gap-1" href="{{ $app_settings->site_description ? 'https://www.google.com/maps/search/?api=1&query=' . urlencode($app_settings->site_description) : 'javascript:void(0)' }}" {{ $app_settings->site_description ? 'target="_blank"' : '' }}>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4 9.64329C4 5.14588 7.58172 1.5 12 1.5C16.4183 1.5 20 5.14588 20 9.64329C20 14.1055 13.4629 21.1744 13.4629 21.1744L12 22.5L10.5371 21.1744C10.5371 21.1744 4 14.1055 4 9.64329Z" stroke="#848484" stroke-width="1.5"/>
                                        <circle cx="12" cy="9.5" r="3" stroke="#848484" stroke-width="1.5"/>
                                    </svg>
                                    <p>{{ $app_settings->site_description ?? '' }}</p>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-5 col-xl-2">
                    <div>
                        <h5 class="mb-4 main-text fs-custom-22">{{ __('message.help') }}</h5>
                        <p class="mb-3 footer-p">
                            <a href="{{ route('privacypolicy') }}" class="gray-text text-decoration-none">{{ __('message.privacy_policy') }}</a>
                        </p>
                        <p class="mb-3 footer-p">
                            <a href="{{ route('termofservice') }}" class="gray-text text-decoration-none">{{ __('message.terms_and_conditions') }}</a>
                        </p>
                    </div>
                </div>
                <div class="mt-4">
                    <hr>
                </div>

                <div class="d-flex justify-content-between">
                    <p>{{ $app_settings->site_copyright ?? '' }}</p>
                    <div>
                        <a class="gray-text text-decoration-none footer-p"
                            href="{{ $app_settings->facebook_url ?? 'javascript:void(0)' }}"
                            {{ $app_settings->facebook_url != null ? 'target="_blank"' : '' }}>
                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M23.1002 5.16016H6.89874C6.3128 5.16016 5.75086 5.38234 5.33654 5.77783C4.92222 6.17332 4.68945 6.70972 4.68945 7.26902V22.734C4.68945 23.2933 4.92222 23.8297 5.33654 24.2252C5.75086 24.6207 6.3128 24.8429 6.89874 24.8429H13.2159V18.1512H10.3162V15.0015H13.2159V12.6009C13.2159 9.87039 14.9189 8.36212 17.5272 8.36212C18.7764 8.36212 20.0827 8.57476 20.0827 8.57476V11.2548H18.6434C17.2253 11.2548 16.783 12.0948 16.783 12.9564V15.0015H19.9487L19.4424 18.1512H16.783V24.8429H23.1002C23.6861 24.8429 24.248 24.6207 24.6624 24.2252C25.0767 23.8297 25.3095 23.2933 25.3095 22.734V7.26902C25.3095 6.70972 25.0767 6.17332 24.6624 5.77783C24.248 5.38234 23.6861 5.16016 23.1002 5.16016Z" fill="#848484"/>
                            </svg>
                        </a>
                        <a class="gray-text text-decoration-none footer-p"
                            href="{{ $app_settings->instagram_url ?? 'javascript:void(0)' }}"
                            {{ $app_settings->instagram_url != null ? 'target="_blank"' : '' }}>
                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M25.1361 22.3692C24.6837 23.4626 23.8078 24.3049 22.6651 24.7456C20.954 25.3983 16.8938 25.2477 15.0029 25.2477C13.112 25.2477 9.04596 25.3927 7.34066 24.7456C6.20379 24.3105 5.32794 23.4681 4.86972 22.3692C4.19108 20.7235 4.34769 16.8186 4.34769 15C4.34769 13.1814 4.19688 9.27089 4.86972 7.63082C5.32214 6.53743 6.19799 5.69508 7.34066 5.25438C9.05176 4.6017 13.112 4.75232 15.0029 4.75232C16.8938 4.75232 20.9598 4.60727 22.6651 5.25438C23.802 5.6895 24.6779 6.53185 25.1361 7.63082C25.8147 9.27647 25.6581 13.1814 25.6581 15C25.6581 16.8186 25.8147 20.7291 25.1361 22.3692ZM8.33831 15C8.33831 11.4521 11.3139 8.59032 15.0029 8.59032C18.6919 8.59032 21.6675 11.4521 21.6675 15C21.6675 18.5479 18.6919 21.4097 15.0029 21.4097C11.3139 21.4097 8.33831 18.5479 8.33831 15ZM21.9401 9.82316C22.7985 9.82316 23.4946 9.15932 23.4946 8.32813C23.4946 7.50251 22.7985 6.83309 21.9401 6.83309C21.0817 6.83309 20.3856 7.50251 20.3856 8.32813C20.3856 9.15374 21.0759 9.82316 21.9401 9.82316Z" fill="#848484"/>
                                <path d="M15.0029 19.1671C12.619 19.1671 10.67 17.2983 10.67 15C10.67 12.7017 12.6132 10.8329 15.0029 10.8329C17.3926 10.8329 19.3357 12.7017 19.3357 15C19.3357 17.2983 17.3868 19.1671 15.0029 19.1671Z" fill="#848484"/>
                            </svg>
                        </a>
                        <a class="gray-text text-decoration-none footer-p"
                            href="{{ $app_settings->twitter_url ?? 'javascript:void(0)' }}"
                            {{ $app_settings->twitter_url != null ? 'target="_blank"' : '' }}>
                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11.1818 5.45312H4.5L12.3854 15.967L4.9295 24.5439H7.45907L13.557 17.5291L18.8182 24.544H25.5L17.283 13.588L24.3546 5.45312H21.8251L16.1114 12.0259L11.1818 5.45312ZM19.7727 22.6349L8.31818 7.36222H10.2273L21.6818 22.6349H19.7727Z" fill="#848484"/>
                            </svg>
                        </a>
                        <a class="gray-text text-decoration-none footer-p"
                            href="{{ $app_settings->linkedin_url ?? 'javascript:void(0)' }}"
                            {{ $app_settings->linkedin_url != null ? 'target="_blank"' : '' }}>
                            <i class="fa-brands fa-linkedin-in fa-xl me-4"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>
</footer>
<!-- END FOOTER SECTION -->
