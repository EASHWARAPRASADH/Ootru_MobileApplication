<?php
    $auth_user= authSession();
?>
@if($deleted_at != null)
    <div class="d-flex justify-content-end align-items-center">
        @if($auth_user->can('deliveryman-edit'))
        <a class="mr-2" href="{{ route('deliveryman.restore', ['id' => $id ,'type'=>'restore']) }}" data--confirmation--restore="true" title="{{ __('message.restore_title') }}"><i class="ri-refresh-line" style="font-size:18px"></i></a>
        @endif
        {{ html()->form('DELETE', route('deliveryman.force.delete', [$id, 'type' => 'forcedelete']))->attribute('data--submit', 'deliveryman'.$id)->open() }}
                @if($auth_user->can('deliveryman-delete'))
                    <a class="mr-2 text-danger" href="javascript:void(0)" data--submit="deliveryman{{$id}}"
                        data--confirmation='true' data-title="{{ __('message.delete_form_title',['form'=> __('message.delivery_man') ]) }}"
                        title="{{ __('message.force_delete_form_title',['form'=>  __('message.delivery_man') ]) }}"
                        data-message='{{ __("message.force_delete_msg") }}'>
                        <i class="ri-delete-bin-2-fill" style="font-size:18px"></i>
                    </a>
                @endif
            </div>
        {{ html()->form()->close() }}
 @else
    <div class="custom-switch custom-switch-text custom-switch-color custom-control-inline">
        <div class="custom-switch-inner">
            @if($action_type == 'email_verified')
                <input type="checkbox" class="custom-control-input bg-success change_user_verification"
                    data-type="user" data-name="is_autoverified_email" id="email_{{ $data['id'] }}" data-id="{{ $data['id'] }}"
                    {{ $data['is_autoverified_email'] == '1' ? 'checked' : '' }} value="{{ $data['id'] }}">
                <label class="custom-control-label" for="email_{{ $data['id'] }}" data-on-label="" data-off-label=""></label>
            @endif
            @if($action_type == 'mobile_verified')
                <input type="checkbox" class="custom-control-input bg-success change_user_verification"
                    data-type="user" data-name="is_autoverified_mobile" id="mobile_{{ $data['id'] }}" data-id="{{ $data['id'] }}"
                    {{ $data['is_autoverified_mobile'] == '1' ? 'checked' : '' }} value="{{ $data['id'] }}">
                <label class="custom-control-label" for="mobile_{{ $data['id'] }}" data-on-label="" data-off-label=""></label>
            @endif
            @if($action_type == 'document_verified')
                <input type="checkbox" class="custom-control-input bg-success change_user_verification"
                    data-type="user" data-name="is_autoverified_document" id="document_{{ $data['id'] }}" data-id="{{ $data['id'] }}"
                    {{ $data['is_autoverified_document'] == '1' ? 'checked' : '' }} value="{{ $data['id'] }}">
                <label class="custom-control-label" for="document_{{ $data['id'] }}" data-on-label="" data-off-label=""></label>
            @endif
        </div>
    </div>
    @if($action_type == 'status')
        <div class="custom-switch custom-switch-text custom-switch-color custom-control-inline m-0">
            <div class="custom-switch-inner">
                <input type="checkbox" class="custom-control-input bg-success change_status" data-type="user" id="{{ $data->id }}" data-id="{{ $data->id }}" {{ ($data->status == '0' ? 0 : 1) ? 'checked' : '' }} value="{{ $data->id }}">
                <label class="custom-control-label" for="{{ $data->id }}" data-on-label="Yes" data-off-label="No"></label>
            </div>
        </div>
    @endif
    @if($action_type == 'verify')
        <div class="custom-switch custom-switch-text custom-switch-color custom-control-inline">
            <div class="custom-switch-inner">
                <input type="checkbox" class="custom-control-input bg-success change_verify" data-type="verify" id="{{ $data->id }}_verifySwitch" data-id="{{ $data->id }}" {{ $data->otp_verify_at ? 'checked' : '' }} value="{{ $data->id }}">
                <label class="custom-control-label" for="{{ $data->id }}_verifySwitch" data-on-label="Yes" data-off-label="No"></label>
            </div>
        </div>
    @endif
    @if($action_type == 'action')
        <div class="d-flex justify-content-end align-items-center">
            @if($auth_user->can('deliveryman-edit'))
                <a class="mr-2" href="{{ route('deliveryman.edit', $id) }}" title="{{ __('message.update_form_title',['form' => __('message.deliveryman') ]) }}"><i class="fas fa-edit text-primary"></i></a>
            @endif

            @if($auth_user->can('deliveryman-show'))
            <a class="mr-2" href="{{ route('deliveryman.show',$id) }}"><i class="fas fa-eye text-secondary"></i></a>
            @endif

            @if($auth_user->can('deliveryman-delete'))
                {{ html()->form('DELETE', route('deliveryman.destroy', $id))->attribute('data--submit', 'deliveryman'.$id)->open() }}
                    <a class="mr-2 text-danger" href="javascript:void(0)" data--submit="deliveryman{{$id}}"
                        data--confirmation='true' data-title="{{ __('message.delete_form_title',['form'=> __('message.delivery_man') ]) }}"
                        title="{{ __('message.delete_form_title',['form'=>  __('message.delivery_man') ]) }}"
                        data-message='{{ __("message.delete_msg") }}'>
                        <i class="fas fa-trash-alt"></i>
                    </a>
                {{ html()->form()->close() }}
            @endif
        </div>
    @endif
@endif
