<x-master-layout :assets="$assets ?? []">
    <div>
        <?php $id = $id ?? null;?>
        @if(isset($id))
            {{ html()->modelForm($data, 'PATCH', route('coupon.update', $id))->id('coupon_form')->open() }}
        @else
            {{ html()->form('POST', route('coupon.store'))->id('coupon_form')->open() }} 
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
                                    {!! html()->label(__('message.start_date').' <span class="text-danger">*</span>')->class('form-control-label') !!}
                                    {!! html()->date('start_date', old('start_date'))
                                        ->placeholder(__('message.start_date'))
                                        ->class('form-control min-datepicker') !!}
                                </div>
                                <div class="form-group col-md-4">
                                    {!! html()->label(__('message.end_date').' <span class="text-danger">*</span>')->class('form-control-label') !!}
                                    {!! html()->date('end_date', old('end_date'))
                                        ->placeholder(__('message.end_date'))
                                        ->class('form-control min-datepicker') !!}
                                </div>
                                <div class="form-group col-md-4">
                                    {!! html()->label(__('message.value_type'))->class('form-control-label') !!}
                                    {!! html()->select('value_type', [
                                        'fixed' => __('message.fixed'),
                                        'percentage' => __('message.percentage')
                                    ], old('value_type'))
                                        ->class('form-control select2js') !!}
                                </div>

                                <div class="form-group col-md-4">
                                    {!! html()->label(__('message.discount_amount').' <span class="text-danger">*</span>')->class('form-control-label') !!}
                                    {!! html()->number('discount_amount', old('discount_amount'))
                                        ->attribute('step', 'any')
                                        ->attribute('min', 0)
                                        ->placeholder(__('message.discount_amount'))
                                        ->class('form-control') !!}
                                </div>

                                <div class="form-group col-md-4">
                                    {!! html()->label(__('message.city_type'))->class('form-control-label') !!}
                                    {!! html()->select('city_type', [
                                        'city_wise' => __('message.city_wise'),
                                        'all' => __('message.all')
                                    ], old('type'))
                                        ->class('form-control select2js')
                                        ->required() !!}
                                </div>

                                <div class="form-group col-md-4" id="cityField">
                                {{ html()->label(__('message.city').' <span class="text-danger">*</span>')->class('form-control-label') }}
                                {{ html()->select('city_id[]', $selected_cities ?? [], old('city_id[]', $selected_cities ?? []))
                                    ->class('select2js form-group city_id')
                                    ->multiple('multiple')
                                    ->attribute('required', 'required')
                                    ->attribute('data-placeholder', __('message.select_name', ['select' => __('message.city')]))
                                    ->attribute('data-ajax--url', route('ajax-list', ['type' => 'city-list'])) 
                                }}
                            </div> 
                                <div class="form-group col-md-4">
                                    {!! html()->label(__('message.status'))->class('form-control-label') !!}
                                    {!! html()->select('status', [
                                        '1' => __('message.enable'),
                                        '0' => __('message.disable')
                                    ], old('status'))
                                        ->class('form-control select2js')
                                        ->required() !!}
                                </div>
                            </div>
                            <hr>
                            {!! html()->submit(isset($id) ? __('message.update') : __('message.save'))->class('btn btn-md btn-primary float-right') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{ html()->form()->close() }}
    </div>
    @section('bottom_script')
    <script>
         $(document).ready(function() {
            function toggleCityField() {
                var typeValue = $('select[name="city_type"]').val();
                if (typeValue === 'all') {
                    $('#cityField').hide();
                    $('select[name="city_id[]"]').empty();
                } else {
                    $('#cityField').show();
                }
            }
   
                toggleCityField();

            $('select[name="city_type"]').change(function() {
                toggleCityField();
            });

            $.validator.addMethod("greaterThanEqual", function(value, element, param) {
                var startDate = $(param).val();
                return this.optional(element) || new Date(value) >= new Date(startDate);
            }, "{{ __('message.end_date_must_be_greater_than_start_date') }}");

            formValidation("#coupon_form", {
                start_date: { required: true },
                end_date: {
                    required: true,
                    greaterThanEqual: "#start_date" 
                },
                discount_amount: { required: true },
                'city_id[]': { required: true }
            }, {
                start_date: { required: "{{__('message.please_select_start_date')}}" },
                end_date: {
                    required: "{{__('message.please_select_end_date')}}",
                    greaterThanEqual: "{{__('message.end_date_must_be_after_start_date')}}" // Use the custom error message
                },
                discount_amount: { required: "{{__('message.please_enter_discount_amount')}}" },
                'city_id[]': { required: "{{__('message.please_select_city')}}" }
            });
        });
    </script>
    @endsection
</x-master-layout>
