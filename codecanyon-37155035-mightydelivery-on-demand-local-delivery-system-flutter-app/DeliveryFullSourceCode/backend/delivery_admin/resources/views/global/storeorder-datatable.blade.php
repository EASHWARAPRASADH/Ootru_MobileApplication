<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{ $pageTitle }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        {!! html()->form('POST', route('store-order-list'))->id('filter-property-form')->open() !!}
            <div class="modal-body">
                <div class="row p-2" id="clear-filter-list-data">
                    <div class="form-group col-md-6">
                        {!! html()->label(__('message.select_name', ['select' => __('message.country')]))->class('form-control-label')->for('country_id') !!}
                        {!! html()->select('country_id', $country, $selectedCountryId)
                            ->class('select2Clear form-group category')
                            ->attribute('data-placeholder', __('message.select_name', ['select' => __('message.country')]))
                            ->attribute('data-ajax--url', route('ajax-list', ['type' => 'country-list'])) !!}
                    </div>
                    
                    <div class="form-group col-md-6">
                        {!! html()->label(__('message.select_name', ['select' => __('message.city')]))->class('form-control-label')->for('city_id') !!}
                        {!! html()->select('city_id', $cities, $selectedCityId)
                            ->class('select2Clear form-group city')
                            ->attribute('data-placeholder', __('message.select_name', ['select' => __('message.city')]))
                            ->attribute('data-ajax--url', route('ajax-list', ['type' => 'city-list'])) !!}
                    </div>
                    
                    <div class="form-group col-md-4">
                        {!! html()->label(__('message.status') . ' <span class="text-danger">*</span>')->class('form-control-label')->for('status') !!}
                        {!! html()->select('status', [
                            '' => __('message.all'), 
                            'draft' => __('message.draft'), 
                            'completed' => __('message.delivered'),
                            'courier_departed' => __('message.departed'),
                            'cancelled' => __('message.cancelled'),
                            'courier_assigned' => __('message.assigned'),
                            'active' => __('message.accepted'),
                            'courier_arrived' => __('message.arrived'),
                            'courier_picked_up' => __('message.picked_up'),
                            'create' => __('message.created')
                        ], $params['status'] ?? old($params['status']))->class('form-control select2Clear') !!}
                    </div>
                    
                    <div class="form-group col-md-4">
                        {!! html()->label(__('message.from_date') . ' <span class="text-danger">*</span>')->class('form-control-label')->for('from_date') !!}
                        {!! html()->date('from_date', $params['from_date'] ?? old('from_date'))->class('form-control min-datepicker select2Clear')->placeholder(__('message.date')) !!}
                    </div>
                    
                    <div class="form-group col-md-4">
                        {!! html()->label(__('message.to') . ' <span class="text-danger">*</span>')->class('form-control-label')->for('to_date') !!}
                        {!! html()->date('to_date', $params['to_date'] ?? old('to_date'))->class('form-control min-datepicker select2Clear')->placeholder(__('message.date')) !!}
                    </div>
                    


                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-md btn-danger float-right mr-1 clearListPropertynumber text-dark">{{ __('message.reset_filter') }}</button>
                {!! html()->submit(__('message.apply_filter'))->id('apply-order-filter')->class('btn btn-md btn-primary float-right') !!}
            </div>
        {!! html()->form()->close() !!}
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.clearListPropertynumber').click(function() {

            $('#clear-filter-list-data').find('select.select2Clear').val(null).trigger('change');
        });

        $('#apply-order-filter').click(function() {
            $('#filter-property-form').submit(function() {
                $(this).find(':input').filter(function() {
                    return $.trim(this.value) === '';
                }).prop('disabled', true);

                return true;
            });
        });
        if($('.select2Clear').length > 0){
            $(document).find('.select2Clear').select2({
                width: '100%',
                allowClear: true
            });
        }
    });
</script>
