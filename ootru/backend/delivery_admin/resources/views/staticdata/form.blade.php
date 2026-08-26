
<?php $id = $id ?? null;?>
@if(isset($id))
    {{ html()->modelForm($data, 'PATCH', route('staticdata.update', $id))->id('parceltype_form')->open() }}
@else
    {{ html()->form('POST', route('staticdata.store'))->id('parceltype_form')->open() }} 
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
                {{ html()->label(__('message.label').' <span class="text-danger">*</span>')->class('form-control-label') }}
                {{ html()->text('label', old('label'))->placeholder(__('message.label'))->class('form-control') }}
                <span class="text-danger" id="ajax_form_validation_label"></span>
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
        formValidation("#parceltype_form", {
            label: { required: true },
        }, {
            label: { required: "{{__('message.please_enter_label')}}"},
        });
    });
</script>

