<!-- Modal -->
<div class="modal-dialog modal-dialog-centered modal-md" role="document">
    {{ html()->form('POST', route('ordercancel.save'))->open() }} 
    {{ html()->hidden('id', $id) }}
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">{{ $pageTitle }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="col-md-12 form-group">
                {{ html()->label(__('message.reason'), 'reason')->class('form-control-label') }}
                {{ html()->select('reason', [
                    'Place order by mistake' => __('message.order_by_mistake'),
                    'Delivery time is too long' => __('message.time_too_long'),
                    'Duplicate order' => __('message.duplicate_order'),
                    'Change of mind' => __('message.change_mind'),
                    'Change order' => __('message.change_order'),
                    'Incorrect/incomplete address' => __('message.incomplated_address'),
                    'Other' => __('message.other')
                ], old('reason'))->class('form-control select2js')->id('reason_select') }}
            </div>
            <div class="col-md-12 form-group" id="other_reason_field" style="display: none;">
                {{ html()->label(__('message.reason'), 'reason')->class('form-control-label') }}
                {{ html()->text('other_reason')
                    ->class('form-control')
                    ->placeholder(__('message.reason')) }}
            </div>
            {{ html()->submit(__('message.submit'))->class('btn btn-md btn-primary float-right') }}
        </div>
    </div>
    {{ html()->form()->close() }}
</div>

<script>
    $(".select2js").select2({
        width: "100%",
        tags: true
    });
    $('#reason_select').on('change', function () {
        if ($(this).val() === 'Other') {
            $('#other_reason_field').show();
        } else {
            $('#other_reason_field').hide();
        }
    });
</script>
