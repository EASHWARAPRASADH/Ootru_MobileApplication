{!! html()->form('GET')->open() !!}
    <div class="row">
        <div class="form-group col-md-3">
                {!! html()->label(__('message.select_name', ['select' => __('message.screen')]))->for('screen')->class('form-control-label') !!}
                {!! html()->select('screen', isset($screen) ? [$screen->screenId => $screen->screenName] : [], old('screen'))
                    ->class('select2Clear form-group screen')
                    ->attribute('data-placeholder', __('message.select_name', ['select' => __('message.screen')]))
                    ->attribute('data-ajax--url', route('ajax-list', ['type' => 'screen'])) !!}       
        </div>
        <div class="form-group col-md-3 mt-1"> 
            {!! html()->button(__('message.apply_filter'))->class('btn btn-warning text-white mt-4 pt-2') !!}
            @if(isset($reset_file_button))
            {!! $reset_file_button !!}
            @endif
        </div>
    </div>
{!! html()->form()->close() !!}
