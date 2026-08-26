<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-2">
                            <h5 class="font-weight-bold">{{ $pageTitle ?? __('message.list') }}</h5>
                            <a href="{{ route('clientreview.create') }}"class=" btn btn-sm btn-primary jqueryvalidationLoadRemoteModel" style="margin-left: 1123px;">{{ __('message.add_form_title', ['form' => __('message.review')]) }}</a>
                            <a href="{{ route('help-clientreview') }}" class="float-right btn btn-xs loadRemoteModel mr-3 help  pt-1 pb-1 mt-2 mb-2" role="button">{{ __('message.help') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                            {{ html()->form('POST', route('frontend.website.information.update', 'client_review'))->id('clientreview_form')->attribute('enctype', 'multipart/form-data')->open() }}
                        <div class="row">
                            @foreach ($clientreview as $key => $value)
                                @if (in_array($key, ['client_review_title']))
                                <div class="col-md-6 form-group">
                                    {{ html()->label(__('message.' . $key) . ' <span class="text-danger">*</span>', $key)->class('form-control-label') }}
                                    {{ html()->text($key, $value ?? null)->placeholder(__('message.' . $key))->class('form-control')->attribute('required', 'true') }}
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
                    @if (count($data) > 0)
                        @include('clientreview.list')
                    @else
                    @endif
                    <br>
                </div>
            </div>
        </div>
    </div>
    @section('bottom_script')
        <script>
            $(document).ready(function() {
                $("#clientreview_form").validate({
                    rules: {
                        'value[]': {
                            required: true
                        }

                    },
                    messages: {
                        'value[]': {
                            required: "{{ __('message.please_enter_title') }}"
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
