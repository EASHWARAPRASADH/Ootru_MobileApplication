<x-master-layout :assets="$assets ?? []">
    <div>
        <?php $id = $id ?? null;?>
            {!! html()->form('POST',route('wallet.store'))->open() !!}
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{ $pageTitle }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="new-user-info">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    {!! html()->label(__('message.type').' <span class="text-danger">*</span>')->for('type')->class('form-control-label') !!}
                                    {!! html()->select('type', ['credit' => __('message.credit'), 'debit' => __('message.debit')], old('type'))->class('form-control select2js')->required() !!}
                                </div>
                                <div class="col-md-12 form-group">
                                    {!! html()->label(__('message.type').' <span class="text-danger">*</span>')->for('type')->class('form-control-label') !!}
                                    {!! html()->number('total_amount', null)->placeholder(__('message.amount'))->class('form-control')->required() !!}
                                 </div>

                            </div>

                            {!! html()->submit(__('message.save'))->class('btn btn-md btn-primary float-right') !!}

                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! html()->form()->close() !!}
    </div>
@section('bottom_script')
<script>
    (function($) {
        $(document).ready(function () {
            $(".select2js").select2({
                width: "100%",
                tags: true
            });
        });
    })(jQuery);
</script>
@endsection
</x-master-layout>
