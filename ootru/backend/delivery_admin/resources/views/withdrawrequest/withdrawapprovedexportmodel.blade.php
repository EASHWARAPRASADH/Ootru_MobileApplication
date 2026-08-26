
<div class="modal-dialog modal-lg" id="modal" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{ __('message.approved_withdraw_request') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form id="exportForm" method="get">
                <div class="d-flex justify-content-center mb-3">
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-secondary active">
                            <input type="radio" name="options" id="option1" value="xlsx" checked> XLSX
                        </label>
                        <label class="btn btn-secondary">
                            <input type="radio" name="options" id="option2" value="xls"> XLS
                        </label>
                        <label class="btn btn-secondary">
                            <input type="radio" name="options" id="option3" value="ods"> ODS
                        </label>
                        <label class="btn btn-secondary">
                            <input type="radio" name="options" id="option4" value="csv"> CSV
                        </label>
                        <label class="btn btn-secondary">
                            <input type="radio" name="options" id="option6" value="html"> HTML
                        </label>
                    </div>
                </div>
                <hr>
                <h6 class="d-flex mb-3">{{ __('Select Columns') }}</h6>
                <div class="row">
                    <div class="col-md-6 mr-4">
                        @foreach(['name', 'amount', 'created_at','approval_date'] as $column)
                            <div class="d-flex">
                                <input type="checkbox" name="columns[]" value="{{ $column }}" checked>
                                <label class="form-check-label ml-2">{{ __('message.' . $column) }}</label>
                            </div>
                        @endforeach
                    </div>
                    <div class="col-md-6">
                    </div>
                </div>
            </form>
        </div>

        <hr>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
            <button type="button" class="btn btn-primary" id="downloadBtn">{{ __('Download') }}</button>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#downloadBtn').on('click', function() {
            var fileType = $('input[name="options"]:checked').val();
            var columns = $('input[name="columns[]"]:checked').map(function() {
                return $(this).val();
            }).get();

            var url = '{{ route("download-withdrawapprovedexcel", ":file_type") }}'.replace(':file_type', fileType);

            var queryString = $.param({
                columns: columns,
            });

            window.location.href = url + '?' + queryString;
        });
    });
</script>
