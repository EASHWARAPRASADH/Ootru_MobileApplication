<div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{ $pageTitle }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
       {!! html()->form('POST', route('store.frontend.order.status.data', ['id' => $id ]))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open() !!}
       {!! html()->hidden('type', 'order_status_section')->placeholder(__('message.order_status_section')) !!}
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 form-group">
                   {!! html()->label(__('message.title'))->class('form-control-label') !!}
                   {!! html()->text('title', isset($id) ? optional($data)->title : old('title'))->placeholder(__('message.title'))->class('form-control') !!}
                </div>
                <div class="col-md-12 form-group">
                   {!! html()->label(__('message.description') . ' <span class="text-danger">*</span>')->for('description')->class('form-control-label')!!}
                   {!! html()->textarea('description', old('description', isset($id) && isset($data) ? $data->description : null,))->class('form-control textarea')->placeholder(__('message.description')) !!}
                </div>
                <div class="form-group col-md-8">
                    {!! html()->label(__('message.order_status_section_image'))->for('order_status_section_image')->class('form-control-label') !!}
                    <div class="custom-file mb-1">
                        {!! html()->file('order_status_section_image')->class('custom-file-input')->accept('image/*')->attribute('data--target', 'order_status_section_image_preview') !!}
                        {!! html()->label(__('message.choose_file', ['file' => __('message.image')]))->class('custom-file-label') !!}
                    </div>
                    <span class="selected_file"></span>
                </div>
                
                <div class="col-md-4 mb-2 position-relative">
                    @if( isset($id) && isset($data) && getMediaFileExit($data,'order_status_section'))
                        <img id="order_status_section_preview" src="{{ getSingleMedia($data, $data->type) }}" alt="order_status_section_image" class="attachment-image mt-1 order_status_section_image_preview">
                        <a class="text-danger remove-file ml-1 mt-1" href="{{ route('remove.file', ['id' => $data->id, 'type' => 'order_status_section' ]) }}"
                            data--submit='confirm_form'
                            data--confirmation='true'
                            data--ajax='true'
                            data-toggle='tooltip'
                            title='{{ __("message.remove_file_title" , ["name" =>  __("message.image") ]) }}'
                            data-title='{{ __("message.remove_file_title" , ["name" =>  __("message.image") ]) }}'
                            data-message='{{ __("message.remove_file_msg") }}'>
                            @if(env('APP_DEMO'))
                            @else
                            <i class="ri-close-circle-line"></i>
                            @endif
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-md btn-secondary" data-dismiss="modal">{{ __('message.close') }}</button>
            <button type="submit" class="btn btn-md btn-primary" id="btn_submit" data-form="ajax" >{{ __('message.save') }}</button>
        </div>
    {!! html()->form()->close() !!}
    </div>
</div>

