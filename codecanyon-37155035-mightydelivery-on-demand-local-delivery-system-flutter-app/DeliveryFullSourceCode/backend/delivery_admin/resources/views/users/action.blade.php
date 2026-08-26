<?php
    $auth_user = authSession();
?>
@if($deleted_at != null)
    <div class="d-flex justify-content-end align-items-center">
        @if($auth_user->can('users-edit'))
            {!! html()->a(route('users.restore', ['id' => $id, 'type' => 'restore']))
                ->class('mr-2')
                ->attribute('data--confirmation--restore', 'true')
                ->attribute('title', __('message.restore_title'))
                ->html('<i class="ri-refresh-line" style="font-size:18px"></i>') !!}
        @endif

        {!! html()->form('DELETE', route('users.force.delete', ['id' => $id, 'type' => 'forcedelete']))->attribute('data--submit', 'users' . $id)->open() !!}
            @if($auth_user->can('users-delete'))
                {!! html()->a('javascript:void(0)')
                    ->class('mr-2 text-danger')
                    ->attribute('data--submit', 'users' . $id)
                    ->attribute('data--confirmation', 'true')
                    ->attribute('data-title', __('message.delete_form_title', ['form' => __('message.users')]))
                    ->attribute('title', __('message.force_delete_form_title', ['form' => __('message.users')]))
                    ->attribute('data-message', __('message.force_delete_msg'))
                    ->html('<i class="ri-delete-bin-2-fill" style="font-size:18px"></i>') !!}
            @endif
        {!! html()->form()->close() !!}
    </div>
@else
    <div class="custom-switch custom-switch-text custom-switch-color custom-control-inline">
        <div class="custom-switch-inner">
            @if($action_type == 'email_verified')
                {!! html()->checkbox('email_verified', $data['is_autoverified_email'] == '1', $data['id'])
                    ->class('custom-control-input bg-success change_user_verification')
                    ->id('email_' . $data['id'])
                    ->attribute('data-type', 'user')
                    ->attribute('data-name', 'is_autoverified_email')
                    ->attribute('data-id', $data['id']) !!}
                {!! html()->label()->for('email_' . $data['id'])->class('custom-control-label') !!}
            @endif

            @if($action_type == 'mobile_verified')
                {!! html()->checkbox('mobile_verified', $data['is_autoverified_mobile'] == '1', $data['id'])
                    ->class('custom-control-input bg-success change_user_verification')
                    ->id('mobile_' . $data['id'])
                    ->attribute('data-type', 'user')
                    ->attribute('data-name', 'is_autoverified_mobile')
                    ->attribute('data-id', $data['id']) !!}
                {!! html()->label()->for('mobile_' . $data['id'])->class('custom-control-label') !!}
            @endif
        </div>
    </div>

    @if($action_type == 'status')
        <div class="custom-switch custom-switch-text custom-switch-color custom-control-inline m-0">
            <div class="custom-switch-inner">
                {!! html()->checkbox('status', $data->status == '1', $data->id)
                    ->class('custom-control-input bg-success change_status')
                    ->id($data->id)
                    ->attribute('data-type', 'user')
                    ->attribute('data-id', $data->id) !!}
                {!! html()->label()->for($data->id)->class('custom-control-label')->attribute('data-on-label', 'Yes')->attribute('data-off-label', 'No') !!}
            </div>
        </div>
    @endif

    @if($action_type == 'action')
        <div class="d-flex justify-content-end align-items-center">
            @if($auth_user->can('users-edit'))
                {!! html()->a(route('users.edit', $id))->class('mr-2')
                    ->attribute('title', __('message.update_form_title', ['form' => __('message.users')]))
                    ->html('<i class="fas fa-edit text-primary"></i>') !!}
            @endif

            @if($auth_user->can('users-show'))
                {!! html()->a(route('users.show', $id))->class('mr-2')->html('<i class="fas fa-eye text-secondary"></i>') !!}
            @endif

            @if($auth_user->can('users-delete'))
                {!! html()->form('DELETE', route('users.destroy', $id))->attribute('data--submit', 'users' . $id)->open() !!}
                    {!! html()->a('javascript:void(0)')
                        ->class('mr-2 text-danger')
                        ->attribute('data--submit', 'users' . $id)
                        ->attribute('data--confirmation', 'true')
                        ->attribute('data-title', __('message.delete_form_title', ['form' => __('message.users')]))
                        ->attribute('title', __('message.delete_form_title', ['form' => __('message.users')]))
                        ->attribute('data-message', __('message.delete_msg'))
                        ->html('<i class="fas fa-trash-alt"></i>') !!}
                    
                {!! html()->form()->close() !!}
            @endif
        </div>
    @endif
@endif
