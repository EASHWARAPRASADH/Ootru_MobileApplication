<x-master-layout :assets="$assets ?? []">
    <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header1 d-flex justify-content-between mt-3 ml-3">
                        <div class="header-title">
                            <h4 class="card-title"><b>{{ $pageTitle }}</b>
                                <span class="" data-bs-toggle="tooltip" title="{{ __('message.help_info') }}">
                                    <i class="fas fa-info-circle fa-sm"></i>
                                </span>
                            </h4>
                        </div>
                        <div class="card-action mr-2 mb-2">
                            <a href="{{ route('order-help') }}" class="btn btn-sm loadRemoteModel mr-3 help" role="button">{{ __('message.help') }}</a>
                            <a href="{{ route('order-download.template') }}" class="btn btn-sm mr-3 downloadtemplate loadRemoteModel" role="button">
                                 {{ __('message.download_template_info') }}
                            </a>
                            <a href="{{ route('ordertemplate.excel') }}" class="btn btn-md  mr-3 ml-3 btn-success" role="button">
                                <i class="fas fa-file-download mr-2"></i>{{ __('message.download') }}</a>
                        </div>
                    </div>
                    <div style="text-align: end">
                        <h5 class="mt-2 mr-3">
                            <span class="font-weight-bold">{{ __('message.note') }}</span>
                            {{ __('message.order_bulk_note') }}
                        </h5>
                    </div>
                    {{ html()->form('POST', route('import.orderdata'))->attribute('enctype', 'multipart/form-data')->attribute('id', 'uploadForm')->open() }} 
                    <div class="card-body">
                        <div class="new-user-info">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="upload-area" id="uploadfile">
                                        <p>{{ __('Drop your template here or click on the browse file') }}</p>
                                        <input type="file" name="order_data" id="fileInput" accept=".xlsx,.xls" required hidden>
                                        <button type="button" class="btn btn-primary" onclick="document.getElementById('fileInput').click();">{{ __('Browse File') }}</button>
                                    </div>
                                    <div class="error-message text-danger mt-2" style="display: none;">{{ __('message.invalid_file_type') }}</div>
                                </div>
                            </div>
                            {{ html()->submit(__('message.import'))->class('btn btn-md btn-primary float-right mt-2 mb-2') }}
                        </div>
                    </div>
                    {{ html()->form()->close() }}
                </div>
            </div>
        </div>
    </div>
    <style>
        .upload-area {
            border: 2px dashed #ccc;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            background-color: #f8f9fa;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .upload-area:hover {
            background-color: #e9ecef;
        }
    </style>
</x-master-layout>
