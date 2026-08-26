<?php
    $auth_user= authSession();
?>
@if($delete_at != null)
    <div class="d-flex justify-content-end align-items-center">
        @if($auth_user->can('city-edit'))
            <a class="mr-2" href="{{ route('city.restore', ['id' => $id ,'type'=>'restore']) }}" data--confirmation--restore="true" title="{{ __('message.restore_title') }}"><i class="ri-refresh-line" style="font-size:18px"></i></a>
        @endif
        {{ html()->form('DELETE', route('city.force.delete', ['id' => $id, 'type' => 'forcedelete']))->attribute('data--submit', 'city'.$id)->open() }}
            @if($auth_user->can('city-delete'))
                <a class="mr-2 text-danger" href="javascript:void(0)"
                    data--submit="city{{ $id }}"
                    data--confirmation="true"
                    data-title="{{ __('message.delete_form_title', ['form' => __('message.city')]) }}"
                    title="{{ __('message.force_delete_form_title', ['form' => __('message.city')]) }}"
                    data-message="{{ __('message.force_delete_msg') }}">
                    <i class="ri-delete-bin-2-fill" style="font-size:18px"></i>
                </a>
            @endif
        {{ html()->form()->close() }}
    </div>
@else
    @if($action_type == 'status')
        <div class=" custom-switch custom-switch-text custom-switch-color custom-control-inline">
            <div class="custom-switch-inner">
                <input type="checkbox" class="custom-control-input bg-success change_status" data-type="city" id="{{ $data->id }}" data-id="{{ $data->id }}" {{ ($data->status == '0' ? 0 : 1) ? 'checked' : '' }} value="{{ $data->id }}">
                <label class="custom-control-label" for="{{ $data->id }}" data-on-label="Yes" data-off-label="No"></label>
            </div>
        </div>
    @endif

    @if($action_type == 'action')
        <div class="d-flex justify-content-end align-items-center">
            @if($auth_user->can('city-edit'))
            <a class="mr-2" href="{{ route('city.edit', $id) }}" title="{{ __('message.update_form_title',['form' => __('message.city') ]) }}"><i class="fas fa-edit text-primary"></i></a>
            @endif

            @if($auth_user->can('city-show'))
            <a class="mr-2" href="{{ route('city.show',$id) }}"><i class="fas fa-eye text-secondary"></i></a>
            @endif

            @if($auth_user->can('city-delete'))
                {{ html()->form('DELETE', route('city.destroy', $id))->attribute('data--submit', 'city'.$id)->open() }}
                    <a class="mr-2 text-danger" href="javascript:void(0)"
                        data--submit="city{{ $id }}"
                        data--confirmation="true"
                        data-title="{{ __('message.delete_form_title', ['form' => __('message.city')]) }}"
                        title="{{ __('message.delete_form_title', ['form' => __('message.city')]) }}"
                        data-message="{{ __('message.delete_msg') }}">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                {{ html()->form()->close() }}
            @endif
        </div>
    @endif
@endif
