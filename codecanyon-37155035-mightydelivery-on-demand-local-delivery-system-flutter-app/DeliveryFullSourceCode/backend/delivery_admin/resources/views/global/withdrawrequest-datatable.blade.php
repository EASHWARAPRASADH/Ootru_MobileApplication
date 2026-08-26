
{!! html()->form('GET')->open() !!}
<div class="row p-2">
    <div class="form-group col-md-2">
        {!! html()->label(__('message.status') . ' <span class="text-danger">*</span>')->for('status')->class('form-control-label') !!}
        {!! html()->select('status', ['' => __('message.all'),'requested' => __('message.requested'),'approved' => __('message.approved'),
                'decline' => __('message.declined')], $params['status'] ?? old($params['status']))->class('form-control select2js')
                ->id('status-select') !!}        
    </div>
    <div class="form-group col-sm-0 mt-3" id="apply-filter-container">
        {!! html()->button(__('message.apply_filter'))->class('btn btn-sm btn-warning text-white mt-3 pt-2 pb-2') !!}
    </div>
    <div class="form-group col-sm-2 mt-3"  id="reset-button-container">
        @if(isset($reset_file_button))
        {!! $reset_file_button !!}
        @endif
    </div>
</div>
{!! html()->form()->close() !!}
