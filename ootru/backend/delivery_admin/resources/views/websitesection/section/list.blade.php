<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            <h5 class="font-weight-bold">{{ __('message.list_form_title',['form'=> $pageTitle ])}}</h5>
            <div class="table-responsive mt-4">
                @foreach ($sections as $section)
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h4 class="mb-1">{{ $section->title }}</h4>
                                    <p class="mb-2">{{ $section->subtitle }}</p>
                                </div>
                                <div class="d-flex align-items-center">
                                    <a href="{{ route('app-overview.edit', $section->id) }}" class="mr-2" data-toggle="tooltip" title="{{ __('message.update_form_title', ['form' => __('message.section')]) }}">
                                        <i class="fas fa-edit text-primary"></i>
                                    </a>
                                    {{ html()->form('DELETE', route('app-overview.destroy', $section->id))->attribute('data--submit', 'websection' . $section->id)->open() }}
                                        <a href="javascript:void(0)" class="text-danger" data--submit="websection{{ $section->id }}" data-toggle="tooltip"
                                            data--confirmation="true"
                                            data-title="{{ __('message.delete_form_title', ['form' => __('message.section')]) }}"
                                            title="{{ __('message.delete_form_title', ['form' => __('message.section')]) }}"
                                            data-message="{{ __('message.delete_msg') }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    {{ html()->form()->close() }}
                                </div>
                            </div>
                        
                            @if(getSingleMedia($section, 'section_image'))
                                <img src="{{ getSingleMedia($section, 'section_image') }}" class="bg-soft-primary rounded img-fluid avatar-80 me-3 mt-3" alt="{{ $section->title }}">
                            @endif
                        
                            <ul class="mt-3">
                                @foreach ($section->websitesectiontitles as $title)
                                    <li>{{ $title->title }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>