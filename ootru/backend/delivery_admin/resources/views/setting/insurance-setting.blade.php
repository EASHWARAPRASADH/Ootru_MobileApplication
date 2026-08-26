{!! html()->modelForm($setting_value, 'POST', route('settingUpdate'))->open() !!}

{!! html()->hidden('id')->class('form-control') !!}
{!! html()->hidden('page', $page)->class('form-control') !!}
{!! html()->hidden('insurance_setting', 'insurance_setting') !!}

    <div class="card shadow mb-10">
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
                                    {!! html()->hidden('type[]', $sub_key) !!}
                                    {!! html()->hidden('key[]', $sub_key) !!}
                            
                                    @if($key == 'insurance')
                                        <div class="custom-switch custom-switch-color custom-control-inline">
                                            {!! html()->hidden('value['.$loop->index.']', 0) !!}
                                            {!! html()->checkbox('value['.$loop->index.']', 1)->checked(isset($data) && $data->value == 1)->class('custom-control-input bg-success float-right')->id($sub_key) !!}
                                            {!! html()->label(__('message.' . $sub_key))->for($sub_key)->class('custom-control-label') !!}
                                        </div>
                            
                                    @elseif($key == 'insurance_percentage')
                                        <div id="insurance_amount_input">
                                            {!! html()->label(__('message.' . $sub_key) . ' <span class="text-danger">*</span>')->for($sub_key)->class('control-label') !!}
                                            {!! html()->input($type, 'value[]', isset($data) ? $data->value : null)
                                                ->class('form-control form-control-lg')
                                                ->placeholder(str_replace('_', ' ', $sub_key))
                                                ->attributes($type == 'number' ? ['min' => 0, 'max' => 100, 'step' => 'any'] : [])
                                                ->required() !!}
                                        </div>
                            
                                    @elseif($key == 'insurance_description')
                                        <div id="insurance_description_select">
                                            @php
                                                $description = isset($data) ? App\Models\Pages::find($data->value) : null;
                                            @endphp
                                            {!! html()->label(__('message.insurance_page'))->for('insurance_page')->class('control-label') !!}
                                            {!! html()->select('value[]', isset($description) ? [$description->id => $description->title] : [])
                                                ->class('select2js form-group insurance_page')
                                                ->attribute('data-placeholder', __('message.insurance_page'))
                                                ->attribute('data-ajax--url', route('ajax-list', ['type' => 'page-list']))
                                                ->required() !!}
                                        </div>
                            
                                    @elseif($key == 'claim_duration')
                                        <div>
                                            {!! html()->label(__('message.' . $sub_key))->for($sub_key)->class('control-label') !!}
                                            {!! html()->input($type, 'value[]', isset($data) ? $data->value : null)
                                                ->class('form-control form-control-lg')
                                                ->placeholder(str_replace('_', ' ', $sub_key))
                                                ->attributes($type == 'number' ? ['min' => 0, 'max' => 100, 'step' => 'any', 'oninput' => 'checkValue(this)'] : ['oninput' => 'checkValue(this)']) !!}
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
        toggleFields($('#insurance_allow').is(':checked'));

            $('#insurance_allow').change(function() {
                toggleFields($(this).is(':checked'));
            });

            function toggleFields(isChecked) {
                if (isChecked) {
                    $('#insurance_description_select').show();
                    $('#insurance_amount_input').show();
                } else {
                    $('#insurance_description_select').hide();
                    $('#insurance_amount_input').hide();
                }
            }
    });
</script>
