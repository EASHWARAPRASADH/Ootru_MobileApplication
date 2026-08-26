<x-master-layout>
    <div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{ $pageTitle }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="new-user-info pl-2 pr-2">
                            <div class="row">
                                <div class="col-md-12 border py-3 bg-indigo pr-3">
                                  <h4>{{__('message.available_balnce')}}</h4>
                                    <div class="col-pt-5">
                                        <br>
                                        <h4>{{getPriceFormat($wallte->total_amount ?? 0)}}</h4>
                                            <a href="{{ route('withdrawrequest.create') }}"class="pr-4 pl-4 pt-3 pb-3 float-right btn btn-sm btn btn-success text-center loadRemoteModel"><h6>{{ __('message.withdraw') }}</h6></a>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <table id="basic-table" class="table mb-3  text-center">
                                    <thead>
                                        <tr>
                                            <th scope='col'>{{ __('message.no') }}</th>
                                            <th scope='col'>{{ __('message.name') }}</th>
                                            <th scope='col'>{{ __('message.amount') }}</th>
                                            <th scope='col'>{{ __('message.available_balnce') }}</th>
                                            <th scope='col'>{{ __('message.created_at') }}</th>
                                            <th scope='col'>{{ __('message.status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($withdrawrequest->count() > 0)
                                            @php
                                                $counter = 1;
                                            @endphp
                                            @foreach($withdrawrequest as $value)
                                                <tr>
                                                    <td>{{ $counter }}</td>
                                                    <td>{{ optional($value->user)->name ?? '-' }}</td>
                                                        @php
                                                            $status = optional($value)->status;
                                                            $style = '';

                                                            if ($status == 'decline') {
                                                                $style = 'color: red;';
                                                            } elseif ($status == 'approved') {
                                                                $style = 'color: green;';
                                                            }
                                                        @endphp
                                                    <td style="{{ $style }}">
                                                        {{ getPriceFormat($value->amount) ?? 0 }}
                                                    </td>

                                                    <td>
                                                    {{ $value->status == 'requested' ? ($wallte ? getPriceFormat($wallte->total_amount) : 0) : '-' }}
                                                    </td>
                                                    <td>{{ dateAgoFormate($value->created_at) ?? '-' }}</td>
                                                    <td>
                                                        @php
                                                            $status = 'danger';
                                                            $status_name = 'requested';
                                                            switch ($value->status) {
                                                                case 'requested':
                                                                    $status = 'indigo';
                                                                    $status_name = __('message.requested');
                                                                    break;
                                                                case 'decline':
                                                                    $status = 'danger';
                                                                    $status_name = __('message.declined');
                                                                    break;
                                                                case 'approved':
                                                                    $status = 'success';
                                                                    $status_name = __('message.approved');
                                                                    break;
                                                            }

                                                        @endphp
                                                        <span class="text-capitalize badge bg-{{$status}}">{{$status_name}} </span>
                                                    </td>
                                                </tr>
                                                @php
                                                    $counter++;
                                                @endphp
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="9">{{ __('message.no_record_found') }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @section('bottom_script')
        <script>
            $("#basic-table").DataTable({
             "dom":  '<"row align-items-center"<"col-md-2"><"col-md-6" B><"col-md-4"f>><"table-responsive my-3" rt><"d-flex" <"flex-grow-1" l><"p-2" i><"mt-4" p>><"clear">',
            });
        </script>
    @endsection
</x-master-layout>
