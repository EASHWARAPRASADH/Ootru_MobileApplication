<x-master-layout :assets="$assets ?? []">
    <div>
        <?php $id = $id ?? null;?>
        {!! html()->form('POST', route('passwordchnage'))->id('chnagepassword_form')->open() !!}
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{ $pageTitle  }}</h4>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="new-user-info">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    {!! html()->label(__('message.old_password') . ' <span class="text-danger">*</span>')->for('old_password')->class('form-control-label')!!}
                                    <div class="input-group">
                                        {!! html()->password('old_password')->placeholder(__('message.old_password'))->class('form-control')->id('password')->required() !!}

                                        <div class="input-group-append">
                                            <span class="input-group-text hide-show-password" style="cursor: pointer;">
                                                <i class="fas fa-eye-slash"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    {!! html()->label(__('message.new_password') . ' <span class="text-danger">*</span>')->for('new_password')->class('form-control-label')!!}

                                    <div class="input-group">
                                        {!! html()->password('new_password')->placeholder(__('message.new_password'))->class('form-control')->id('new_password')->required() !!}
                                        <div class="input-group-append">
                                            <span class="input-group-text hide-show-password" style="cursor: pointer;">
                                                <i class="fas fa-eye-slash"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    {!! html()->label(__('message.confirm_password') . ' <span class="text-danger">*</span>')->for('confirm_password')->class('form-control-label')!!}

                                    <div class="input-group">
                                        {!! html()->password('confirm_password')->placeholder(__('message.confirm_password'))->class('form-control')->id('confirm_password')->required() !!}

                                        <div class="input-group-append">
                                            <span class="input-group-text hide-show-password" style="cursor: pointer;">
                                                <i class="fas fa-eye-slash"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {!! html()->submit(__('message.save'))->class('btn btn-primary  btn-xl btn-block') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! html()->form()->close() !!}
    </div>
    @section('bottom_script')
        @parent
        <script>
            $(document).ready(function() {
                $('.hide-show-password').on('click', function() {
                    var eyeIcon = $(this).find('i');
                    var passwordInput = $(this).closest('.input-group').find('input');

                    var passwordFieldType = passwordInput.attr('type');
                    if (passwordFieldType === 'password') {
                        passwordInput.attr('type', 'text');
                        eyeIcon.removeClass('fa-eye-slash').addClass('fa-eye');
                    } else {
                        passwordInput.attr('type', 'password');
                        eyeIcon.removeClass('fa-eye').addClass('fa-eye-slash');
                    }
                });
                formValidation("#chnagepassword_form", {
                        old_password: { required: true },
                        new_password: { required: true },
                        confirm_password: { required: true },
                    }, {
                        old_password: { required: "{{__('message.please_enter_old_password')}}" },
                        new_password: { required: "{{__('message.please_enter_new_password')}}" },
                        confirm_password: { required: "{{__('message.please_enter_confirm_password')}}" },
                });
            });
        </script>
    @endsection
</x-master-layout>
