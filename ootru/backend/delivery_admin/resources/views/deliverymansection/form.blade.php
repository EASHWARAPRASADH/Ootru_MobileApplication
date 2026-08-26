<x-master-layout>
    <div>
        <?php $id = $id ?? null; ?>
        @if(isset($id))
            {{ html()->modelForm($data, 'PATCH', route('delivery-man-section.update', $id))->attribute('enctype', 'multipart/form-data')->id('section_form')->open() }}
        @else
            {{ html()->form('POST', route('delivery-man-section.store'))->attribute('enctype', 'multipart/form-data')->id('section_form')->open() }}
        @endif

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="card-title">{{ $pageTitle }}</h4>
                        <a href="{{ route('delivery-man-section.index') }}" class="btn btn-sm btn-primary">{{ __('message.back') }}</a>
                    </div>
                    <div class="card-body" id="section-container">
                        <div class="section-item">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    {{ html()->label(__('message.title') . ' <span class="text-danger">*</span>', 'title')->class('form-control-label') }}
                                    {{ html()->text('sections[0][title]', $data->title ?? '')->placeholder(__('message.title'))->class('form-control')->required() }}
                                </div>
                                {{-- <div class="form-group col-md-6">
                                    {{ html()->label(__('message.subtitle') . ' <span class="text-danger">*</span>', 'subtitle')->class('form-control-label') }}
                                    {{ html()->text('sections[0][subtitle]', $data->subtitle ?? '')->placeholder(__('message.subtitle'))->class('form-control')->required() }}
                                </div> --}}
                                @if(isset($id) && getMediaFileExit($data, 'delivery_man_section_image'))
                                    <div class="form-group col-md-5">
                                        {{ html()->label(__('message.image'), 'delivery_man_section_image')->class('form-control-label') }}
                                        <div class="custom-file">
                                            {{ html()->file('sections[0][delivery_man_section_image]')->class('custom-file-input')->accept('image/*') }}
                                            {{ html()->label(__('message.choose_file', ['file' => __('message.image')]))->class('custom-file-label') }}
                                        </div>
                                        <span class="selected_file"></span>
                                    </div>

                                    <div class="col-md-1 mb-2">
                                        <img id="delivery_man_section_image_preview" src="{{ getSingleMedia($data,'delivery_man_section_image') }}" alt="category-image" class="attachment-image mt-2">
                                        <a class="text-danger remove-file" href="{{ route('remove.file', ['id' => $data->id, 'type' => 'delivery_man_section_image']) }}"
                                        data--submit="confirm_form" data--confirmation="true" data--ajax="true" data-toggle="tooltip"
                                        title="{{ __('message.remove_file_title', ['name' => __('message.image')]) }}"
                                        data-title="{{ __('message.remove_file_title', ['name' => __('message.image')]) }}"
                                        data-message="{{ __('message.remove_file_msg') }}">
                                            <i class="ri-close-circle-line"></i>
                                        </a>
                                    </div>
                                @else
                                    <div class="form-group col-md-6">
                                        {{ html()->label(__('message.image'), 'delivery_man_section_image')->class('form-control-label') }}
                                        <div class="custom-file">
                                            {{ html()->file('sections[0][delivery_man_section_image]')->class('custom-file-input')->accept('image/*') }}
                                            {{ html()->label(__('message.choose_file', ['file' => __('message.image')]))->class('custom-file-label') }}
                                        </div>
                                        <span class="selected_file"></span>
                                    </div>
                                @endif

                                <div class="form-group col-md-12">
                                    <div class="d-flex justify-content-end align-items-center mb-2">
                                        <button type="button" class="btn btn-sm btn-secondary add-title">+ {{ __('message.add') }}</button>
                                    </div>

                                    <div class="titles-wrapper">
                                        @forelse ($titles as $title)
                                            <div class="title-group d-flex mb-2">
                                                <input type="text" name="sections[0][titles][]" value="{{ $title }}" class="form-control mr-2" />
                                                <button type="button" class="btn btn-danger btn-sm remove-title"><i class="ri-delete-bin-line"></i></button>
                                            </div>
                                        @empty
                                            <div class="title-group d-flex mb-2">
                                                <input type="text" name="sections[0][titles][]" placeholder="{{ __('message.title') }}" class="form-control mr-2" />
                                                <button type="button" class="btn btn-danger btn-sm remove-title"><i class="ri-delete-bin-line"></i></button>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            <hr>
                            {{ html()->submit(__('message.save'))->class('btn btn-md btn-primary float-right') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{ html()->form()->close() }}
    </div>

    @section('bottom_script')
        <script>
            $(document).on('click', '.add-title', function () {
                const titleText = "{{ __('message.title') }}";
                const titleHtml = `
                    <div class="title-group d-flex mb-2">
                        <input type="text" name="sections[0][titles][]" class="form-control mr-2" placeholder="${titleText}" />
                        <button type="button" class="btn btn-danger btn-sm remove-title"><i class="ri-delete-bin-line"></i></button>
                    </div>
                `;
                $(this).closest('.form-group').find('.titles-wrapper').append(titleHtml);
            });

            $(document).on('click', '.remove-title', function () {
                $(this).closest('.title-group').remove();
            });
        </script>
    @endsection
</x-master-layout>
