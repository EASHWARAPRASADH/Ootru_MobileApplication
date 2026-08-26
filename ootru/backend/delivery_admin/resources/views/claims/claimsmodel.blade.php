<?php $data = $data ?? null; ?>

 {{ html()->form('POST', route('claims-history'))->attribute('files', true)->open() }}


<div class="modal-dialog modal-lg" id="modal" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 form-group">
                    {{ html()->label(__('message.amount').' <span class="text-danger">*</span>', 'amount', ['class' => 'form-control-label']) }}
                    {{ html()->number('amount', $data->amount ?? null)
                        ->placeholder(__('message.amount'))
                        ->class('form-control') }}
                </div>
                <div class="col-md-12 form-group">
                    {{ html()->label(__('message.description').' <span class="text-danger">*</span>', 'description', ['class' => 'form-control-label']) }}
                    {{ html()->textarea('description', $data->description ?? null)
                        ->class('form-control textarea')
                        ->rows(2)
                        ->placeholder(__('message.description')) }}
                </div>
            </div>
            <div class="form-group col-md-6">
                <label class="form-control-label" for="image">{{ __('message.attachment_file') }}</label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" name="attachment_resolve_file[]" accept="image/*" multiple>
                    <label class="custom-file-label">{{  __('message.choose_file',['file' =>  __('message.attachment_file') ]) }}</label>
                </div>
            </div>
            @if(isset($data) && $data->hasMedia('attachment_resolve_file'))
                <div id="image-preview" class="d-flex flex-wrap col-md-12">
                    @foreach($data->getMedia('attachment_resolve_file') as $media)
                    <div class="col-md-2 mb-2">
                        <a class="magnific-popup-image-gallery" href="{{ $media->getUrl() }}" title="{{ $media->name }}">
                            <img id="{{ $media->id }}_preview" src="{{ $media->getUrl() }}" alt="{{ $media->name }}" class="attachment-image mt-3">
                        </a>
                        <a class="text-danger remove-file mt-3 ml-1" href="{{ route('remove.file', ['id' => $media->id, 'type' =>'attachment_resolve_file']) }}"
                            data--submit='confirm_form'
                            data--confirmation='true'
                            data--ajax='true'
                            data-toggle='tooltip'
                            title='{{ __("message.remove_file_title" , ["name" => __("message.image") ]) }}'
                            data-title='{{ __("message.remove_file_title" , ["name" => __("message.image") ]) }}'
                            data-message='{{ __("message.remove_file_msg") }}'>
                            <i class="ri-close-circle-line"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
            @endif
            <hr>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('message.close') }}</button>
            {!! isset($data) ? '' : '<button type="submit" class="btn btn-primary">' . __('message.save') . '</button>' !!}            
        </div>
        {{ html()->hidden('claim_id', $id) }}
        {{ html()->form()->close() }}
    </div>
</div>
<script>
$(document).ready(function () {
    $("form").on("submit", function () {
        $(this).find(":submit").prop("disabled", true);
    });
});
</script>

