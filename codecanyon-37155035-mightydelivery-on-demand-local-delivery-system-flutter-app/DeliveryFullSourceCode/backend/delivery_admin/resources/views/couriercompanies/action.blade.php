<?php
    $auth_user= authSession();
?>
@if($action_type == 'action')
    {{ html()->form('DELETE', route('couriercompanies.destroy', $id))->attribute('data--submit', 'couriercompanies'.$id)->open() }}
        <div class="d-flex justify-content-end align-items-center">
            @if($auth_user->can('couriercompanies-edit'))
                <a class="mr-2 loadRemoteModel" href="{{ route('couriercompanies.edit', $id) }}" title="{{ __('message.update_form_title',['form' => __('message.courier_companies') ]) }}"><i class="fas fa-edit text-primary"></i></a>
            @endif

            @if($auth_user->can('couriercompanies-delete'))
                <a class="mr-2 text-danger" href="javascript:void(0)" data--submit="couriercompanies{{$id}}"
                    data--confirmation='true' data-title="{{ __('message.delete_form_title',['form'=> __('message.courier_companies') ]) }}"
                    title="{{ __('message.delete_form_title',['form'=>  __('message.courier_companies') ]) }}"
                    data-message='{{ __("message.delete_msg") }}'>
                    <i class="fas fa-trash-alt"></i>
                </a>
            @endif
        </div>
    {{ html()->form()->close() }}
@endif
@if($action_type == 'status')
    <div class="custom-switch custom-switch-text custom-switch-color custom-control-inline m-0">
        <div class="custom-switch-inner">
            <input type="checkbox" class="custom-control-input bg-success change_status" data-type="couriercompanies" id="{{ $data->id }}" data-id="{{ $data->id }}" {{ ($data->status == '0' ? 0 : 1) ? 'checked' : '' }} value="{{ $data->id }}">
            <label class="custom-control-label" for="{{ $data->id }}" data-on-label="Yes" data-off-label="No"></label>
        </div>
    </div>
@endif   
