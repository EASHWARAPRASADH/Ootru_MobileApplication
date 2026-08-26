<x-master-layout :assets="$assets ?? []">
    <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="font-weight-bold ">{{__('message.claims_deatils')}}</h4>
                        </div>
                        @if($data->status == 'pending')
                        <div class="col-auto offset-lg-9 d-flex">
                        {{-- <div>
                            <a class="mr-2" href="{{ route('approvedstatus',  ['id' => $data->id, 'status' => 1]) }}" title="{{ __('message.approve') }}">
                                <span class="badge badge-success mr-1"><i class="fas fa-check"></i></span>
                            </a>
                        </div>
                        <div>
                            <a class="mr-2" href="{{ route('approvedstatus',  ['id' => $data->id, 'status' => 0]) }}" title="{{ __('message.reject') }}">
                                <span class="badge badge-danger mr-1"><i class="fas fa-ban"></i></span>
                            </a>
                        </div> --}}
                        <a class="mr-2" href="javascript:void(0)" onclick="confirmAction('{{ route('approvedstatus', ['id' => $data->id, 'status' => 1]) }}', 'approve')" title="{{ __('message.approve') }}">
                            <span class="badge badge-success mr-1"><i class="fas fa-check"></i></span>
                        </a>
                        <a class="mr-2" href="javascript:void(0)" onclick="confirmAction('{{ route('approvedstatus', ['id' => $data->id, 'status' => 0]) }}', 'reject')" title="{{ __('message.reject') }}">
                            <span class="badge badge-danger mr-1"><i class="fas fa-ban"></i></span>
                        </a>  
                        </div>
                        @endif
                        <a  href="{{route('claims.index')}}" class="btn btn-primary btn-sm"> <i class="fa fa-angle-double-left"></i> {{__('message.back')}}</a>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @section('bottom_script')
    <script>
        function confirmAction(url, action) {
            const title = action === 'approve' ? 'Are you sure you want to approve this?' : 'Are you sure you want to reject this?';
            const confirmButtonText = action === 'approve' ? 'Yes, Approve' : 'Yes, Reject';
            const cancelButtonText = 'Cancel';

            Swal.fire({
                title: title,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: action === 'approve' ? '#28a745' : '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: confirmButtonText,
                cancelButtonText: cancelButtonText,
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to the URL if confirmed
                    window.location.href = url;
                }
            });
        }
        </script>
    @endsection
</x-master-layout>
