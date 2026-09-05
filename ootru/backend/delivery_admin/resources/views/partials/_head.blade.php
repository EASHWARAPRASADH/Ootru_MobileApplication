<link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}?v=2">
<link rel="alternate icon" type="image/png" href="{{ asset('images/favicon.ico') }}?v=2">
<link rel="shortcut icon" class="site_favicon_preview" href="{{ asset('images/favicon.ico') }}?v=2" />
<link rel="stylesheet" href="{{ asset('css/backend-bundle.min.css') }}"/>
<link rel="stylesheet" href="{{ asset('css/backend.css') }}"/>
@if(mighty_language_direction() == 'rtl')
    <link rel="stylesheet" href="{{ asset('css/rtl.css') }}">
@endif
<link rel="stylesheet" href="{{ asset('vendor/@fortawesome/fontawesome-free/css/all.min.css') }}"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
<link rel="stylesheet" href="{{ asset('vendor/remixicon/fonts/remixicon.css') }}"/>
{{-- Select2 CSS is already included in backend-bundle.min.css --}}
<link rel="stylesheet" href="{{ asset('vendor/confirmJS/jquery-confirm.min.css') }}"/>
<link rel="stylesheet" href="{{ asset('vendor/magnific-popup/css/magnific-popup.css') }}"/>
<link rel="stylesheet" href="{{ asset('css/custom.css')}}">
@if(isset($assets) && in_array('phone', $assets))
    <link rel="stylesheet" href="{{ asset('vendor/intlTelInput/css/intlTelInput.css') }}">
@endif
<link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">
