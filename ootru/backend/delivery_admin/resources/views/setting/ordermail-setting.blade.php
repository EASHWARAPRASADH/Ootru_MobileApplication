 {!! html()->modelForm($setting_value, 'POST', route('settingUpdate'))->open() !!}

     {!! html()->hidden('id')->class('form-control') !!}
    {!! html()->hidden('page', $page)->class('form-control') !!}
    {!! html()->hidden('mail_template_setting', 'mail_template_setting') !!}

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
                            <div class="col-sm-4">
                                <div class="form-group">
                                    {!! html()->hidden('type[]', $key)->class('form-control') !!}
                                    {!! html()->hidden('key[]', $sub_key) !!}
                        
                                    @if($key == 'order_mail')
                                        <div class="custom-switch custom-switch-color custom-control-inline">
                                            {!! html()->hidden('value['.$loop->index.']', 0) !!}
                                            {!! html()->checkbox('value['.$loop->index.']', 1)->checked(isset($data) && !empty($data->value) && $data->value == 1)->class('custom-control-input bg-success float-right')->id($sub_key) !!}
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
