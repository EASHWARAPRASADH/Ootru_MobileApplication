<?php $id = $id ?? null; ?>

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
                                        <th scope='col'>{{ __('message.no') }}</th>
                                        <th scope='col'>{{ __('message.order_id') }}</th>
                                        <th scope='col'>{{ __('message.last_access_date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if( count($restApiHistory) > 0)
                                        @foreach ( $restApiHistory  as $value)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td><a href="{{ route('order.show', $value->order_id) }}">{{ optional($value)->order_id }}</a></td>
                                                <td>{{dateAgoFormate($value->last_access_date) ?? '-' }}</td>   
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
     $("#basic-table").DataTable({
            "dom":  '<"row align-items-center"<"col-md-2"><"col-md-6" B><"col-md-4"f>><"table-responsive my-3" rt><"d-flex" <"flex-grow-1" l><"p-2" i><"mt-4" p>><"clear">',
            "order": [[0, "desc"]]
        });
</script>

