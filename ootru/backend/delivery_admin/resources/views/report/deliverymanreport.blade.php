<x-master-layout>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-block card-stretch card-height">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title mb-0">{{ $pageTitle }}</h4>
                    </div>
                    @include('report.deliverymandatefilter')
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
                            @foreach ($orders as $order)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td> <a href="{{ route('order.show', $order) }}">{{ $order->id }}</a></td>
                                    <td><a href="{{ route('users.show', $order->client_id) }}">{{ optional($order->client)->name ?? '-' }}</a></td>
                                    <td><a href="{{ route('deliveryman-view.show', $order->delivery_man_id) }}">{{ optional($order->delivery_man)->name ?? '-'}}</a></td>
                                    <td> {{ dateAgoFormate($order->pickup_datetime) ?? '-' }}</td>
                                    <td>{{ dateAgoFormate($order->delivery_datetime) ?? '-' }}</td>
                                    <td class="text-center">{{ getPriceFormat($order->total_amount) ?? 0 }}</td>
                                    @php
                                        $commission_type = optional($order)->city->commission_type ?? '-' ;
                                        $deliveryman_commission = optional($order)->payment->delivery_man_commission ?? 0 ;
                                        $admin_commission = optional($order)->payment->admin_commission ?? 0 ;
                                        // $formatted_commission = $commission_type == 'percentage' ? getPriceFormat($deliveryman_commission) . ' %' : ($commission_type == 'fixed' ? getPriceFormat($deliveryman_commission) . ' fixed' : '-');
                                    @endphp
                                    <td class="text-center text-capitalize">{{ $commission_type }}</td>
                                    <td class="text-center">{{getPriceFormat($admin_commission) }}</td>
                                    <td class="text-center ">{{ getPriceFormat($deliveryman_commission) }}</td>
                                    <td>{{ dateAgoFormate($order->created_at) ?? '-' }}</td>
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
                                <td class="text-center font-weight-bold">{{ getPriceFormat($totalAmountorder) ?? '-' }}</td>
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
            "order": [[0, "desc"]]
        });
    </script>
@endsection
</x-master-layout>
