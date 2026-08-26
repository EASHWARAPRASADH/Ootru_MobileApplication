@if( count($data) > 0 )
    @foreach ($data as $frontend_data)
        <tr>
            <td><img src="{{ getSingleMedia($frontend_data, $frontend_data->type) }}" alt="{{ $title }}"class="bg-soft-primary rounded img-fluid avatar-40 me-3"></td>
            <td>{{ $frontend_data->title }}</td>
            <td>{{ $frontend_data->description }}</td>
            <td>
                <a href="{{ route('website.section.order.status', ['type' => 'section','sub_type' => $frontend_data->type,'id' => $frontend_data->id ]) }}" class="float-end btn btn-sm loadRemoteModel">
                    <i class="fas fa-edit text-primary"></i>
                </a>
                <a class="btn btn-sm btn-icon" 
                    data-bs-toggle="tooltip" href="{{ route('delete.frontend.order.status.data', [ 'id' => $frontend_data->id]) }}"
                    data--confirmation='true'
                    data--ajax='true'
                    data-title="{{ __('message.delete_form_title',[ 'form'=> __('message.'.$title) ]) }}"
                    title="{{ __('message.delete_form_title',[ 'form'=>  __('message.'.$title) ]) }}"
                    data-message='{{ __("message.delete_msg") }}'>
                    <i class="fas fa-trash-alt"></i>
                </a>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="{{ in_array($type,['client-testimonial']) ? 5 : 4 }}" class="text-center">
            {{ __('message.not_found_entry', [ 'name' => __('message.'.$title) ]) }}
        </td>
    </tr>
@endif