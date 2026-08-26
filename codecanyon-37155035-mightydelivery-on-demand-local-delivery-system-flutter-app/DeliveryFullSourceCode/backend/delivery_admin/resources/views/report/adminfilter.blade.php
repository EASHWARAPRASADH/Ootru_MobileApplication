<div class="card-body">
    {!! html()->form('GET', route('report-adminEarning'))->id('filter-form')->open() !!}
        <div class="row justify-content-end align-items-end">
            <div class="form-group col-auto">
                {!! html()->label(__('message.from') . '<span class="text-danger">*</span>')->for('from_date')->class('form-control-label') !!}
                <span class="text-danger" id="form_validation_from_date"></span>
                {!! html()->date('from_date', $params['from_date'] ?? request('from_date'))->placeholder(__('message.date'))->class('form-control datepicker select2Clear')->id('from_date_main') !!}
            </div>
            <div class="form-group col-auto">
                {!! html()->label(__('message.to') . ' <span class="text-danger">*</span>')->for('to_date')->class('form-control-label') !!}
                <span class="text-danger" id="form_validation_to_date"></span>
                {!! html()->date('to_date', $params['to_date'] ?? request('to_date'))->placeholder(__('message.date'))->class('form-control datepicker select2Clear')->id('to_date_main') !!}
            </div>
            <div class="form-group col-sm-0">
                {!! html()->button(__('message.apply_filter'))->type('submit')->class('btn btn-sm btn-primary text-white pt-2 pb-2 clearListPropertynumber') !!}
            </div>
            <div class="form-group col-md-auto text-right">
                <a href="{{ route('report-adminEarning') }}" class="btn btn-light text-dark pt-2 pb-2">
                    <i class="ri-repeat-line" style="font-size:12px"></i> {{ __('message.reset_filter') }}
                </a>
                <button type="button" class="btn btn-info text-white pt-2 pb-2" data-toggle="modal" data-target="#exportModal">
                    <i class="ri-download-line" style="font-size:12px"></i> {{ __('message.export') }}
                </button>
            </div>
        </div>
        {!! html()->form()->close() !!}
    @include('report.adminexportmodel')
</div>
<script src="{{ asset('frontend-website/assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script>
    $(document).ready(function () { 
        $.validator.addMethod('greaterThanEqual', function (value, element, param) {
            var fromDateValue = $(param).val();
            if (!value || !fromDateValue) {
                return true;
            }
            return new Date(value) >= new Date(fromDateValue);
        });

        $('#filter-form').validate({
            rules: {
                from_date: {
                    required: true
                },
                to_date: {
                    greaterThanEqual: '#from_date_main'
                }
            },
            messages: {
                from_date: {
                    required: "{{ __('message.from_date_required') }}"
                },
                to_date: {
                    greaterThanEqual: "{{ __('message.to_date_must_be_greater_than_from_date') }}"
                }
            },
            errorPlacement: function (error, element) {
                error.addClass('text-danger');
                if (element.attr("name") == "from_date") {
                    $('#form_validation_from_date').prepend(error);
                } else if (element.attr("name") == "to_date") {
                    $('#form_validation_to_date').prepend(error);
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function (element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>