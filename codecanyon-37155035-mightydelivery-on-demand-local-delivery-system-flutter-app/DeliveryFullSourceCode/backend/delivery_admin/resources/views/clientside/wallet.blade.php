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
                                        <h4>{{getPriceFormat($withdrawn->total_amount ?? 0)}}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <table id="basic-table" class="table mb-3  text-center">
                                    <thead>
                                        <tr>
                                            <th scope='col'>{{ __('message.order_id') }}</th>
                                            <th scope='col'>{{ __('message.transaction_type') }}</th>
                                            <th scope='col'>{{ __('message.amount') }}</th>
                                            <th scope='col'>{{ __('message.created_at') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($wallet->count() > 0)
                                            @foreach($wallet as $value)
                                                <tr>
                                                    <td>{{ optional($value)->order_id ?? '-' }}</td>
                                                    <td>{{ optional($value)->transaction_type ?? '-' }}</td>
                                                    <td style="color: {{ optional($value)->type == 'credit' ? 'green' : 'red' }}">
                                                        {{ getPriceFormat($value->amount) ?? 0 }}
                                                    </td>
                                                    <td>{{ dateAgoFormate($value->created_at) ?? '-' }}</td>
                                                </tr>
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
                "order": [[0, "desc"]]
            });
        </script>
    @endsection
</x-master-layout>
