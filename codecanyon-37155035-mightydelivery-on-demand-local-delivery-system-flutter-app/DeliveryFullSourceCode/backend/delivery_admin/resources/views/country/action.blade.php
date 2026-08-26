<?php
    $auth_user = authSession();
?>
@if($delete_at != null)
    <div class="d-flex justify-content-end align-items-center">
        @if($auth_user->can('country-edit'))
            {!! html()->a(route('country.restore', ['id' => $id, 'type' => 'restore']))
                ->class('mr-2')
                ->attribute('data--confirmation--restore', 'true')
                ->attribute('title', __('message.restore_title'))
                ->html('<i class="ri-refresh-line" style="font-size:18px"></i>') !!}
        @endif
        {!! html()->form('DELETE', route('country.force.delete', [$id, 'type' => 'forcedelete']))
            ->attribute('data--submit', 'country' . $id)
            ->open() !!}
            @if($auth_user->can('country-delete'))
                {!! html()->a('javascript:void(0)')
                    ->class('mr-2 text-danger')
                    ->attribute('data--submit', 'country' . $id)
                    ->attribute('data--confirmation', 'true')
                    ->attribute('data-title', __('message.delete_form_title', ['form' => __('message.country')]))
                    ->attribute('title', __('message.force_delete_form_title', ['form' => __('message.country')]))
                    ->attribute('data-message', __('message.force_delete_msg'))
                    ->html('<i class="ri-delete-bin-2-fill" style="font-size:18px"></i>') !!}
            @endif
        {!! html()->form()->close() !!}
    </div>
@else
    @if($action_type == 'status')
    <div class="custom-switch custom-switch-text custom-switch-color custom-control-inline">
        <div class="custom-switch-inner">
            {!! html()->checkbox('status', $data->status == '1')
                ->class('custom-control-input bg-success change_status')
                ->attribute('data-type', 'country')
                ->id($data->id)
                ->attribute('data-id', $data->id)
                ->value($data->id) !!}
            {!! html()->label('')->for($data->id)->class('custom-control-label')->attribute('data-on-label', 'Yes')->attribute('data-off-label', 'No') !!}
        </div>
    </div>
    @endif

    @if($action_type == 'action')
        <div class="d-flex justify-content-end align-items-center">
            @if($auth_user->can('country-edit'))
                {!! html()->a(route('country.edit', $id))
                    ->class('mr-2')
                    ->attribute('title', __('message.update_form_title', ['form' => __('message.country')]))
                    ->html('<i class="fas fa-edit text-primary"></i>') !!}
            @endif
            @if($auth_user->can('country-delete'))
                {!! html()->form('DELETE', route('country.destroy', $id))->attribute('data--submit', 'country' . $id)->open() !!}
                    {!! html()->a('javascript:void(0)')
                        ->class('mr-2 text-danger')
                        ->attribute('data--submit', 'country' . $id)
                        ->attribute('data--confirmation', 'true')
                        ->attribute('data-title', __('message.delete_form_title', ['form' => __('message.country')]))
                        ->attribute('title', __('message.delete_form_title', ['form' => __('message.country')]))
                        ->attribute('data-message', __('message.delete_msg'))
                        ->html('<i class="fas fa-trash-alt"></i>') !!}
                {!! html()->form()->close() !!}
            @endif
        </div>
    @endif
@endif
