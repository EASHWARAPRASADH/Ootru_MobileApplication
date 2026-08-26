<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title mb-0">{{ $pageTitle ?? ''}}</h4>
                        </div>
                        @include('report.adminfilter')
                    </div>
                    <div class="card-body">
                        <div class="card-header-toolbar">

                        </div>
                            <table id="basic-table" class="table mb-1 border-1  text-center" role="grid">
                                <thead>
                                    <tr>
                                        <th scope='col'>{{ __('message.id') }}</th>
                                        <th scope='col'>{{ __('message.order_id') }}</th>
                                        <th scope='col'>{{ __('message.user') }}</th>
                                        <th scope='col'>{{ __('message.delivery_man') }}</th>
                                        <th scope='col'>{{ __('message.pickup_date_time') }}</th>
                                        <th scope='col'>{{ __('message.delivery_date_time') }}</th>
                                        <th scope='col'>{{ __('message.total_amount') }}</th>
                                        <th scope='col'>{{ __('message.commission_type') }}</th>
                                        <th scope='col' class="text-center">{{ __('message.admin_commission') }}</th>
                                        <th scope='col' class="text-center">{{ __('message.delivery_man_commission') }}</th>
                                        <th scope='col'>{{ __('message.created_at') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($orders) > 0)
                                    @foreach ($orders as $result)

                                            <td>{{ $loop->iteration }}</td>
                                            <td><a href="{{ route('order.show', $result) }}">{{ $result->id}}</a></td>
                                            <td><a href="{{ route('users.show', $result->client_id) }}">{{ optional($result->client)->name ?? '-' }}</a></td>
                                            
                                            <td><a href="{{ route('deliveryman-view.show', $result->delivery_man_id)}}">{{ optional($result->delivery_man)->name ?? '-'}}</a></td>
                                            <td> {{ dateAgoFormate($result->pickup_datetime) ?? '-' }}</td>
                                            <td>{{ dateAgoFormate($result->delivery_datetime) ?? '-' }}</td>
                                            <td class="text-center">{{ getPriceFormat($result->total_amount) ?? 0 }}</td>
                                            @php
                                                $commission_type = optional($result)->city->commission_type ?? '-' ;
                                                $admin_commission = optional($result)->payment->admin_commission ?? 0 ;
                                                $deliveryman_commission = optional($result)->payment->delivery_man_commission ?? 0 ;
                                                // $formatted_commission = $commission_type == 'percentage' ? getPriceFormat($admin_commission) . ' %' : ($commission_type == 'fixed' ? getPriceFormat($admin_commission) . ' fixed' : '-');
                                            @endphp

                                            <td class="text-center text-capitalize">{{ $commission_type }}</td>
                                            <td class="text-center text-capitalize">{{getPriceFormat($admin_commission) }}</td>
                                            <td class="text-center text-capitalize">{{ getPriceFormat($deliveryman_commission) }}</td>
                                            <td>{{ dateAgoFormate($result->created_at) ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                    @else
                                        <tr>
                                            <td colspan="12">{{ __('message.no_record_found') }}</td>
                                        </tr>
                                    @endif

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td class="font-weight-bold">{{ __('message.total_amount') }}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-center font-weight-bold">{{ getPriceFormat($totalAmountorder) }}</td>
                                        <td></td>
                                        <td class="text-center font-weight-bold">{{ getPriceFormat($totalAdminSum) }}</td>
                                        <td class="text-center font-weight-bold">{{ getPriceFormat($totaldeliverymanAmountSum) }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>   
                    </div>
                </div>
            </div>
        </div>
    </div>
    @section('bottom_script')
        <script>
            $("#basic-table").DataTable({
                "dom":  '<"row align-items-center"<"col-md-2"><"col-md-6" B><"col-md-4"f>><"table-responsive my-3" rt><"d-flex" <"flex-grow-1" l><"p-2" i><"mt-4" p>><"clear">',
                language: {
                    search: '',
                    searchPlaceholder: "{{ __('pagination.search') }}",
                    lengthMenu : "{{  __('pagination.show'). ' _MENU_ ' .__('pagination.entries')}}",
                    zeroRecords: "{{__('pagination.no_records_found')}}",
                    info: "{{__('pagination.showing') .' _START_ '.__('pagination.to') .' _END_ ' . __('pagination.of').' _TOTAL_ ' . __('pagination.entries')}}", 
                    infoFiltered: "{{__('pagination.filtered_from_total') . ' _MAX_ ' . __('pagination.entries')}}",
                    infoEmpty: "{{__('pagination.showing_entries')}}",
                    paginate: {
                        previous: "{{__('pagination.__previous')}}",
                        next: "{{__('pagination.__next')}}"
                    }
                 },
                "order": [[0, "desc"]]
            });

        </script>
    @endsection
    </x-master-layout>

