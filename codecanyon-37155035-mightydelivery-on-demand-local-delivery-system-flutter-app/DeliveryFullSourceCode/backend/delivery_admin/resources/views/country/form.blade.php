<x-master-layout :assets="$assets ?? []">
    <div>
        <?php $id = $id ?? null; ?>
        @if(isset($id))
            {!! html()->modelForm($data, 'PATCH', route('country.update', $id))->id('country_form')->open() !!}
        @else
            {!! html()->form('POST', route('country.store'))->id('country_form')->open() !!}
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
                                <div class="form-group col-md-6">
                                    {!! html()->label(__('message.country'). ' <span class="text-danger">*</span>')->for('name')->class('form-control-label') !!}
                                    {!! html()->select('name', collect($country)->pluck('countryNameEn', 'countryNameEn'))->class('form-control select2js')->required() !!}
                                </div>

                                <div class="form-group col-md-6">
                                    {!! html()->label(__('message.distance_type'))->for('distance_type')->class('form-control-label') !!}
                                    {!! html()->select('distance_type', ['km' => __('message.km'), 'miles' => __('message.miles')], old('distance_type'))->class('form-control select2js')->required() !!}
                                </div>

                                <div class="form-group col-md-6">
                                    {!! html()->label(__('message.weight_type'))->for('weight_type')->class('form-control-label') !!}
                                    {!! html()->select('weight_type', ['kg' => __('message.kg'), 'pound' => __('message.pound')], old('weight_type'))->class('form-control select2js')->required() !!}
                                </div>

                                <div class="form-group col-md-6">
                                    {!! html()->label(__('message.status'))->for('status')->class('form-control-label') !!}
                                    {!! html()->select('status', ['1' => __('message.enable'), '0' => __('message.disable')], old('status'))->class('form-control select2js')->required() !!}
                                </div>
                            </div>
                            <hr>
                            {!! html()->submit($id ? __('message.update') : __('message.save'))->class('btn btn-md btn-primary float-right') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! html()->form()->close() !!}
    </div>
</x-master-layout>
