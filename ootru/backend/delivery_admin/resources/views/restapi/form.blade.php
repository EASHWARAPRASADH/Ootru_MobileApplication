<?php $id = $id ?? null; ?>
{!! html()->modelForm('POST' ,route('rest-api.store'))->id('restapi_form')->open() !!}

<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{$pageTitle}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 form-group">
                    {!! html()->label(__('message.name') . ' <span class="text-danger">*</span>')->for('name')->class('form-control-label')!!}
                    {!! html()->text('name', old('name'))->placeholder(__('message.name'))->class('form-control') !!}
                </div>
                <div class="col-md-12 form-group">
                    {!! html()->label(__('message.description') . ' <span class="text-danger">*</span>')->for('description')->class('form-control-label')!!}
                    {!! html()->textarea('description', old('description', $data->description ?? null))->class('form-control textarea')->placeholder(__('message.description')) !!}
                </div>
                <div class="form-group col-md-12">
                    {!! html()->label(__('message.country') . ' <span class="text-danger">*</span>')->for('country_id')->class('form-control-label') !!}
                    {!! html()->select('country_id', isset($data) ? [$data->country->id => $data->country->name] : [])
                        ->id('country_id')
                        ->class('form-control select2js country')
                        ->required()
                        ->attribute('data-placeholder', __('message.country'))
                        ->attribute('data-ajax--url', route('ajax-list', ['type' => 'country-list'])) !!}
                </div>

                <div class="form-group col-md-12">
                    {!! html()->label(__('message.city') . ' <span class="text-danger">*</span>')->for('city_id')->class('form-control-label') !!}
                    {!! html()->select('city_id', isset($data) ? [$data->city->id => $data->city->name] : [])
                        ->id('city_id')
                        ->class('select2js form-group city_id')
                        ->attribute('data-placeholder', __('message.city')) !!}
                </div>
                <div class="col-md-12 form-group">
                    {!! html()->label(__('message.key'))->for('rest_key')->class('form-control-label') !!}
                    <div class="input-group">
                        <?php $randome = $randome ?? Str::random(10); ?>
                        {!! html()->select('rest_key', ['rest_' . $randome => 'rest_' . $randome], 'rest_' . $randome)
                            ->id('rest_key')
                            ->class('form-control select2') !!}
                        <div class="input-group-append">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="copyButton">Copy</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-md btn-secondary" data-dismiss="modal">{{ __('message.close') }}</button>
            <button type="submit" class="btn btn-md btn-primary" id="btn_submit">{{ isset($id) ? __('message.update') : __('message.save') }}</button>
        </div>
    </div>
 {!! html()->form()->close() !!}
</div>

<script>
    $(document).ready(function () {
        $(".select2js").select2({
            width: "100%",
            tags: true
        });

        $(document).on('change', '#country_id', function () {
            var country_id = $(this).val();
            $('#city_id').empty(); 
            cityList(country_id); 
        });

        formValidation("#restapi_form", {
            name: { required: true },
            description: { required: true },
            'city_id': { required: true },
            'country_id': { required: true }
        }, {
            name: { required: "{{__('message.please_enter_name')}}" },
            description: { required: "{{__('message.please_enter_description')}}" },
            'city_id': { required: "{{__('message.please_select_city')}}" },
            'country_id': { required: "{{__('message.please_select_country')}}" }
        });
    });

    function cityList(country_id) {
        var section_class_route = "{{ route('ajax-list', ['type' => 'extra_charge_city', 'country_id' => '']) }}" + country_id;
        section_class_route = section_class_route.replace('amp;', '');

        $.ajax({
            url: section_class_route,
            success: function(result) {
                $('#city_id').select2({
                    width: '100%',
                    placeholder: "{{ __('message.select_name', ['select' => __('message.city')]) }}",
                    data: result.results
                });

                if (typeof state !== 'undefined' && state !== null) {
                    $("#city_id").val(state).trigger('change');
                }
            }
        });
    }

    $('#copyButton').click(function() {
    var selectedOption = $('#rest_key').val();
    navigator.clipboard.writeText(selectedOption).then(() => {
        Swal.fire({
            position: 'top-end', 
            icon: 'success', 
            title: 'Copied!', 
            showConfirmButton: false, 
            timer: 1500, 
            toast: true, 
            background: '{{ $themeColor }}', 
            color: '#fff', 
            iconColor: '#fff', 
            customClass: {
                popup: 'custom-toast' 
            }
        });
    }).catch(() => {
        Swal.fire({
            position: 'top-end',
            icon: 'error',
            title: 'Failed!',
            showConfirmButton: false,
            timer: 1500,
            toast: true,
            background: '#dc3545', // Red background for error
            color: '#fff',
            iconColor: '#fff'
        });
    });
});


</script>
