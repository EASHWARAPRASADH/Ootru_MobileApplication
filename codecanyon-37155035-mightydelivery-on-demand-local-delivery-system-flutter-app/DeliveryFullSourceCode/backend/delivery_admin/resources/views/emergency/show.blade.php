<x-master-layout :assets="$assets ?? []">
    <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="text-uppercase font-weight-bold mb-0">
                                {{ optional($emergency->deliveryMan)->name . ' ' . __('message.details') }}
                            </h4>
                        </div>
                        <a  href="{{route('emergency.index')}}" class="btn btn-primary btn-sm"> <i class="fa fa-angle-double-left"></i> {{__('message.back')}}</a>
                    </div>
                    <div class="card-body">
                        <div class="new-user-info">
                            <div class="row">
                                <div class="form-group col-md-3">
                                    {{ html()->label(__('message.reported_at'))->class('form-control-label text-secondary') }}
                                    <h4>{{ dateAgoFormate($emergency->created_at) }}</h4>
                                </div>
                                <div class="form-group col-md-3">
                                    {{ html()->label(__('message.email'))->class('form-control-label text-secondary') }}
                                    <h4>{{ $emergency->deliveryMan?->email ?? 'N/A' }}</h4>
                                </div>
                                <div class="form-group col-md-3">
                                    {{ html()->label(__('message.contact_number'))->class('form-control-label text-secondary') }}
                                    <h4>{{ $emergency->deliveryMan?->contact_number ?? 'N/A' }}</h4>
                                </div>
                                <div class="form-group col-md-3">
                                    {{ html()->label(__('message.updated_at'))->class('form-control-label text-secondary') }}
                                    <h4>{{ dateAgoFormate($emergency->updated_at) }}</h4>
                                </div>
                            </div>
                            <ul class="list-group mb-4">
                                <li class="list-group-item">
                                    <strong>{{ __('message.reason') }}:</strong> {{ $emergency->emrgency_reason }}
                                </li>
                                <li class="list-group-item">
                                    <strong>Status:</strong> 
                                    @php
                                        $statusText = [
                                            0 => ['label' => 'Pending', 'class' => 'bg-warning'],
                                            1 => ['label' => 'In Progress', 'class' => 'bg-info'],
                                            2 => ['label' => 'Closed', 'class' => 'bg-success'],
                                        ];
                                    @endphp
                                    <span class="badge {{ $statusText[$emergency->status]['class'] ?? 'bg-secondary' }}">
                                        {{ $statusText[$emergency->status]['label'] ?? 'Unknown' }}
                                    </span>
                                </li>
                            </ul>
                            @if($emergency->status !=  2)
                                {{ html()->form('PUT', route('emergency.update', $emergency->id))->open() }}
                                    <div class="form-group">
                                        {{ html()->label(__('message.admin_note') . ' <span class="text-danger">*</span>', 'admin_note')->class('form-control-label') }}
                                        {{ html()->textarea('emergency_resolved', old('emergency_resolved'))->class('form-control textaraea')->rows(2)->placeholder(__('message.admin_note'))->required() }}
                                    </div>  
                                    {{ html()->submit(__('message.mark_resolved'))->class('btn btn-md btn-primary') }}
                                {{ html()->form()->close() }}
                            @else
                                <div class="alert alert-success">
                                    <strong>{{__('message.resolved_note')}}</strong><br>
                                    {{ $emergency->emergency_resolved }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{ html()->form()->close() }}
    </div>
    @section('bottom_script')
    @endsection
</x-master-layout>
