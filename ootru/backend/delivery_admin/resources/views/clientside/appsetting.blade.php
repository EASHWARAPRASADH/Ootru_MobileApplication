<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3">
                            <h5 class="font-weight-bold">{{ $pageTitle  }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <p>{{__('message.are_you_sure_want_to_delete_your_account')}}</p>
                            </div>
                            {!! html()->form('DELETE', route('users.destroy', $client))->attribute('data--submit', 'account_delete'.$client)->open() !!}
                                <a class="ml-3 btn btn-md btn btn-success text-center mr-2 text-danger" href="javascript:void(0)" data--submit="account_delete{{$client}}"
                                    data--confirmation='true' data-title="{{ __('message.delete_form_title',['form'=> __('message.account') ]) }}"
                                    data-message='{{ __("message.delete_msg") }}'>
                                    <i class="fas fa-trash-alt"></i> {{__('message.deleted_account')}}
                                </a>
                            {!! html()->form()->close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @section('bottom_script')
    @endsection
</x-master-layout>
