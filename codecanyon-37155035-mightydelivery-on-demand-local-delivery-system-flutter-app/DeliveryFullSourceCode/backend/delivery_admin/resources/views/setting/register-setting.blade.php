{!! html()->modelForm($setting_value, 'POST', route('settingUpdate'))->open() !!}
{!! html()->hidden('id',  null)->class('form-control') !!}
{!! html()->hidden('page', $page)->class('form-control') !!}
    <div class="row">
        @foreach($setting as $key => $value)
            <div class="col-md-12 col-sm-12 card shadow mb-10">
                <div class="card-header">
                    <h4>{{ $pageTitle }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($value as $sub_key => $sub_value)
                            @php
                                $data = null;
                                foreach($setting_value as $v) {
                         
                                    if ($v->key == $sub_key) {
                                        $data = $v;
                                    }
                                }
                            @endphp
                            <div class="col-sm-12">
                                <div class="form-group">
                                    {!! html()->hidden('type[]', $sub_key)->class('form-control') !!}
                                    {!! html()->hidden('key[]', $sub_key) !!}
                                    @if($key == 'register_setting')
                                        <div class="custom-switch custom-switch-color custom-control-inline">
                                            {!! html()->hidden('value['.$loop->index.']', 0) !!}
                                            {!! html()->checkbox('value['.$loop->index.']', 1)->checked(isset($data) && $data->value == 1)->class('custom-control-input bg-success float-right')->id($sub_key) !!}
                                            {!! html()->label(__('message.' . $sub_key))->for($sub_key)->class('custom-control-label') !!}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
{!! html()->submit(__('message.save'))->class('btn btn-md btn-primary float-md-right') !!}
{!! html()->form()->close() !!}
