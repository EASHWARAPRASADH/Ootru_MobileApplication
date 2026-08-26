
{!! html()->form('GET', route('reference-list'))->open() !!}
<div class="row p-2">
    <div class="form-group col-md-2">
        {{ html()->label(__('message.select_name', ['select' => __('message.country')]), 'country_id')->class('form-control-label') }}
        {{ html()->select('country_id', $country, $selectedCountryId)
        ->class('select2js form-group country')
        ->attribute('data-placeholder', __('message.select_name', ['select' => __('message.country')]))
        ->attribute('data-ajax--url', route('ajax-list', ['type' => 'country-list'])) }}
    </div>
    <div class="form-group col-md-2">
    {{ html()->label(__('message.select_name', ['select' => __('message.city')]), 'city_id')->class('form-control-label') }}
    {{ html()->select('city_id', $cities, $selectedCityId)
        ->class('select2js form-group city')
        ->attribute('data-placeholder', __('message.select_name', ['select' => __('message.city')]))
        ->attribute('data-ajax--url', route('ajax-list', ['type' => 'city-list'])) }}
    </div>
    <div class="form-group col-sm-0 mt-3">
        <button class="btn btn-sm btn-warning text-white mt-3 pt-2 pb-2">{{ __('message.apply_filter') }}</button>
    </div>
    <div class="form-group col-sm-0 ml-1 mt-3">
        @if(isset($reset_file_button))
        {!! $reset_file_button !!}
        @endif
    </div>
    
</div>
{{ html()->form()->close() }}   
<script src="{{ asset('frontend-website/assets/js/jquery.min.js') }}"></script>
<script>
     $(document).ready(function () {

    $(".select2js").select2({
        width: "100%",
        tags: true
    });
});
</script>   
