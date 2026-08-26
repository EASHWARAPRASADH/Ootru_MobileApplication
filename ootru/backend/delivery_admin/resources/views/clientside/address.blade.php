<x-master-layout :assets="$assets ?? []">
    <div>
        <?php $id = $id ?? null;?>
            {!! html()->form('POST', route('useraddress.store'))->id('address_form')->open() !!}
            {!! html()->hidden('user_id', $userdata->id) !!}
            {!! html()->hidden('country_id', $userdata->country_id) !!}
            {!! html()->hidden('city_id', $userdata->city_id) !!}
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <div class="header-title">
                                <h5 class="font-weight-bold">{{ $pageTitle  }}</h5>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="new-user-info">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        {!! html()->label(__('message.address') . ' <span class="text-danger">*</span>')->for('address')->class('form-control-label')!!}
                                        {!! html()->text('address', old('address'))->class('form-control')->id('location')->required() !!}
                                    </div>
                                
                                    {!! html()->hidden('latitude')->id('pickup_lat') !!}
                                    {!! html()->hidden('longitude')->id('pickup_lng') !!}
                                
                                    <div class="form-group col-md-6">
                                        {!! html()->label(__('message.address_type') . ' <span class="text-danger">*</span>')->for('address_type')->class('form-control-label')!!}
                                        {!! html()->select('address_type', [
                                                'home' => __('message.home'),
                                                'work' => __('message.work'),
                                                'commercial' => __('message.commercial')
                                            ], old('address_type'))
                                            ->class('form-control select2js') !!}
                                    </div>
                                
                                    <div class="form-group col-md-6">
                                        {!! html()->label(__('message.contact_number') . ' <span class="text-danger">*</span>')->for('contact_number')->class('form-control-label')!!}
                                        {!! html()->text('contact_number', old('contact_number'))->placeholder(__('message.contact_number'))->class('form-control')->id('phone')->required() !!}
                                    </div>
                                </div>
                                <hr>
                                {!! html()->submit(__('message.save'))->class('btn btn-md btn-primary float-right') !!}

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        {!! html()->form()->close() !!}
        </div>
    @section('bottom_script')
        <script>
            $(document).ready(function(){
                function pickupAutocomplete() {
                    var input = document.getElementById('location');
                    var autocomplete = new google.maps.places.Autocomplete(input);

                    autocomplete.addListener('place_changed', function () {
                    var place = autocomplete.getPlace();
                    document.getElementById('pickup_lat').value = place.geometry.location.lat();
                    document.getElementById('pickup_lng').value = place.geometry.location.lng();
                });
                }

                pickupAutocomplete();

                formValidation("#address_form", {
                    address: { required: true },
                    contact_number: { required: true },
                }, {
                    address: { required: "{{__('message.please_enter_address')}}" },
                    contact_number: { required: "{{__('message.please_enter_contact_number')}}" },
                });
            });
        </script>
    @endsection
</x-master-layout>
