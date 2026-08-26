<x-master-layout :assets="$assets ?? []">
    <div>
        <?php $id = $id ?? null;?>
        @if(isset($id))
            {!! html()->modelForm($pages, 'PATCH', route('pages.update', $id))->id('page_form')->open() !!}
        @else
            {!! html()->form('POST', route('pages.store'))->id('page_form')->open() !!}
        @endif
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{ $pageTitle }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="new-user-info">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    {!! html()->label(__('message.title') . ' <span class="text-danger">*</span>')->class('form-control-label') !!}
                                    {!! html()->text('title', old('title'))->placeholder(__('message.title'))->class('form-control') !!}
                                </div>
                                <div class="form-group col-md-12">
                                    {!! html()->label(__('message.description') . ' <span class="text-danger">*</span>')->for('description')->class('form-control-label')!!}
                                    {!! html()->textarea('description', old('description'))->class('form-control tinymce-description')->placeholder(__('message.description')) !!}
                                </div>
                                @if ($errors->has('description'))
                                 <span class="text-danger">{{ $errors->first('description') }}</span>
                                @endif
                            </div>
                            <hr>
                            {!! html()->submit(isset($id) ? __('message.update') : __('message.save'))->class('btn btn-md btn-primary float-right') !!}  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {!! html()->form()->close() !!}
    </div>
    @section('bottom_script')
      <script>
        (function($) {
            $(document).ready(function(){
                tinymceEditor('.tinymce-description',' ',function (ed) {

                }, 450)
                formValidation("#page_form", {
                    title: { required: true },
                    description: { required: true },
                }, {
                    title: { required: "{{__('message.please_enter_title')}}" },
                    description: { required: "{{__('message.please_select_country')}}" },
                });
            });

        })(jQuery);
      </script>
    @endsection
</x-master-layout>
