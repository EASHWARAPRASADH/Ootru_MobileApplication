<link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}?v=2">
<link rel="alternate icon" type="image/png" href="{{ asset('images/favicon.ico') }}?v=2">
<link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}?v=2">
<link rel="stylesheet" href="{{ asset('frontend-website/assets/css/style.css') }}">
<link rel="stylesheet" href="{{ asset('frontend-website/assets/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('frontend-website/assets/css/toastr.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
<link rel="stylesheet" href="{{ asset('frontend-website/assets/css/owl.carousel.css') }}">
<link rel="stylesheet" href="{{ asset('frontend-website/assets/css/owl.carousel.min.css') }}">

<link rel="stylesheet" href="{{ asset('vendor/intlTelInput/css/intlTelInput.css') }}">
@if(mighty_language_direction() == 'rtl')
    <link rel="stylesheet" href="{{ asset('css/rtl.css') }}">
@endif 

<style>
    :root {
        --site-color: {{ $themeColor }};
    }
</style>