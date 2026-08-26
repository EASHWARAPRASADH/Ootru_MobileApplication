<div id="loading">
    @include('frontand-partials._body_loader')
</div>
@include('frontand-partials._body_header')

{{--
<head>
    <style>
        :root {
            --site-color: {{ $themeColor }};
        }
    </style>
</head>
--}}
<div id="remoteModelData" class="modal fade" role="dialog"></div>
<div class="main-page">
    {{ $slot }}
</div>

@include('frontand-partials._body_footer')

@include('frontand-partials._scripts')
