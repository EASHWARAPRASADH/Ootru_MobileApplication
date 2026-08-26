<div class="modal-dialog modal-dialog-centered modal-xl" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{optional($user)->name}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="form-group col-md-3 ">
                    {!! html()->label(__('message.bank_name'))->class('form-control-label text-secondary ml-2') !!}
                    <h6 class="ml-2">{{ optional($data)->bank_name }}</h6>
                </div>

                <div class="form-group col-md-3">
                    {!! html()->label(__('message.bank_account_holder_name'))->class('form-control-label text-secondary ml-2') !!}
                    <h6 class="ml-2"> {{optional($data)->account_holder_name }}</h6>
                </div>

                <div class="form-group col-md-3">
                    {!! html()->label(__('message.account_number'))->class('form-control-label text-secondary ml-2') !!}
                    <h6 class="ml-2"> {{optional($data)->account_number }}</h6>
                </div>

                <div class="form-group col-md-3">
                    {!! html()->label(__('message.bank_ifsc_code'))->class('form-control-label text-secondary ml-2') !!}
                    <h6 class="ml-2"> {{optional($data)->bank_code }}</h6>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="form-group col-md-3">
                    {!! html()->label(__('message.bank_address'))->class('form-control-label text-secondary ml-2') !!}
                    <h6 class="ml-2">{{optional($data)->bank_address }}</h6>
                </div>

                <div class="form-group col-md-3">
                    {!! html()->label(__('message.routing_number'))->class('form-control-label text-secondary ml-2') !!}
                    <h6 class="ml-2"> {{optional($data)->routing_number }}</h6>
                </div>

                <div class="form-group col-md-3">
                    {!! html()->label(__('message.bank_iban'))->class('form-control-label text-secondary ml-2') !!}
                    <h6 class="ml-2"> {{optional($data)->bank_iban }}</h6>
                </div>

                <div class="form-group col-md-3">
                    {!! html()->label(__('message.bank_swift'))->class('form-control-label text-secondary ml-2') !!}
                    <h6 class="ml-2"> {{optional($data)->bank_swift }}</h6>
                </div>
                <hr>
            </div>
        </div>
    </div>
</div>
