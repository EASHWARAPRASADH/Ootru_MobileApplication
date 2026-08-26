<x-master-layout :assets="$assets ?? []">
    <div>
        <?php $id = $id ?? null;?>
        @if(isset($id))
            {{ html()->modelForm($data, 'PATCH', route('extracharge.update', $id))->id('extracharge_form')->open() }}
        @else
            {{ html()->form('POST', route('extracharge.store'))->attribute('enctype', 'multipart/form-data')->id('extracharge_form')->open() }} 
        @endif
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{ $pageTitle }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="new-user-info">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    {{ html()->label(__('message.country').' <span class="text-danger">*</span>')->class('form-control-label') }}
                                    {{ html()->select('country_id', isset($data) ? [$data->country->id =>  $data->country->name ] : [], old('country_id'))
                                        ->class('select2js form-group country_id')
                                        ->attribute('data-placeholder', __('message.country'))
                                        ->attribute('data-ajax--url', route('ajax-list', [ 'type' => 'country-list' ])) }}
                                </div>

                                <div class="form-group col-md-4">
                                    {{ html()->label(__('message.city').' <span class="text-danger">*</span>')->class('form-control-label') }}
                                    {{ html()->select('city_id', isset($data) ? [$data->city->id =>  $data->city->name ] : [], old('city_id'))
                                        ->class('select2js form-group city_id')
                                        ->attribute('data-placeholder', __('message.city')) }}
                                </div>

                                <div class="form-group col-md-4">
                                    {{ html()->label(__('message.name').' <span class="text-danger">*</span>')->class('form-control-label') }}
                                    {{ html()->text('title', old('title'))->placeholder(__('message.name'))->class('form-control') }}
                                </div>

                                <div class="form-group col-md-4">
                                    {{ html()->label(__('message.charges').' <span class="text-danger">*</span>')->class('form-control-label') }}
                                    {{ html()->number('charges', old('charges'))
                                        ->attribute('step', 'any')
                                        ->attribute('min', '0')
                                        ->placeholder(__('message.charges'))
                                        ->class('form-control')
                                        ->id('chargesInput') }}
                                </div>

                                <div class="form-group col-md-4">
                                    {{ html()->label(__('message.charges_type').' <span class="text-danger">*</span>')->class('form-control-label') }}
                                    {{ html()->select('charges_type', ['fixed' => __('message.fixed'), 'percentage' => __('message.percentage')], old('charges_type'))
                                        ->class('form-control select2js') }}
                                </div>

                                <div class="form-group col-md-4">
                                    {{ html()->label(__('message.status').' <span class="text-danger">*</span>')->class('form-control-label') }}
                                    {{ html()->select('status', ['1' => __('message.enable'), '0' => __('message.disable')], old('status'))
                                        ->class('form-control select2js') }}
                                </div>
                            </div>
                            <hr>
                            {{ html()->submit(isset($id) ? __('message.update') : __('message.save'))->class('btn btn-md btn-primary float-right') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{ html()->form()->close() }}
    </div>
    @section('bottom_script')
    <script>
        // Listen for input changes
        document.getElementById('chargesInput').addEventListener('input', function(e) {
            let value = e.target.value.replace(/,/g, ''); // Remove any existing commas
            if (value) {
                e.target.value = Number(value).toLocaleString(); // Add commas as thousands separator
            }
        });
    
        // Optional: ensure proper value is submitted (as a number)
        document.querySelector('form').addEventListener('submit', function() {
            let chargeInput = document.getElementById('chargesInput');
            let rawValue = chargeInput.value.replace(/,/g, ''); // Strip commas for form submission
            chargeInput.value = rawValue; // Update the value without commas for submission
        });
    </script>
        <script>
            (function($) {
                "use strict";
                $(document).ready(function () {

                    $(document).on('change' , '#country_id' , function (){
                        var country_id = $(this).val();
                        $('#city_id').empty();
                        cityList(country_id);
                    });
                });

                function cityList(country_id) {
                    var section_class_route = "{{ route('ajax-list', ['type' => 'extra_charge_city', 'country_id' => '']) }}" + country_id;
                    section_class_route = section_class_route.replace('amp;','');

                    $.ajax({
                        url: section_class_route,
                        success: function(result){
                            $('#city_id').select2({
                                width: '100%',
                                placeholder: "{{ __('message.select_name',['select' => __('message.city')]) }}",
                                data: result.results
                            });
                            if(state != null){
                                $("#city_id").val(state).trigger('change');
                            }
                        }
                    });
                }
                formValidation("#extracharge_form", {
                    country_id: { required: true },
                    city_id: { required: true },
                    title: { required: true },
                    charges: { required: true },
                    charges_type: { required: true },
                    status: { required: true },
                }, {
                    country_id: { required: "{{__('message.please_select_country')}}"},
                    city_id: { required: "{{__('message.please_select_city')}}"},
                    title: { required: "{{__('message.please_enter_name')}}" },
                    charges: { required: "{{__('message.please_enter_charges')}}" },
                    charges_type: { required: "{{__('message.please_enter_charges_type')}}" },
                    status: { required: "{{__('message.please_select_status')}}" },
                });
            })(jQuery);
        </script>
      
    @endsection
</x-master-layout>
