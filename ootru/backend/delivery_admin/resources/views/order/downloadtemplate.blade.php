<!-- Modal -->
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{ $pageTitle }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="mt-2">
                            <div class="row">
                                <div class="col-md-12">
                                    <ul class="">
                                        <li class="mt-1">{{ __('message.download_template_title') }}</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <ol class="ml-4">
                                        <li>{{ __('message.goto') }} “{{ __('message.bulk_import_order_data_list') }}” </a>{{ __('message.section') }}.</li>
                                        <li>{{ __('message.goto_download_button_click_or_download_full_data') }}</li>
                                    </ol>
                                    <ol class="ml-4" start="3">
                                        <li>{{ __('message.download_template_value_three') }}</li>
                                        <li>{{ __('message.download_template_value_four') }}</li>
                                        <li>{{ __('message.download_template_value_five') }} <span class="font-weight-bold">"{{ __('message.order_data')  }}" </span> {{ __('message.download_template_value_six') }} </li>
                                        <li>{{ __('message.download_template_value_seven') }}</li>
                                        <li>{{ __('message.download_template_value_eight') }}</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-2">
                                    <h6 class="mb-2">{{ __('message.download_template_value_nine') }}  <span class="font-weight-bold">"{{ __('message.order_data')  }}" </span> {{ __('message.download_template_value_ten') }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                        {{-- <a href="{{ route('ordertemplate.excel') }}" class="btn btn-sm mr-3 downloadtemplate loadRemoteModel" role="button">
                            <i class="fas fa-file-download mr-2"></i> {{ __('message.download') }}
                        </a> --}}
                    </div>
                </div>
            </div>
    </div>
</div>