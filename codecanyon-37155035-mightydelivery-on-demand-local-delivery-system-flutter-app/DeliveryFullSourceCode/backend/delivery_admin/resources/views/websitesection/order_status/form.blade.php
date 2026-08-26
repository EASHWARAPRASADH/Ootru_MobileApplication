<x-master-layout :assets="$assets ?? []">
    <?php $id = $id ?? null;?>
    <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch">
                    <div class="card-body p-0">
                        <div class="d-flex justify-content-between align-items-center p-3">
                            <h5 class="font-weight-bold">{{  $pageTitle }}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                            {!! html()->form('POST', route('store.frontend.order.status.data', ['id' => isset($data) ? $data->id : null ]))->attribute('enctype', 'multipart/form-data')->attribute('data-toggle', 'validator')->open() !!}
                            {!! html()->hidden('type', 'order_status')->placeholder(__('message.order_status_section')) !!}
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    {!! html()->label(__('message.title'))->for('title')->class('form-control-label mb-1') !!}
                                    {!! html()->text('title', isset($data) ? $data->title : null)->placeholder(__('message.title'))->class('form-control') !!}
                                </div>
                            </div>
                            <hr>
                            <div class="col-md-12 mt-1 mb-4">
                                {!! html()->button(__('message.save'))->class('btn btn-md btn-primary float-md-end')->id('saveButton') !!}
                            </div>
                        {!! html()->form()->close() !!}
                    </div>
                </div>
            </div>
            <div class="col-lg-12" id="frontend_data_table">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center p-3">
                            <h5 class="font-weight-bold">{{ __('message.list_form_title', ['form' => __('message.order_status_section')]) }}</h5>
                            <a href="{{ route('website.section.order.status', ['type' => 'section','sub_type' => $type ,'frontend_id' => '']) }}" class="float-end btn btn-sm btn-primary loadRemoteModel order_status_sectionsection-btn"><i class="fa fa-plus-circle"></i> {{ __('message.add_form_title',['form' => __('message.order_status_section')  ]) }}</a>
                        </div>
                        <div class="table-responsive mt-4 assign-profile-max-height">
                            <table id="basic-table" class="table table-striped mb-0" role="grid">
                                <thead>
                                    <tr>
                                        <th>{{ __('message.image') }}</th>
                                        <th>{{ __('message.title') }}</th>
                                        <th>{{ __('message.description') }}</th>
                                        <th colspan="2">{{ __('message.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="order_status_section-data">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @section('bottom_script')
        <script>
            function getAssignList(type = ''){
                url = "{{ route('get.frontend.order.status.status') }}";
                $.ajax({
                    type: 'get',
                    url: url,
                    data: {
                        'type': "{{ $type }}",
                    },
                    success: function(res){
                        if (res.count_data >= 5 ) {                                                
                            $('.order_status_sectionsection-btn').hide();
                        }else{
                            $('.order_status_sectionsection-btn').show();
                        }
                        $('#'+type+'-data').html(res.data);
                    }
                });
            }
            $(document).ready(function () {
                getAssignList('{{$type}}');
            });
    </script>
    @endsection
</x-master-layout>
