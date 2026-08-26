<x-master-layout :assets="$assets ?? []">
    <div>
        <?php $id = $id ?? null;?>
            {!! html()->form('POST' ,route('deliverymandocument.store'))->attribute('enctype', 'multipart/form-data')->id('deliverymandocument_form')->open() !!}
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{ $pageTitle }}</h4>
                        </div>
                        <div class="card-action">
                            <a href="{{ route('deliverymandocument.index') }} " class="btn btn-sm btn-primary" role="button">{{ __('message.back') }}</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="new-user-info">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    {!! html()->label(__('message.deliverymandocument', ['select' => __('message.deliverymandocument')]) . ' <span class="text-danger">*</span>')
                                        ->class('form-control-label')
                                        ->for('delivery_man_id') !!}
                                
                                    {!! html()->select('delivery_man_id', isset($data->delivery_man) ? [$data->delivery_man->id => $data->delivery_man->name] : [], old('delivery_man_id'))
                                        ->class('form-control select2js deliverymandocument')
                                        ->required()
                                        ->attribute('data-placeholder', __('message.select_name', ['select' => __('message.deliverymandocument')]))
                                        ->attribute('data-ajax--url', route('ajax-list', ['type' => 'deliveryman_name'])) !!}
                                </div>
                                
                                <div class="form-group col-md-4">
                                    {!! html()->label(__('message.document', ['select' => __('message.document')]) . ' <span class="text-danger">*</span>')
                                    ->class('form-control-label')
                                    ->for('document_id') !!}
                                
                                    {!! html()->select('document_id', isset($data->document) ? [$data->document->id => $data->document->name] : [], old('document_id'))
                                        ->class('form-control select2js document')
                                        ->required()
                                        ->attribute('data-placeholder', __('message.select_name', ['select' => __('message.document')]))
                                        ->attribute('data-ajax--url', route('ajax-list', ['type' => 'document_name'])) !!}
                                </div>
                                
                                <div class="form-group col-md-4">
                                    <label class="form-control-label" for="image">{{ __('message.document') }} </label>
                                    <div class="custom-file">
                                        <input class="custom-file-input" type="file" name="delivery_man_document" accept="image/*">
                                        <label class="custom-file-label">{{  __('message.choose_file',['file' =>  __('message.image') ]) }}</label>
                                    </div>
                                </div>
                                @if(isset($id) && getMediaFileExit($data, 'delivery_man_document'))
                                    <div class="col-md-2 mb-2 position-relative">
                                        <img id="delivery_man_document_preview" src="{{ getSingleMedia($data,'delivery_man_document') }}" alt="delivery_man_document-image" class="avatar-100 mt-1">
                                        <a class="text-danger remove-file" href="{{ route('remove.file', ['id' => $data->id, 'type' => 'delivery_man_document']) }}"
                                            data--submit='confirm_form'
                                            data--confirmation='true'
                                            data--ajax='true'
                                            data-toggle='tooltip'
                                            title='{{ __("message.remove_file_title" , ["name" =>  __("message.image") ]) }}'
                                            data-title='{{ __("message.remove_file_title" , ["name" =>  __("message.image") ]) }}'
                                            data-message='{{ __("message.remove_file_msg") }}'
                                            >
                                            <i class="ri-close-circle-line"></i>
                                        </a>
                                    </div>
                                @endif                                                                                       
                            </div>
                            <hr>
                            {!! html()->submit(isset($id) ? __('message.update') : __('message.save'))->class('btn btn-md btn-primary float-right') !!}
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
                formValidation("#deliverymandocument_form", {
                    delivery_man_id: { required: true },
                    document_id: { required: true },
                    delivery_man_document: { required: true },
                }, {
                    delivery_man_id: { required: "{{__('message.please_select_deliveryman')}}" },
                    document_id: { required: "{{__('message.please_select_document')}}" },
                    delivery_man_document: { required: "{{__('message.please_select_document_image')}}" },
                });
            });
        </script>
    @endsection
</x-master-layout>
