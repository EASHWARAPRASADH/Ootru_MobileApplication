<?php
    $auth_user = authSession();
    $protected_roles = ['admin', 'demo_admin', 'client', 'delivery_man', 'user', 'agent'];
?>
<div class="d-flex justify-content-end align-items-center">
    @if( !in_array(strtolower($role->name), $protected_roles) )
        <div class="custom-control custom-switch custom-switch-text custom-switch-color custom-control-inline mr-2">
            <div class="custom-switch-inner">
                <input type="checkbox" class="custom-control-input bg-success change_status" data-type="role" id="{{ $role->id }}" data-id="{{ $role->id }}" {{ $role->status ? 'checked' : '' }} value = "{{ $role->id }}">
                <label class="custom-control-label" for="{{ $role->id }}" data-on-label="" data-off-label=""></label>
            </div>
        </div>

        {{ html()->form('DELETE', route('role.destroy', $role->id))->attribute('data--submit', 'role' . $role->id)->open() }}
            <a class="text-danger" href="javascript:void(0)" data--submit="role{{$role->id}}"
                data--confirmation='true' data-title="{{ __('message.delete_form_title',['form'=> __('message.role') ]) }}"
                title="{{ __('message.delete_form_title',['form'=>  __('message.role') ]) }}"
                data-message='{{ __("message.delete_msg") }}'>
                <i class="fas fa-trash-alt" style="font-size: 16px;"></i>
            </a>
        {{ html()->form()->close() }}
    @endif
</div>