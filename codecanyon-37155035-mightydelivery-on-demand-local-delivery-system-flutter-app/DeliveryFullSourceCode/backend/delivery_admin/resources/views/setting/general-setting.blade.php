{!! html()->modelForm($settings, 'POST' , route('settingsUpdates'))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open() !!}
{!! html()->hidden('id',  null)->class('form-control') !!}
{!! html()->hidden('page', $page)->class('form-control') !!}

<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <label for="avatar" class="col-sm-3 form-control-label">{{ __('message.logo') }}</label>
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-4">
                        <img src="{{ getSingleMedia($settings,'site_logo') }}" width="100"  id="site_logo_preview" alt="site_logo" class="image site_logo site_logo_preview">
                        @if(getMediaFileExit($settings, 'site_logo'))
                            <a class="text-danger remove-file" href="{{ route('remove.file', ['id' => $settings->id, 'type' => 'site_logo']) }}"
                                data--submit="confirm_form"
                                data--confirmation='true'
                                data--ajax="true"
                                title='{{ __("message.remove_file_title" , ["name" =>  __("message.image") ]) }}'
                                data-title='{{ __("message.remove_file_title" , ["name" =>  __("message.image") ]) }}'
                                data-message='{{ __("message.remove_file_msg") }}'>
                                <i class="ri-close-circle-line"></i>
                            </a>
                        @endif
                    </div>
                    <div class="col-sm-8">
                        @if(env('APP_DEMO') == true)
                            <div class="alert alert-danger">
                                {{ __('message.demo_permission_denied') }}
                            </div>
                        @else
                            <div class="custom-file col-md-12">
                                {!! html()->file('site_logo')->class('custom-file-input custom-file-input-sm detail')->id('site_logo')->attribute('lang', 'en')->accept('image/*') !!}
                                {!! html()->label(__('message.logo'))->for('site_logo')->class('custom-file-label') !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="avatar" class="col-sm-3 form-control-label">{{ __('message.dark_logo') }}</label>
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-4">
                        <img src="{{ getSingleMedia($settings,'site_dark_logo') }}" width="100"  id="site_dark_logo_preview" alt="site_dark_logo" class="image site_dark_logo site_dark_logo_preview border">
                        @if(getMediaFileExit($settings, 'site_dark_logo'))
                            <a class="text-danger remove-file" href="{{ route('remove.file', ['id' => $settings->id, 'type' => 'site_dark_logo']) }}"
                                data--submit="confirm_form"
                                data--confirmation='true'
                                data--ajax="true"
                                title='{{ __("message.remove_file_title" , ["name" =>  __("message.image") ]) }}'
                                data-title='{{ __("message.remove_file_title" , ["name" =>  __("message.image") ]) }}'
                                data-message='{{ __("message.remove_file_msg") }}'>
                                <i class="ri-close-circle-line"></i>
                            </a>
                        @endif
                    </div>
                    <div class="col-sm-8">
                        @if(env('APP_DEMO') == true)
                            <div class="alert alert-danger">
                                {{ __('message.demo_permission_denied') }}
                            </div>
                        @else
                            <div class="custom-file col-md-12">
                                {!! html()->file('site_dark_logo')->class('custom-file-input custom-file-input-sm detail')->id('site_dark_logo')->attribute('lang', 'en')->accept('image/*') !!}
                                {!! html()->label(__('message.dark_logo'))->for('site_dark_logo')->class('custom-file-label') !!}
                            </div>
                         @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="avatar" class="col-sm-6 form-control-label">{{ __('message.favicon') }}</label>
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-4">
                        <img src="{{ getSingleMedia($settings,'site_favicon') }}" height="30"  id="site_favicon_preview" alt="site_favicon" class="image site_favicon site_favicon_preview">
                        @if(getMediaFileExit($settings, 'site_favicon'))
                            <a class="text-danger remove-file" href="{{ route('remove.file', ['id' => $settings->id, 'type' => 'site_favicon']) }}"
                                data--submit="confirm_form"
                                data--confirmation='true'
                                data--ajax="true"
                                title='{{ __("message.remove_file_title" , ["name" =>  __("message.image") ]) }}'
                                data-title='{{ __("message.remove_file_title" , ["name" =>  __("message.image") ]) }}'
                                data-message='{{ __("message.remove_file_msg") }}'>
                                <i class="ri-close-circle-line"></i>
                            </a>
                        @endif
                    </div>
                    <div class="col-sm-8">
                        @if(env('APP_DEMO') == true)
                            <div class="alert alert-danger">
                                {{ __('message.demo_permission_denied') }}
                            </div>
                        @else
                            <div class="custom-file col-md-12">
                                {!! html()->file('site_favicon')->class('custom-file-input custom-file-input-sm detail')->id('site_favicon')->attribute('lang', 'en')->accept('image/*') !!}
                                {!! html()->label(__('message.site_favicon'))->for('site_dark_logo')->class('custom-file-label') !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            {!! html()->label(__('message.site_name'))->for('site_name')->class('col-sm-6  form-control-label') !!}
            <div class="col-sm-12">
                {!! html()->text('site_name', null)->class('form-control')->placeholder(__('message.site_name')) !!}
            </div>
        </div>

        <div class="form-group">
            {!! html()->label(__('message.site_description'))->for('site_description')->class('col-sm-6  form-control-label') !!}
            <div class="col-sm-12">
                {!! html()->textarea('site_description', null)->class('form-control textarea')->placeholder(__('message.site_description'))->attribute('rows','3') !!}
            </div>
        </div>

        <div class="form-group">
            {!! html()->label(__('message.contact_email'))->for('contact_email')->class('col-sm-6  form-control-label') !!}
            <div class="col-sm-12">
                {!! html()->text('support_email', null)->class('form-control')->placeholder(__('message.contact_email')) !!}
            </div>
        </div>

        <div class="form-group">
            {!! html()->label(__('message.contact_number'))->for('contact_number')->class('col-sm-6  form-control-label') !!}
            <div class="col-sm-12">
                {!! html()->text('support_number', null)->class('form-control')->placeholder(__('message.contact_number')) !!}
            </div>
        </div>

        <div class="form-group">
            {!! html()->label(__('message.site_email'))->for('site_email')->class('col-sm-6  form-control-label') !!}
            <div class="col-sm-12">
                {!! html()->text('site_email', null)->class('form-control')->placeholder(__('message.site_email')) !!}
            </div>
        </div>
        <div class="form-group">
            <div class="custom-control custom-switch custom-switch-color custom-control-inline ml-2">
                {!! html()->hidden('admin_login_button', 0) !!}
                <div class="col-sm-12">
                    {!! html()->checkbox('admin_login_button', 1)->checked(isset($settings) && $settings->admin_login_button == 1)->class('custom-control-input bg-success')->id('switch_button') !!}
                    {!! html()->label(__('message.admin_login_button'))->for('switch_button')->class('custom-control-label') !!}
                </div>
            </div>
        </div>

    </div>
    <div class="col-lg-6">
        <div class="form-group">
            {!! html()->label(__('message.default_language'))->for('default_language')->class('col-sm-12  form-control-label') !!}
            <div class="col-sm-12">
                <select class="form-control select2js default_language" name="env[DEFAULT_LANGUAGE]" id="default_language">
                    @foreach(languagesArray() as $language)
                        <option value="{{ $language['id'] }}" {{ config('app.locale') == $language['id']  ? 'selected' : '' }}  >{{ $language['title'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            {!! html()->label(__('message.language_option'))->for('language_option')->class('col-sm-12  form-control-label') !!}
            <div class="col-sm-12">
                <select class="form-control select2js language_option" name="language_option[]" id="language_option" multiple>
                    @foreach(languagesArray() as $language)
                        @if(config('app.locale') == $language['id']  )
                            <option value="{{ $language['id'] }}"  disabled="">{{ $language['title'] }}</option>
                        @else
                            <option value="{{ $language['id'] }}" {{in_array($language['id'], $settings->language_option) ? 'selected' : '' }}  >{{ $language['title'] }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            {!! html()->label(__('message.currency'))->for('currency')->class('col-sm-6  form-control-label') !!}
            <div class="col-sm-12">
                @php
                    $currency_code = $settings->currency_code  ?? 'USD';
                    $currency = currencyArray($currency_code);
                @endphp
                <select class="form-control select2js" name="currency_code" id="currency_code">
                    @foreach(currencyArray() as $array)
                        <option value="{{ $array['code'] }}" {{ $array['code'] == $currency_code  ? 'selected' : '' }}>
                            ( {{ $array['symbol'] }} ) {{ $array['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group">
            {!! html()->label(__('message.currency_position'))->for('currency_position')->class('col-sm-12  form-control-label') !!}
            <div class="col-sm-12">
                 {!! html()->select('currency_position', ['left' => __('message.left'),'right' => __('message.right')], $settings->currency_position ?? 'left')->class('form-control select2js')!!}
            </div>
        </div>

        <div class="form-group col-md-12">
            {!! html()->label( __('message.theme_color'))->for('bg_color_code')->class('col-sm-12  form-control-label') !!}
            <div class="input-group">
                {!! html()->input('color', 'bg_color', $settings->color ?? '#5e3c9e')->class('form-control form-control-color')->id('bg_color_picker')->attribute('title', __('message.theme_color'))->required()!!}
                {!! html()->text('color', $settings->color ?? '#5e3c9e')->placeholder(__('message.bg_color'))->class('form-control')->id('bg_color_code')->required()!!}
            </div>
        </div>

        <div class="form-group">
            {!! html()->label(__('message.timezone'), 'timezone')->class('col-sm-12 form-control-label') !!}
            <div class="col-sm-12">
                {!! html()->select('timezone', [ auth()->user()->timezone => timeZoneList()[ auth()->user()->timezone ] ])
                    ->value(old('timezone'))
                    ->class('form-control select2js')
                    ->attribute('data-ajax--url', route('ajax-list', ['type' => 'timezone']))
                    ->attribute('data-placeholder', __('message.select_field', ['name' => __('message.timezone')]))
                    ->required()
                !!}
            </div>
        </div>

        <div class="form-group">
            {!! html()->label(__('message.facebook_url'))->for('facebook_url')->class('col-sm-12  form-control-label') !!}
            <div class="col-sm-12">
                {!! html()->text('facebook_url', null)->class('form-control')->placeholder(__('message.enter_name', [ 'name' => __('message.facebook_url') ])) !!}
            </div>
        </div>
        <div class="form-group">
            {!! html()->label(__('message.twitter_url'))->for('twitter_url')->class('col-sm-12  form-control-label') !!}
            <div class="col-sm-12">
                {!! html()->text('twitter_url', null)->class('form-control')->placeholder(__('message.enter_name', [ 'name' => __('message.twitter_url') ])) !!}
            </div>
        </div>

        <div class="form-group">
            {!! html()->label(__('message.linkedin_url'))->for('linkedin_url')->class('col-sm-12  form-control-label') !!}
            <div class="col-sm-12">
                {!! html()->text('linkedin_url', null)->class('form-control')->placeholder(__('message.enter_name', [ 'name' => __('message.linkedin_url') ])) !!}
            </div>
        </div>

        <div class="form-group">
            {!! html()->label(__('message.instagram_url'))->for('instagram_url')->class('col-sm-12  form-control-label') !!}
            <div class="col-sm-12">
                {!! html()->text('instagram_url', null)->class('form-control')->placeholder(__('message.enter_name', [ 'name' => __('message.instagram_url') ])) !!}
            </div>
        </div>

        <div class="form-group">
            {!! html()->label(__('message.copyright_text'))->for('copyright_text')->class('col-sm-12  form-control-label') !!}
            <div class="col-sm-12">
                {!! html()->text('site_copyright', null)->class('form-control')->placeholder(__('message.enter_name', [ 'name' => __('message.site_copyright') ])) !!}
            </div>
        </div>
        {{-- <div class="form-group">
            {!! html()->label(__('message.help_support_url'))->for('help_support_url')->class('col-sm-12  form-control-label') !!}
            <div class="col-sm-12">
                {!! html()->text('help_support_url', null)->class('form-control')->placeholder(__('message.enter_name', [ 'name' => __('message.help_support_url') ])) !!}
            </div>
        </div> --}}
    </div>
    <hr>
     <div class="col-lg-12">
        <div class="form-group">
            <div class="col-md-offset-3 col-sm-12 ">
                {!! html()->submit(__('message.save'))->class('btn btn-md btn-primary float-md-right') !!}
            </div>
        </div>
     </div>
</div>
{!! html()->form()->close() !!}
<script>
    function getExtension(filename) {
            var parts = filename.split('.');
            return parts[parts.length - 1];
        }
        function isImage(filename) {
            var ext = getExtension(filename);
            switch (ext.toLowerCase()) {
                case 'jpg':
                case 'jpeg':
                case 'png':
                case 'gif':
                case 'ico':
                    return true;
            }
            return false;
        }
    function readURL(input,className) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            var res = isImage(input.files[0].name);
            if(res == false){
                var msg = 'Image should be png/PNG, jpg/JPG & jpeg/JPG.';
                Snackbar.show({text: msg ,pos: 'bottom-right',backgroundColor:'#d32f2f',actionTextColor:'#fff'});
                $(input).val("");
                return false;
            }
            reader.onload = function(e){
                $(document).find('img.'+className).attr('src', e.target.result);
                $(document).find("label."+className).text((input.files[0].name));
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
    $(document).ready(function (){
        $('.select2js').select2();
        $(document).on('change','#site_logo',function(){
            readURL(this,'site_logo');
        });
        $(document).on('change','#site_favicon',function(){
            readURL(this,'site_favicon');
        });

        $('.default_language').on('change', function (e) {
            var id= $(this).val();
            $('.language_option option:disabled').prop('selected',true);
            $('.language_option option').prop('disabled',false);

            $('.language_option option').each(function(index, val){
                var $this = $(this);
                if(id == $this.val()){
                $this.prop('disabled',true);
                $this.prop('selected',false);
                }
            });
            $('.language_option').select2("destroy").select2();
        });
        function colorCodeInput() {
                    var colorCode = $('#bg_color_code').val();
                    if (colorCode[0] !== '#') {
                        colorCode = '#' + colorCode;
                    }
                    $('#bg_color_code').val(colorCode);
                }

        $('#bg_color_code').on('input', function() {
            colorCodeInput();
            var colorCode = $(this).val();
            $('#bg_color_picker').val(colorCode);
        });

        $('#bg_color_picker').on('input', function() {
            var selectedColor = $(this).val();
            $('#bg_color_code').val(selectedColor);
        });

        colorCodeInput();
    })
</script>
