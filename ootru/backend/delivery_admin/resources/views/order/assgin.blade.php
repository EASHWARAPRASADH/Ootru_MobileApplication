<?php $id = $id ?? null; ?>
{!! html()->form('POST', route('order.assign'))->id('assign_order_form')->open() !!}
{!! html()->hidden('type', 'courier_assigned') !!}
{!! html()->hidden('status', 'courier_assigned') !!}
{!! html()->hidden('id', $id) !!}
<div class="modal-dialog modal-dialog-centered  modal-xl" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{ __($pageTitle) }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="card-body">
                    <div class="card-body p-0">
                        <div class="table-responsive mt-4">
                            <table id="basic-table" class="table mb-0  text-center" role="grid">
                                <thead>
                                    <tr>
                                        <th scope='col'>{{ __('message.id') }}</th>
                                        <th scope='col'>{{ __('message.delivery_man') }}</th>
                                        <th scope='col'>{{ __('message.city_name') }}</th>
                                        <th scope='col'>{{ __('message.assign') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if( count($deliveryMen) > 0)
                                        @foreach ( $deliveryMen  as $value)
                                            <tr>
                                                <td>{{optional($value)->id ?? '-' }}</td>
                                                <td>{{optional($value)->name ?? '-' }}</td>
                                                <td>{{optional($value->city)->name ?? '-' }}</td>
                                                @if($order->delivery_man_id === null)
                                                <td><a class="btn btn-sm assign-btn btn-outline-primary" data-id="{{ optional($value)->id }}">{{__('message.assign_order')}}</a></td>
                                                @else
                                                <td><a class="btn btn-sm assign-btn btn-outline-primary" data-id="{{ optional($value)->id }}">{{__('message.order_transfer')}}</a></td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="9">{{ __('message.no_record_found') }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                                {!! html()->hidden('delivery_man_id')->id('delivery_man_id_input') !!}

                            </table>
                        </div>
                    </div>
                    {!! html()->form()->close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function($) {
        $(document).on('click', '.assign-btn', function() {
            var deliveryManId = $(this).data('id');
            $('#delivery_man_id_input').val(deliveryManId);
            $('#assign_order_form').submit();
        });

        $("#basic-table").DataTable({
            "dom":  '<"row align-items-center"<"col-md-2"><"col-md-6" B><"col-md-4"f>><"table-responsive my-3" rt><"d-flex" <"flex-grow-1" l><"p-2" i><"mt-4" p>><"clear">',
            "order": [[0, "desc"]]
        });
    });
</script>

