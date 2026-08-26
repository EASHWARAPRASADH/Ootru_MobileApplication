<?php $id = $id ?? null; ?>
@if (isset($id))
    {{ html()->modelForm($data, 'PATCH', route('deliverypartner.update', $id))->attribute('enctype','multipart/form-data')->open() }}
@else
    {{ html()->form('POST', route('deliverypartner.store'))->attribute('enctype','multipart/form-data')->open() }} 
@endif
{!! html()->hidden('type', 'partner_benefits') !!}
<div class="modal-dialog modal-dialog-centered modal-xl" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{ __($pageTitle) }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-group col-md-12">
                {{ html()->label(__('message.title') . ' <span class="text-danger">*</span>', 'title')->class('form-control-label') }}
                {{ html()->text('title', old('title'))->placeholder(__('message.title'))->class('form-control')->required() }}
                <span class="text-danger" id="ajax_form_validation_title"></span>
            </div>
            <div class="form-group col-md-12">
                {{ html()->label(__('message.subtitle') . (isset($id) ? ' <span class="text-danger">*' . __('message.max_100_character') . '</span>' : ' <span class="text-danger">*</span>'), 'subtitle')->class('form-control-label') }}
                {{ html()->text('subtitle', old('subtitle'))->placeholder(__('message.subtitle'))->class('form-control')->required() }}
                <span class="text-danger" id="ajax_form_validation_subtitle"></span>
            </div>
            <div class="row ml-1">
                <div class="form-group col-md-4">
                    {{ html()->label(__('message.image'), 'frontend_data_image')->class('form-control-label') }}
                    <div class="custom-file">
                        {{ html()->file('frontend_data_image')->class('custom-file-input')->id('frontend_data_image')->accept('image/*')->attribute('lang', 'en') }}
                        <label class="custom-file-label" for="frontend_data_image">
                            {{ __('message.choose_file', ['file' => __('message.image')]) }}
                        </label>
                    </div>
                </div>
                {{-- @if (isset($id) && getMediaFileExit($data, 'frontend_data_image'))
                    <img src="{{ getSingleMedia($data, 'frontend_data_image' ?? 'images/default.png') }}"
                        alt="image" class="attachment-image mt-1 rounded-circle img-fluid avatar-50">
                @endif --}}
                <div class="col-md-2 mb-2 position-relative">
                    @if(isset($id) && getMediaFileExit($data, 'frontend_data_image'))
                    <div class="col-md-2 mb-2 position-relative">
                        <img id="frontend_data_image_preview" src="{{ getSingleMedia($data,'frontend_data_image') }}" alt="frontend-image" class="avatar-100 mt-1">
                        <a class="text-danger remove-file" href="{{ route('remove.file', ['id' => $data->id, 'type' => 'frontend_data_image']) }}"
                            data--submit='confirm_form'
                            data--confirmation='true'
                            data--ajax='true'
                            data-toggle='tooltip'
                            title='{{ __("message.remove_file_title" , ["name" =>  __("message.image") ]) }}'
                            data-title='{{ __("message.remove_file_title" , ["name" =>  __("message.image") ]) }}'
                            data-message='{{ __("message.remove_file_msg") }}'
                            >
                            <i class="ri-close-circle-line" id ="iconremove"></i></i>
                        </a>
                    </div>
                @endif
                </div>
            </div>
        </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-md btn-secondary" data-dismiss="modal">{{ __('message.close') }}</button>
                <button type="submit" class="btn btn-md btn-primary" id="btn_submit" data-form="ajax-submite-jquery-validation">{{ isset($id) ? __('message.update') : __('message.save') }}</button>
            </div>
        </div>
    </div>
{{ html()->form()->close() }}

