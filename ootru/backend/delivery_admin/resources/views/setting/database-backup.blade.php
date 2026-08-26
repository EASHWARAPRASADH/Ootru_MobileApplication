{!! html()->form('POST',route('order-setting-save'))->open() !!}
{!! html()->hidden('id',isset($setting_value[0]) ? $setting_value[0]['id'] : NULL ) !!}
{!! html()->hidden('database_backup','database_backup') !!}
<div class="card shadow mb-10">
    <div class="card-body">
        <div class="row"> 
            <div class="col-md-12">
                 @php
                    $data = null;
                    foreach($setting_value as $v){
                        
                    }
                @endphp
                <div class="row">
                    <div class="form-group col-md-4">
                        {!! html()->label(__('message.database_backup'). ' <span class="text-danger">*</span>')->for('database_backup')->class('form-control-label') !!}
                        {!! html()->select('backup_type', ['daily' => __('message.daily'), 'monthly' => __('message.monthly'), 'weekly' => __('message.weekly'),'none' => __('message.none')], isset($v) ? $v->backup_type : 'daily')->class('form-control select2js') !!}
                    </div>
                    @if(env('APP_DEMO'))
                        <div class="form-group col-md-4">
                            {!! html()->label(__('message.email').' <span class="text-danger">*</span>')->for('backup_email')->class('form-control-label') !!}
                            {!! html()->email('backup_email', isset($setting_value[0]) ? $setting_value[0]['backup_email']: old('backup_email'))->placeholder(__('message.email'))->class('form-control')->attribute('readonly')->required() !!}
                        </div>
                    @else
                    <div class="form-group col-md-4">
                        {!! html()->label(__('message.email').' <span class="text-danger">*</span>')->for('backup_email')->class('form-control-label') !!}
                        {!! html()->email('backup_email', isset($setting_value[0]) ? $setting_value[0]['backup_email']: old('backup_email'))->placeholder(__('message.email'))->class('form-control')->required() !!}
                    </div>

                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
{!! html()->button(__('message.save'))->type('submit')->class('btn btn-md btn-primary float-md-right') !!}
{!! html()->form()->close() !!}
<script>
        $(document).ready(function() {

        $('.select2js').select2();
        });
</script>




