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
                }, {
                    name: { required: "{{__('message.please_enter_name')}}." },
                    country_id: { required: "{{__('message.please_select_country')}}" },
                });
            });
        </script>
    @endsection
</x-master-layout>
