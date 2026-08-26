<x-master-layout :assets="$assets ?? []">
    <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="font-weight-bold ">{{__('message.claims_deatils')}}</h4>
                        </div>
                        <a  href="{{route('claims.index') }}" class="btn btn-primary btn-sm"> <i class="fa fa-angle-double-left"></i> {{__('message.back')}}</a>
                    </div>
                    <div class="card-body">
                        <div class="new-user-info">
                            <div class="card card-block mr-3">
                                <div class="header-title ml-3 mt-3">
                                    <h4 class="card-title">{{ __('message.submited_by')}}</h4>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12 ml-3 mt-3 mb-3">
                                        <span >{{ $data->user->name ?? ''}}</span>
                                    </div>
                                </div>
                            </div>  
                            <div class="card card-block mr-3">
                                <div class="header-title ml-3 mt-3">
                                    <h4 class="card-title">{{ __('message.prof_value')}}</h4>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12 ml-3 mt-3 mb-3">
                                        <span >{{$data->prof_value}}</span>
                                    </div>
                                </div>
                            </div>    
                            <div class="card card-block mr-3">
                                <div class="header-title ml-3 mt-3">
                                    <h4 class="card-title">{{ __('message.detail')}}</h4>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12 ml-3 mt-3 mb-3" style="text-wrap: balance;">
                                        <span>{{$data->detail}}</span>
                                    </div>
                                </div>
                            </div>    
                            <div class="card card-block mr-3">
                                <div class="header-title ml-3 mt-3">
                                    <h4 class="card-title">{{ __('message.attachment_file')}}</h4>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12 ml-3 mt-3 mb-3" style="text-wrap: balance;">
                                        @php
                                            $fileLinks = [];
                                            
                                            foreach ($mediaItems as $file) {
                                                $fileLinks[] = '<a href="' . $file->getUrl() . '"target="_blank">' . $file->file_name . '</a>';
                                            }
                                        @endphp
                                        <div class="bldefile">
                                            {!! implode('<br>', $fileLinks) !!}
                                        </div>
                                        
                                    </div>
                                </div>
                                
                            </div>    

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @section('bottom_script')
    @endsection
</x-master-layout>
