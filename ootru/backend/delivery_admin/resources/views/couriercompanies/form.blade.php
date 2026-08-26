
<?php $id = $id ?? null;?>
@if(isset($id))
    {{ html()->modelForm($data, 'PATCH', route('couriercompanies.update', $id))->id('courier_companies_form')->open() }}
@else
    {{ html()->form('POST', route('couriercompanies.store'))->id('courier_companies_form')->open() }} 
@endif
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{__($pageTitle)}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-group col-md-12">
                {{ html()->label(__('message.name').' <span class="text-danger">*</span>')->class('form-control-label') }}
                {{ html()->text('name', old('name'))->placeholder(__('message.name'))->class('form-control') }}
                <span class="text-danger" id="ajax_form_validation_name"></span>
            </div>

            <div class="form-group col-md-12">
                {{ html()->label(__('message.link').' <span class="text-danger">*</span>')->class('form-control-label') }}
                {{ html()->text('link', old('link'))->placeholder(__('message.link'))->class('form-control') }}
                <span class="text-danger" id="ajax_form_validation_link"></span>
            </div>

            <div class="form-group col-md-12">
                <label class="form-control-label" for="image">{{ __('message.image') }}</label>
                <div class="custom-file">
                    {{ html()->file('couriercompanies_image')
                        ->class('custom-file-input')
                        ->id('couriercompanies_image')
                        ->attribute('data--target', 'couriercompanies_image_preview')
                        ->attribute('lang', 'en')
                        ->attribute('accept', 'image/*') }}
                    <label class="custom-file-label">{{ __('message.choose_file', ['file' => __('message.image')]) }}</label>
                </div>
                <span class="selected_file"></span>
            </div>

            <div class="col-md-6 mb-2">
                @if(isset($id) && getMediaFileExit($data, 'couriercompanies_image'))
                    <img id="couriercompanies_image_preview" src="{{ getSingleMedia($data, 'couriercompanies_image' ?? 'images/default.png') }}" alt="image" class="attachment-image mt-1 couriercompanies_image_preview">
                @else
                    <img id="couriercompanies_image_preview" src="{{ asset('images/default.png') }}" alt="image" class="attachment-image mt-1 couriercompanies_image_preview">
                @endif
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-md btn-secondary" data-dismiss="modal">{{ __('message.close') }}</button>
            <button type="submit" class="btn btn-md btn-primary" id="btn_submit" data-form="ajax-submite-jquery-validation" >{{ isset($id) ?  __('message.update') : __('message.save') }}</button>
        </div>
    </div>
</div>
{{ html()->form()->close() }}
<script>
    $(document).ready(function () {
            $("form").on("submit", function () {
            $(this).find(":submit").prop("disabled", true);
        });
        formValidation("#courier_companies_form", {
            name: { required: true },
        }, {
            name: { required: "{{__('message.please_enter_name')}}"},
        });
    });
</script>

