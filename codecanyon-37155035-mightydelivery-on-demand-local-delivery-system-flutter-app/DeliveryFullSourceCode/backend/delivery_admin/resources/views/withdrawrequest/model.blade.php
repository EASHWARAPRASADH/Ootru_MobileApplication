<?php $data = $data ?? null; ?>

@if($data)
    {!! html()->modelForm($data, 'PATCH', route('withdraw-history-edit', $data->id))->attribute('enctype', 'multipart/form-data')->open() !!}
@else
    {!! html()->form('POST', route('withdraw-deatils'))->attribute('enctype', 'multipart/form-data')->open() !!}
@endif

{!! html()->hidden('withdrawrequest_id', $id) !!}

<div class="modal-dialog modal-lg" id="modal" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{ $pageTitle }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="row col-md-12 p-2" id="clear-filter-list-data">
            <div class="form-group col-md-6">
                {!! html()->label(__('message.transaction') . ' <span class="text-danger">*</span>')->for('transaction_id')->class('form-control-label') !!}
                {!! html()->text('transaction_id', $data->transaction_id ?? old('transaction_id'))
                    ->placeholder(__('message.transaction'))
                    ->class('form-control') !!}
                <span class="text-danger" id="ajax_form_validation_label"></span>
            </div>

            <div class="form-group col-md-6">
                {!! html()->label(__('message.via') . ' <span class="text-danger">*</span>')->for('via')->class('form-control-label') !!}
                {!! html()->text('via', $data->via ?? old('via'))
                    ->placeholder(__('message.via'))
                    ->class('form-control') !!}
                <span class="text-danger" id="ajax_form_validation_label"></span>
            </div>

            <div class="form-group col-md-6">
                {!! html()->label(__('message.other_detail') . ' <span class="text-danger">*</span>')->for('other_detail')->class('form-control-label') !!}
                {!! html()->text('other_detail', $data->other_detail ?? old('other_detail'))
                    ->placeholder(__('message.other_detail'))
                    ->class('form-control') !!}
                <span class="text-danger" id="ajax_form_validation_label"></span>
            </div>

            <div class="form-group col-md-4">
                {!! html()->label(__('message.image'))->class('form-control-label')->for('image') !!}
                <div class="custom-file">
                    {!! html()->file('withdrawimage')->class('custom-file-input')->id('withdrawimage')->attribute('lang', 'en')->attribute('accept', 'image/*') !!}
                    {!! html()->label(__('message.choose_file', ['file' => __('message.image')]))->class('custom-file-label') !!}
                </div>
                <span class="selected_file"></span>
            </div>

            <div class="col-md-2 mb-2">
                @if($data && getMediaFileExit($data, 'withdrawimage'))
                    <img id="withdrawimage_preview" src="{{ getSingleMedia($data, 'withdrawimage' ?? 'images/default.png') }}" alt="image" class="attachment-image mt-1 withdrawimage_preview">
                @else
                    <img id="withdrawimage_preview" src="{{ asset('images/default.png') }}" alt="image" class="attachment-image mt-1 withdrawimage_preview">
                @endif
            </div>
        </div>

        <hr>
        <div class="modal-footer">
            {!! html()->submit(__('message.submit'))->id('apply-order-filter')->class('btn btn-md btn-primary float-right') !!}
        </div>
        {!! html()->closeModelForm() !!}
    </div>
</div>

<script>
    $(".select2js").select2({
        width: "100%",
    });
</script>
