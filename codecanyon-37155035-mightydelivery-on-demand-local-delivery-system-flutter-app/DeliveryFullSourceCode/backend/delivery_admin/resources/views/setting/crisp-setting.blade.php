{!! html()->modelForm($setting_value, 'POST' , route('settingUpdate'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open() !!}
{!! html()->hidden('id',  null)->class('form-control') !!}
{!! html()->hidden('page', $page)->class('form-control') !!}

    <div class="row">
        @foreach($setting as $key => $value)
            <div class="col-md-12 col-sm-12 card shadow mb-10">
                <div class="card-header">
                    <h4>{{ str_replace('_',' ',$key) }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($value as $sub_keys => $sub_value)
                            @php
                                $data=null;
                                foreach($setting_value as $v){

                                    if($v->key==($key.'_'.$sub_keys)){
                                        $data = $v;
                                    }
                                }
                                $class = 'col-md-6';
                                $type = 'text';
                                switch ($key){
                                    case 'FIREBASE':
                                        $class = 'col-md-12';
                                        break;
                                    case 'COLOR' : 
                                        $type = 'color';
                                        break;
                                    case 'DISTANCE' :
                                        $type = 'number';
                                        break;
                                    default : break;
                                }
                            @endphp
                            <div class=" {{ $class }} col-sm-12">
                                <div class="form-group">
                                    <label for="{{ $key.'_'.$sub_keys }}">{{ str_replace('_',' ',$sub_keys) }} </label>
                                    {!! html()->hidden('type[]', $key)->class('form-control') !!}
                                    <input type="hidden" name="key[]" value="{{ $key.'_'.$sub_keys }}">
                                    @if($sub_keys == 'ENABLE/DISABLE')
                                    <div class="col-md-4">
                                        <div class="custom-control custom-radio custom-control-inline col-2">
                                            {!! html()->radio('value[]', old('value[]', optional($data)->value) == 1, 1)
                                                ->class('custom-control-input')
                                                ->id('yes') !!}
                                            {!! html()->label(__('message.yes'))->for('yes')->class('custom-control-label') !!}
                                        </div>

                                        <div class="custom-control custom-radio custom-control-inline col-2">
                                            {!! html()->radio('value[]', old('value[]', optional($data)->value) == 0, 0)
                                                ->class('custom-control-input')
                                                ->id('no') !!}
                                            {!! html()->label(__('message.no'))->for('no')->class('custom-control-label') !!}
                                        </div>
                                    </div>

                                    @else
                                        <input type="{{ $type }}" name="value[]" value="{{ isset($data) ? $data->value : null }}" id="{{ $key.'_'.$sub_keys }}" {{ $type == 'number' ? "min=0 step='any'" : '' }} class="form-control form-control-lg" placeholder="{{ str_replace('_',' ',$sub_keys) }}">
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endForeach
    </div>
{!! html()->submit(__('message.save'))->class('btn btn-md btn-primary float-right') !!}
{!! html()->form()->close() !!}

<script>
    $(document).ready(function() {
        $('.select2js').select2();
    });
</script>
