<?php
    $auth_user= authSession();
?>
@if($deleted_at != null)
    <div class="d-flex justify-content-end align-items-center">
            <a class="mr-2" href="{{ route('rest-api.restore', ['id' => $id ,'type'=>'restore']) }}" data--confirmation--restore="true" title="{{ __('message.restore_title') }}"><i class="ri-refresh-line" style="font-size:18px"></i></a>
        {!! html()->form('DELETE', route('rest-api.force.delete', ['id' => $id, 'type' => 'forcedelete']))->attribute('data--submit', 'rest-api' . $id)->open() !!}
                <a class="mr-2 text-danger" href="javascript:void(0)" data--submit="rest-api{{$id}}"
                    data--confirmation='true' data-title="{{ __('message.delete_form_title',['form'=> __('message.rest_api') ]) }}"
                    title="{{ __('message.force_delete_form_title',['form'=>  __('message.rest_api') ]) }}"
                    data-message='{{ __("message.force_delete_msg") }}'>
                    <i class="ri-delete-bin-2-fill" style="font-size:18px"></i>
                </a>
        {!! html()->form()->close() !!}
    </div>
@else
    <div class="d-flex justify-content-end align-items-center">
        {!! html()->form('DELETE', route('rest-api.destroy', $id))->attribute('data--submit', 'rest-api' . $id)->open() !!}
            @if($auth_user->can('push notification-delete'))
                <div class="d-flex justify-content-end align-items-center">    
                    <a class="mr-2 text-danger" href="javascript:void(0)" data--submit="rest-api{{$id}}" 
                        data--confirmation='true' data-title="{{ __('message.delete_form_title',['form'=> __('message.rest_api') ]) }}"
                        title="{{ __('message.delete_form_title',[ 'form'=>  __('message.rest_api') ]) }}"
                        data-message='{{ __("message.delete_msg") }}'>
                        <i class="fas fa-trash-alt"></i>
                    </a>
                </div>
            @endif
        {!! html()->form()->close() !!}
    </div>
@endif