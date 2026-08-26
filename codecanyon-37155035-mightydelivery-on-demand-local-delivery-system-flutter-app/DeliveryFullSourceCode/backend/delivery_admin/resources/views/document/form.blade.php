
<?php $id = $id ?? null;?>
@if(isset($id))
    {{ html()->modelForm($data, 'PATCH', route('document.update', $id))->id('document_form')->open() }}
@else
    {{ html()->form('POST', route('document.store'))->attribute('data-toggle','validator')->id('document_form')->open() }} 
@endif
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{$pageTitle}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 form-group">
                    {!! html()->label(__('message.name') . ' <span class="text-danger">*</span>')->for('name')->class('form-control-label')!!}
                    {!! html()->text('name')->placeholder(__('message.name'))->class('form-control') !!}
                 </div>
                <div class="form-group col-md-6">
                    <div class="custom-control custom-checkbox m-2">
                        {!! html()->checkbox('is_required', isset($is_required) ? (bool) $is_required : false, 1)
                            ->class('custom-control-input')
                            ->id('is_required') !!}
                        {!! html()->label(__('message.required'))->class('custom-control-label')->for('is_required') !!}
                    </div>
                </div>  
            </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-md btn-secondary" data-dismiss="modal">{{ __('message.close') }}</button>
                    <button type="submit" class="btn btn-md btn-primary"id="btn_submit">{{ isset($id) ?  __('message.update') : __('message.save') }}</button>
                </div>
        </div>
        {!! html()->form()->close() !!}
    </div>
</div>
<script>
    $(document).ready(function(){
        $("#document_form").validate({
            rules: {
                name: {required: true },
            },
            messages: {
                name: {
                    required: "{{ __('message.please_enter_name') }}.",
                }
            },
            errorElement: "div",
            errorPlacement: function(error, element) {
                error.addClass("invalid-feedback");
                element.closest(".form-group").append(error);
            },
            highlight: function(element) {
                $(element).addClass("is-invalid");
            },
            unhighlight: function(element) {
                $(element).removeClass("is-invalid");
            }
        });
    });
</script>
