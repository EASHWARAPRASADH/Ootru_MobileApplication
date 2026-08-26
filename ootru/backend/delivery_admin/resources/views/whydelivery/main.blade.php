<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3">
                            <h5 class="font-weight-bold mb-0">{{ $pageTitle ?? __('message.list') }}</h5>
                            <div class="d-flex align-items-center">
                                @if ($count != 3)
                                    <a href="{{ route('whydelivery.create') }}"class="btn btn-sm btn-primary jqueryvalidationLoadRemoteModel mr-2">{{ __('message.whydelivery') }}</a>
                                @endif
                                <a href="{{ route('help-whydelivery') }}" class="btn btn-xs loadRemoteModel help pt-1 pb-1" role="button">{{ __('message.help') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                    {{ html()->form()->close() }}
                    {{ html()->form('POST', route('frontend.website.information.update', 'why_choose'))->id('whydelivery_form')->attribute('enctype','multipart/form-data')->open() }}

                        <div class="row">
                            @foreach($whydelivery as $key => $value)
                                @if(in_array($key, ['title']))
                                    <div class="col-md-6 form-group">
                                        {!! html()->label(__('message.'.$key) . ' <span class="text-danger">*</span>')->for($key)->class('form-control-label') !!}
                                        {!! html()->text($key, $value ?? null)->placeholder(__('message.'.$key))->class('form-control')->required() !!}
                                    </div>
                                @else
                                    <div class="col-md-12 form-group">
                                        {!! html()->label(__('message.'.$key) . ' <span class="text-danger">*</span>')->for($key)->class('form-control-label') !!}
                                        {!! html()->textarea($key, $value ?? null)->class('form-control textarea')->rows(3)->placeholder(__('message.'.$key))->required() !!}
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <hr>
                        <div class="col-md-12 mt-1 mb-4">
                            <button class="btn btn-md btn-primary float-md-right" id="saveButton">{{ __('message.save') }}</button>
                        </div>
                    {{ html()->form()->close() }}
                    </div>
                    @if(count($data) > 0)
                        @include('whydelivery.list')
                    @endif
                    <br>
                </div>
            </div>
        </div>
    </div>
    @section('bottom_script')
    <script>
        $(document).ready(function() {
            $("#whydelivery_form").validate({
                rules: {
                    'value[]': {
                        required: true
                    }
                },
                messages: {
                    'value[]': {
                        required: "{{ __('message.please_enter_value') }}"
                    }
                },
                errorPlacement: function(error, element) {
                    error.addClass('text-danger');
                    error.insertAfter(element);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                    $(element).removeClass('is-valid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                    $(element).addClass('is-valid');
                }
            });
        });
    </script>
    @endsection
</x-master-layout>
