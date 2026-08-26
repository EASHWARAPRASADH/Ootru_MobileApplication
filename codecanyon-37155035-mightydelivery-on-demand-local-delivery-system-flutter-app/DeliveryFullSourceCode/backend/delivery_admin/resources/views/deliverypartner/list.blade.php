<div class="row col-md-12">
    <div class="col-lg-12">
        <h4 class="modal-title">{{__('message.section_list')}}</h4>
        <br>
        @foreach($data as $item)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <img src="{{ getSingleMedia($item, 'frontend_data_image') }}" class="avatar-100 img-fluid rounded" >
                        </div>
                        <div class="col-sm-10">
                            <div class="pt-2"><strong>{{__('message.title')}}</strong>: {{ $item->title }}</div>
                            <div class="pt-2"><strong>{{__('message.subtitle')}}</strong>: {{ $item->subtitle }}</div>
                        </div>
                        <div class="col text-right d-flex justify-content-end">
                            <a class="mr-2 jqueryvalidationLoadRemoteModel" href="{{ route('deliverypartner.edit', $item->id) }}" title="{{ __('message.update_form_title',['form' => __('message.deliverypartner') ]) }}"><i class="fas fa-edit text-primary"></i></a>
                            {{ html()->form('GET', route('website_section-delete', $item->id))->attribute('data--submit', 'deliverypartner'.$item->id)->open() }}
                                <a class="mr-2 text-danger" href="javascript:void(0)" data--submit="deliverypartner{{ $item->id}}" 
                                    data--confirmation='true' data-title="{{ __('message.delete_form_title',['form'=> __('message.deliverypartner') ]) }}"
                                    title="{{ __('message.delete_form_title',['form'=>  __('message.deliverypartner') ]) }}"
                                    data-message='{{ __("message.delete_msg") }}'>
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            {{ html()->form()->close() }}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
