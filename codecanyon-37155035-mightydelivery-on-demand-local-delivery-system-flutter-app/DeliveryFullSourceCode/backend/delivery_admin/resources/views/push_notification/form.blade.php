
<x-master-layout :assets="$assets ?? []">
    <div>
        <?php $id = $id ?? null;?>
        @if(isset($id))
            {{ html()->modelForm($data, 'POST', route('pushnotification.store',['notify_type' => 'resend']))->attribute('enctype', 'multipart/form-data')->id('pushnotificaton_form')->open() }}
        @else
            {{ html()->form('POST', route('pushnotification.store'))->attribute('enctype', 'multipart/form-data')->id('pushnotificaton_form')->open() }} 
        @endif
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{ $pageTitle }}</h4>
                        </div>
                        <div class="card-action">
                            <a href="{{ route('pushnotification.index') }}" class="btn btn-sm btn-primary" role="button">{{ __('message.back') }}</a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="new-user-info">
                            <div class="row">

                                <div class="form-group col-md-4">
                                    {{ html()->label(__('message.user'))->class('form-control-label') }}
                                    {{ html()->select('client[]', $client, old('client'))
                                        ->id('client_list')
                                        ->class('select2js form-control')
                                        ->multiple()
                                        ->attribute('data-placeholder', __('message.select_name', ['select' => __('message.client')])) }}
                                </div>

                                <div class="form-group col-md-2">
                                    <div class="custom-control custom-checkbox mt-4 pt-3">
                                        <input type="checkbox" class="custom-control-input selectAll" id="all_client" data-usertype="client">
                                        <label class="custom-control-label" for="all_client">{{ __('message.select_all') }}</label>
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    {{ html()->label(__('message.delivery_man'))->class('form-control-label') }}
                                    {{ html()->select('delivery_man[]', $delivery_man, old('delivery_man'))
                                        ->id('delivery_man_list')
                                        ->class('select2js form-control')
                                        ->multiple()
                                        ->attribute('data-placeholder', __('message.select_name', ['select' => __('message.delivery_man')])) }}
                                </div>

                                <div class="form-group col-md-2">
                                    <div class="custom-control custom-checkbox mt-4 pt-3">
                                        <input type="checkbox" class="custom-control-input selectAll" id="all_delivery_man" data-usertype="delivery_man">
                                        <label class="custom-control-label" for="all_delivery_man">{{ __('message.select_all') }}</label>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    {{ html()->label(__('message.title').' <span class="text-danger">*</span>')->class('form-control-label') }}
                                    {{ html()->text('title', old('title'))
                                        ->placeholder(__('message.title'))
                                        ->class('form-control')
                                        ->attribute('required', true) }}
                                </div>

                                <div class="form-group col-md-12">
                                    {{ html()->label(__('message.message').' <span class="text-danger">*</span>')->class('form-control-label') }}
                                    {{ html()->textarea('message', null)
                                        ->class('form-control textarea')
                                        ->rows(3)
                                        ->attribute('required', true)
                                        ->placeholder(__('message.message')) }}
                                </div>

                                <div class="form-group col-md-4">
                                    {{ html()->label(__('message.image'))->class('form-control-label')->for('image') }}
                                    <div class="custom-file">
                                        {{ html()->file('notification_image')
                                            ->class('custom-file-input')
                                            ->id('notification_image')
                                            ->attribute('data--target', 'notification_image_preview')
                                            ->attribute('lang', 'en')
                                            ->attribute('accept', 'image/*') }}
                                        <label class="custom-file-label">{{ __('message.choose_file', ['file' => __('message.image')]) }}</label>
                                    </div>
                                    <span class="selected_file"></span>
                                </div>

                                <div class="col-md-2 mb-2">
                                    <img id="notification_image_preview" src="{{ asset('images/default.png') }}" alt="image" class="attachment-image mt-1 notification_image_preview">
                                </div>

                            </div>
                            <hr>
                            {{ html()->submit(__('message.send'))->class('btn btn-md btn-primary float-right') }}
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
            $('.select2js').select2();uslength;
                $('#delivery_man_list').next('span.select2').find('ul').html("<li class='ml-2'>" + count + " delivery manSelected</li>");
            }

            $('#all_client').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#client_list').find('option').prop('selected', true);
                } else {
                    $('#client_list').find('option').prop('selected', false);
                }
                $('#client_list').trigger('change');
                updateClientCounter();
            });

            $('#all_delivery_man').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#delivery_man_list').find('option').prop('selected', true);
                } else {
                    $('#delivery_man_list').find('option').prop('selected', false);
                }
                $('#delivery_man_list').trigger('change');
                updateDeliveryManCounter();
            });

            $('#client_list').on('change', function() {
                updateClientCounter();
            });

            $('#delivery_man_list').on('change', function() {
                updateDeliveryManCounter();
            });

            updateClientCounter();
            updateDeliveryManCounter();

            formValidation("#pushnotificaton_form", {
                title: { required: true },
                message: { required: true },
            }, {
                title: { required: "{{__('message.please_enter_name')}}" },
                message: { required: "{{__('message.please_enter_message')}}" },
            });
        });
    </script>
    @endsection
</x-master-layout>


