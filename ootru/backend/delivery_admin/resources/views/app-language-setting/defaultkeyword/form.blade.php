<!-- Modal -->
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{ $pageTitle }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @if(isset($id))
            {!! html()->modelForm($data, 'PATCH', route('defaultkeyword.update', $id))->open() !!}
        @else
            {!! html()->form('POST', route('defaultkeyword.store'))->open() !!}
        @endif
        <div class="modal-body">
            <div class="form-group">
                <div class="form-group col-md-12">
                    {!! html()->label(__('message.keyword_id'). ' <span class="text-danger">*</span>')->for('keyword_id')->class('form-control-label') !!}
                    {!! html()->number('keyword_id')->value(isset($id) ? $data->keyword_id : $lastKeywordId)->placeholder(__('message.keyword_id'))->class('form-control')->attribute('readonly', true)->required() !!}
                </div>
                <div class="form-group col-md-12">
                    {!! html()->label(__('message.keyword_title'). ' <span class="text-danger">*</span>')->for('keyword_name')->class('form-control-label') !!}
                    @if(isset($id))
                        <p>{{ $data->keyword_name }}</p>
                    @else
                        {{ html()->text('keyword_name',old('keyword_name'))->placeholder(__('message.keyword_title'))->class('form-control') ->attribute('required', 'required') }}
                    @endif
                </div>
                <div class="form-group col-md-12">
                    {!! html()->label(__('message.keyword_value'). ' <span class="text-danger">*</span>')->for('keyword_value')->class('form-control-label') !!}
                    {!! html()->text('keyword_value',old('keyword_value'))->placeholder(__('message.keyword_value'))->class('form-control')->required() !!}
                </div>
                <div class="form-group col-md-12">
                    {!! html()->label(__('message.screen_name') . ' <span class="text-danger">*</span>')->for('screen_id')->class('form-control-label')!!}
                    {!! html()->select('screen_id', isset($id) ? [optional($data->screen)->screenId => optional($data->screen)->screenName] : [])
                        ->id('screenName')
                        ->class('select2 form-group')
                        ->attribute('data-placeholder', __('message.select_name', ['select' => __('message.screen_name')]))
                        ->attribute('data-ajax--url', route('ajax-list', ['type' => 'screen']))
                        ->required()
                    !!}
                </div>
            </div>
        </div>
        <div class="modal-footer">
            {!! html()->submit(__('message.save'))->class('btn btn-md btn-primary float-right')->id('btn_submit')->attribute('data-form', 'ajax')!!}
            <button type="button" class="btn btn-md btn-secondary float-right mr-1" data-dismiss="modal">
                {{ __('message.close') }}
            </button>
        </div>
        {!!  html()->form()->close() !!}
    </div>
</div>
<script>
    $('#screenName').select2({
        width: '100%',
        placeholder: "{{ __('message.select_name', ['select' => __('message.screen_name')]) }}",
    });
</script>
