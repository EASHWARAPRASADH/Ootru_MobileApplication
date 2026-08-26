<?php $id = $id ?? null; ?>
    {!! html()->form('POST', route('withdrawrequest.store'))->open() !!}
<div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{ $pageTitle }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-group col-md-12">
                {!! html()->label(__('message.withdraw'))->class('form-control-label')->for('withdraw') !!}
                {!! html()->number('amount', old('withdraw'))->placeholder(__('message.withdraw'))->class('form-control')->required()
                    ->attribute('min', 0) !!}
            </div>
        </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-md btn-secondary"
                    data-dismiss="modal">{{ __('message.close') }}</button>
                <button type="submit" class="btn btn-md btn-primary" id="btn_submit"
                    data-form="ajax">{{  __('message.save')  }}</button>
            </div>
        </div>
    </div> 
{!! html()->form()->close() !!}