<x-master-layout :assets="$assets ?? []">
    <div>
        <?php $id = $id ?? null;?>
        @if(isset($id))
            {{ html()->modelForm($data, 'PATCH', route('city.update', $id))->attribute('id', 'city_form')->open() }}
        @else
            {{ html()->form('POST', route('city.store'))->attribute('id', 'city_form')->open() }} 
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
                                    {{ html()->label(__('message.name') . ' <span class="text-danger">*</span>', 'name')->class('form-control-label') }}
                                    {{ html()->text('name', old('name'))->class('form-control')->placeholder(__('message.name')) }}
                                </div>

                                <div class="form-group col-md-4">
                                    {{ html()->label(__('message.country') . ' <span class="text-danger">*</span>', 'country_id')->class('form-control-label') }}
                                    {{ html()->select('country_id', isset($data) ? [optional($data->country)->id => optional($data->country)->name] : [], old('country_id'))
                                        ->class('select2js form-group country_id')
                                        ->attribute('data-placeholder', __('message.country'))
                                        ->attribute('data-ajax--url', route('ajax-list', ['type' => 'country-list'])) }}
                                </div>

                                <div class="form-group col-md-4">
                                    {{ html()->label(__('message.fixed_charges') . ' <span class="text-danger">*</span>', 'fixed_charges')->class('form-control-label') }}
                                    {{ html()->number('fixed_charges', old('fixed_charges'))->class('form-control')->attribute('step', 'any')->attribute('min', 0)->placeholder(__('message.fixed_charges')) }}
                                </div>

                                <div class="form-group col-md-4">
                                    {{ html()->label(__('message.cancel_charges') . ' <span class="text-danger">*</span>', 'cancel_charges')->class('form-control-label') }}
                                    {{ html()->number('cancel_charges', old('cancel_charges'))->class('form-control')->attribute('step', 'any')->attribute('min', 0)->placeholder(__('message.cancel_charges')) }}
                                </div>

                                <div class="form-group col-md-4">
                                    {{ html()->label(__('message.min_distance') . ' <span class="text-danger">*</span>', 'min_distance')->class('form-control-label') }}
                                    {{ html()->number('min_distance', old('min_distance'))->class('form-control')->attribute('step', 'any')->attribute('min', 0)->placeholder(__('message.min_distance')) }}
                                </div>

                                <div class="form-group col-md-4">
                                    {{ html()->label(__('message.min_weight') . ' <span class="text-danger">*</span>', 'min_weight')->class('form-control-label') }}
                                    {{ html()->number('min_weight', old('min_weight'))->class('form-control')->attribute('step', 'any')->attribute('min', 0)->placeholder(__('message.min_weight')) }}
                                </div>

                                <div class="form-group col-md-4">
                                    {{ html()->label(__('message.per_distance_charges') . ' <span class="text-danger">*</span>', 'per_distance_charges')->class('form-control-label') }}
                                    {{ html()->number('per_distance_charges', old('per_distance_charges'))->class('form-control')->attribute('step', 'any')->attribute('min', 0)->placeholder(__('message.per_distance_charges')) }}
                                </div>

                                <div class="form-group col-md-4">
                                    {{ html()->label(__('message.per_weight_charges') . ' <span class="text-danger">*</span>', 'per_weight_charges')->class('form-control-label') }}
                                    {{ html()->number('per_weight_charges', old('per_weight_charges'))->class('form-control')->attribute('step', 'any')->attribute('min', 0)->placeholder(__('message.per_weight_charges')) }}
                                </div>

                                <div class="form-group col-md-4">
                                    {{ html()->label(__('message.commission_type'), 'commission_type')->class('form-control-label') }}
                                    {{ html()->select('commission_type', ['fixed' => __('message.fixed'), 'percentage' => __('message.percentage')], old('commission_type'))
                                        ->class('form-control select2js') }}
                                </div>

                                <div class="form-group col-md-4">
                                    {{ html()->label(__('message.admin_commission') . ' <span class="text-danger">*</span>', 'admin_commission')->class('form-control-label') }}
                                    {{ html()->number('admin_commission', old('admin_commission'))->class('form-control')->attribute('step', 'any')->attribute('min', 0)->placeholder(__('message.admin_commission')) }}
                                </div>

                                <div class="form-group col-md-4">
                                    {{ html()->label(__('message.status'), 'status')->class('form-control-label') }}
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
            $(document).ready(function(){
                formValidation("#city_form", {
                    name: { required: true },
                    country_id: { required: true },
                    fixed_charges: { required: true },
                    cancel_charges: { required: true },
                    min_distance: { required: true },
                    min_weight: { required: true },
                    per_distance_charges: { required: true },
                    per_weight_charges: { required: true },
                    admin_commission: { required: true },
                }, {
                    name: { required: "{{__('message.please_enter_name')}}." },
                    country_id: { required: "{{__('message.please_select_country')}}" },
                    fixed_charges: { required: "{{__('message.please_enter_fixed_charges')}}" },
                    cancel_charges: { required: "{{__('message.please_enter_cancel_charges')}}" },
                    min_distance: { required: "{{__('message.please_enter_min_distance')}}" },
                    min_weight: { required: "{{__('message.please_enter_min_weight')}}" },
                    per_distance_charges: { required: "{{__('message.please_enter_per_distance_charges')}}" },
                    per_weight_charges: { required: "{{__('message.please_enter_per_weight_charges')}}" },
                    admin_commission: { required: "{{__('message.please_enter_admin_commission')}}" },
                });
            });
        </script>
    @endsection
</x-master-layout>
