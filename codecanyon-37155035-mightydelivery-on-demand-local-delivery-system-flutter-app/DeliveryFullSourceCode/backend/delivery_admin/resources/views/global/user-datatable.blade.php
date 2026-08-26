
{!! html()->form('GET', route('users.store'))->open() !!}
<div class="row p-2">
    <div class="form-group col-md-2">
        {!! html()->label(__('message.select_name', ['select' => __('message.country')]), 'country_id')->class('form-control-label') !!}
        {!! html()->select('country_id', $country, $selectedCountryId)
            ->class('select2js form-group country')
            ->attribute('data-placeholder', __('message.select_name', ['select' => __('message.country')]))
            ->attribute('data-ajax--url', route('ajax-list', ['type' => 'country-list'])) !!}
    </div>
    
    <div class="form-group col-md-2">
        {!! html()->label(__('message.select_name', ['select' => __('message.city')]), 'city_id')->class('form-control-label') !!}
        {!! html()->select('city_id', $cities, $selectedCityId)
            ->class('select2js form-group city')
            ->attribute('data-placeholder', __('message.select_name', ['select' => __('message.city')]))
            ->attribute('data-ajax--url', route('ajax-list', ['type' => 'city-list'])) !!}
    </div>
  
    <div class="form-group col-md-2">
        {!! html()->label(__('message.status') . '<span data-toggle="tooltip" data-html="true" data-placement="top" title="Active user: Who last activated date in 1 day<br>Engaged user: Who last activated date in 2-15 days<br>Inactive user: Who last activated date in more than 15 days">(info)</span>', 'last_actived_at')->class('form-control-label') !!}
        {!! html()->select('last_actived_at', [
            '' => __('message.all'),
            'active_user' => __('message.active_user'),
            'engaged_user' => __('message.engaged_user'),
            'inactive_user' => __('message.inactive_user')
        ], $params['last_actived_at'] ?? old($params['last_actived_at']))
        ->class('form-control select2js') !!}
    </div>      

    <div class="form-group col-sm-0 mt-3">
        {!! html()->button(__('message.apply_filter'))->class('btn btn-sm btn-warning text-white mt-3 pt-2 pb-2')->type('submit') !!}
    </div>

    <div class="form-group col-sm-2 mt-3">
        @isset($reset_file_button)
            {!! $reset_file_button !!}
        @endisset
    </div>
</div>
{!! html()->form()->close() !!}
<script>
     $(document).ready(function () {

    $(".select2js").select2({
        width: "100%",
        tags: true
    });
});
</script>