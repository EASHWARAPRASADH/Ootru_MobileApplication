<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3">
                            <h5 class="font-weight-bold">{{ $pageTitle ?? __('message.list') }}</h5>
                            @if($count != 4)
                            <a href="{{ route('walkthrough.create') }}"class="float-right btn btn-sm btn-primary jqueryvalidationLoadRemoteModel"  style="margin-left: 1094px;">{{ __('message.add_form_title', ['form' => __('message.walkthrough')]) }}</a>
                            <a href="{{ route('help-walkthrough') }}" class="float-right btn btn-xs loadRemoteModel mr-3 help pt-1 pb-1 mt-2" role="button">{{ __('message.help') }}</a>
                        @endif
                        </div>
                    </div>
                </div>
            </div>
            @if(!empty($data) && isset($data[0]->type))
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row col-md-12">
                            <div class="col-lg-12">
                                @foreach($data as $item)
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <img src="{{ getSingleMedia($item, 'frontend_data_image') }}" class="avatar-100 img-fluid rounded">
                                                </div>
                                                <div class="col-sm-10">
                                                    <div class="pt-2"><strong>{{__('message.title')}}</strong>: {{ $item->title }}</div>
                                                    <div class="pt-2"><strong>{{__('message.subtitle')}}</strong>: {{ $item->description }}</div>
                                                </div>
                                                    <div class="col text-right d-flex justify-content-end">
                                                    <a class="mr-2 jqueryvalidationLoadRemoteModel" href="{{ route('walkthrough.edit', $item->id) }}" title="{{ __('message.update_form_title',['form' => __('message.walkthrough_section') ]) }}"><i class="fas fa-edit text-primary"></i></a>
                                                    {{ html()->form('GET', route('website_section-delete', $item->id))->attribute('data--submit', 'walkthrough'.$item->id)->open() }}
                                                        <a class="mr-2 text-danger" href="javascript:void(0)" data--submit="walkthrough{{ $item->id}}" data-id="{{ $item->id }}"
                                                            data--confirmation='true' data-title="{{ __('message.delete_form_title',['form'=> __('message.walkthrough_section') ]) }}"
                                                            title="{{ __('message.delete_form_title',['form'=>  __('message.walkthrough_section') ]) }}"
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
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @section('bottom_script')
    @endsection
</x-master-layout>
