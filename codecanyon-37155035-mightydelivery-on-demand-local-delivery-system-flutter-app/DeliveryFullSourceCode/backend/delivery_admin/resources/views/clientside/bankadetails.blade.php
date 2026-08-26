<x-master-layout :assets="$assets ?? []">
    <div>
        <?php $id = $id ?? null;?>
        @if(isset($id))
            {{ html()->modelForm($userbankaccount, 'PATCH', route('banks-deatils-save', $id))->id('bank_form')->open() }}
        @else
            {{ html()->form('POST', route('banks-deatils-save'))->id('bank_form')->open() }} 
        @endif
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
                                <div class="form-group col-md-6">
                                    {!! html()->text('user_bank_account[bank_name]', $userbankaccount->bank_name ?? null)->placeholder(__('message.bank_name'))->class('form-control') !!}
                                </div>
                                <div class="form-group col-md-6">
                                    {!! html()->text('user_bank_account[account_number]', $userbankaccount->account_number ?? null)->placeholder(__('message.bank_account_number'))->class('form-control') !!}
                                </div>
                                <div class="form-group col-md-6">
                                    {!! html()->text('user_bank_account[account_holder_name]', $userbankaccount->account_holder_name ?? null)->placeholder(__('message.bank_account_holder_name'))->class('form-control') !!}
                                </div>
                                <div class="form-group col-md-6">
                                    {!! html()->text('user_bank_account[bank_code]', $userbankaccount->bank_code ?? null)->placeholder(__('message.bank_ifsc_code'))->class('form-control') !!}
                                </div>
                                <div class="form-group col-md-6">
                                    {!! html()->text('user_bank_account[bank_address]', $userbankaccount->bank_address ?? null)->placeholder(__('message.bank_address'))->class('form-control') !!}
                                </div>
                                <div class="form-group col-md-6">
                                    {!! html()->text('user_bank_account[routing_number]', $userbankaccount->routing_number ?? null)->placeholder(__('message.routing_number'))->class('form-control') !!}
                                </div>
                                <div class="form-group col-md-6">
                                    {!! html()->text('user_bank_account[bank_iban]', $userbankaccount->bank_iban ?? null)->placeholder(__('message.bank_iban'))->class('form-control') !!}
                                </div>
                                <div class="form-group col-md-6">
                                    {!! html()->text('user_bank_account[bank_swift]', $userbankaccount->bank_swift ?? null)->placeholder(__('message.bank_swift'))->class('form-control') !!}
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
            $(document).ready(function(){

                formValidation("#bank_form", {
                        'user_bank_account[bank_name]': { required: true },
                        'user_bank_account[account_number]': { required: true },
                        'user_bank_account[account_holder_name]': { required: true },
                        'user_bank_account[bank_code]': { required: true },
                    }, {
                        'user_bank_account[bank_name]': { required: "{{__('message.please_enter_bank_name')}}" },
                        'user_bank_account[account_number]': { required: "{{__('message.please_enter_account_number')}}" },
                        'user_bank_account[account_holder_name]': { required: "{{__('message.please_enter_holder_name')}}" },
                        'user_bank_account[bank_code]': { required: "{{__('message.please_enter_bank_code')}}" },
                });
            });
        </script>
    @endsection
</x-master-layout>
