<x-master-layout>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-block card-stretch card-height">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title mb-0">{{ $pageTitle ?? ''}}</h4>

                    </div>
                    @include('report.reportofdeliverymanfilter')
                </div>
                <div class="card-body">
                    @if (count($orders) > 0)
                        <table id="basic-table" class="table mb-1 border-1 text-center" role="grid" style="display: none;">
                            <thead>
                                <tr>
                                    <th scope='col'>{{ __('message.id') }}</th>
                                    <th scope='col'>{{ __('message.order_id') }}</th>
                                    <th scope='col'>{{ __('message.client') }}</th>
                                    <th scope='col'>{{ __('message.delivery_man') }}</th>
                                    <th scope='col'>{{ __('message.total_amount') }}</th>
                                    <th scope='col'>{{ __('message.pickup_date_time') }}</th>
                                    <th scope='col'>{{ __('message.delivery_date_time') }}</th>
                                    <th scope='col'>{{ __('message.commission_type') }}</th>
                                    <th scope='col' class="text-center">{{ __('message.admin_commission') }}</th>
                                    <th scope='col' class="text-center">{{ __('message.delivery_man_commission') }}</th>
                                    <th scope='col'>{{ __('message.created_at') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td><a href="{{ route('order.show', $order->id) }}">{{ $order->id}}</a></td>
                                        <td><a href="{{ route('users.show', $order) }}">{{ optional($order->client)->name ?? '-' }}</a></td>
                                        <td><a href="{{ route('users.show', $order) }}">{{ optional($order->delivery_man)->name ?? '-'}}</a></td>
                                        <td class="text-center">{{ getPriceFormat($order->total_amount) ?? 0 }}</td>
                                        <td> {{ dateAgoFormate($order->pickup_datetime) ?? '-' }}</td>
                                        <td>{{ dateAgoFormate($order->delivery_datetime) ?? '-' }}</td>
                                        @php
                                            $commission_type = optional($order)->city->commission_type ?? '-' ;
                                            $admin_commission = optional($order)->payment->admin_commission ?? 0 ;
                                            $deliveryman_commission = optional($order)->payment->delivery_man_commission ?? 0 ;
                                        @endphp
                                        <td class="text-center text-capitalize">{{ $commission_type }}</td>
                                        <td class="text-center text-capitalize">{{getPriceFormat($admin_commission) }}</td>
                                        <td class="text-center text-capitalize">{{ getPriceFormat($deliveryman_commission) }}</td>
                                        <td>{{ dateAgoFormate($order->created_at) ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="font-weight-bold">{{ __('message.total_amount') }}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-center font-weight-bold">{{ getPriceFormat($totalAmountorder) ?? '-' }}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-center font-weight-bold">{{ getPriceFormat($totalAdminSum) ?? '-' }}</td>
                                    <td class="text-center font-weight-bold">{{ getPriceFormat($totaldeliverymanSum) ?? '-' }}</td>
                                    <td></td>

                                </tr>
                            </tfoot>
                        </table>
                    @else
                        <div class ="text-center">{{ __('message.no_record_found') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@section('bottom_script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deliveryman = "{{ request('delivery_man_id') }}";
        const table = document.getElementById('basic-table');

        if (deliveryman) {
            table.style.display = 'table';
            $("#basic-table").DataTable({
                "dom": '<"row align-items-center"<"col-md-2"><"col-md-6" B><"col-md-4" f>><"table-responsive my-3" rt><"d-flex"<"flex-grow-1" l><"" i><"" p>><"clear">',
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
        }

        document.querySelector('.clearListPropertynumber').addEventListener('click', function() {
            document.querySelector('input[name="delivery_man_id"]').value = null;
            document.getElementById('filter-form').submit();
        });
    });
</script>

@endsection
</x-master-layout>
