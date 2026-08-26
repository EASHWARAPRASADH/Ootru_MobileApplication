    {!! html()->modelForm($setting_value, 'POST', route('settingUpdate'))->open() !!}
    {!! html()->hidden('id')->class('form-control') !!}
    {!! html()->hidden('printlabel','mobile_number_allow')!!}
    {!! html()->hidden('page', $page)->class('form-control') !!}

    <div class="card shadow mb-10">
        <div class="card-header">
            <h4>{{ $pageTitle }}</h4>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($setting as $key => $value)
                    @foreach($value as $sub_key => $sub_value)
                        @php
                            $data = null;
                            foreach($setting_value as $v) {
                                if ($v->key == $sub_key) {
                                    $data = $v;
                                }
                            }
                            $type = 'number';
                        @endphp
                            <div class="col-sm-12">
                                <div class="form-group">
                                    {!! html()->hidden('type[]', $sub_key)->class('form-control') !!}
                                    {!! html()->hidden('key[]', $sub_key)->class('form-control') !!}

                                    @if($key == 'printlabel')
                                    <div class="custom-switch custom-switch-color custom-control-inline">
                                        {!! html()->hidden('value['.$loop->index.']',0) !!}
                                        {!! html()->checkbox('value['.$loop->index.']', isset($data) && $data->value == 1, 1)->class('custom-control-input bg-success float-right')->id($sub_key) !!}
                                        {!! html()->label(__('message.' . $sub_key))->for($sub_key)->class('custom-control-label') !!}

                                    </div>
                                    @endif
                                   
                                </div>
                            </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
{!! html()->submit(__('message.save'))->class('btn btn-md btn-primary float-md-right') !!}
{!! html()->form()->close() !!}

<script>
    $(document).ready(function() {
        $(".select2js").select2({
            width: "100%",
        });
        function checkValue(input) {
            if (input.value < 100) {
                input.setCustomValidity('The value must be greater than or equal to 100.');
            } else {
                input.setCustomValidity('');
            }
        }
    });
</script>
