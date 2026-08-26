<?php

namespace App\DataTables;

use App\Models\Order;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;

class OrderPrintDataTable extends DataTable
{
    use DataTableTrait;
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class="select-table-row-checked-values" id="datatable-row-' . $row->id . '" name="datatable_ids[]" value="' . $row->id . '">';
            })
            ->editColumn('status', function ($query) {
                $status = 'danger';
                $status_name = 'cancelled';
                switch ($query->status) {
                    case 'draft':
                        $status = 'light';
                        $status_name = __('message.draft');
                        break;
                    case 'create':
                        $status = 'primary';
                        $status_name = __('message.create');
                        break;
                    case 'completed':
                        $status = 'success';
                        $status_name = __('message.delivered');
                        break;
                    case 'courier_assigned':
                        $status = 'warning';
                        $status_name = __('message.assigned');
                        break;
                    case 'active':
                        $status = 'info';
                        $status_name = __('message.active');
                        break;
                    case 'courier_departed':
                        $status = 'info';
                        $status_name = __('message.departed');
                        break;
                    case 'courier_picked_up':
                        $status = 'warning';
                        $status_name = __('message.picked_up');
                        break;
                    case 'courier_arrived':
                        $status = 'warning';
                        $status_name = __('message.arrived');
                        break;
                    case 'cancelled':
                        $status = 'danger';
                        $status_name = __('message.cancelled');
                        break;
                }
                return '<span class="text-capitalize badge bg-' . $status . '">' . $status_name . '</span>';
            })

            ->editColumn('created_at', function ($query) {
                return dateAgoFormate($query->created_at, true);
            })

            ->editColumn('pickup_point',function($query){
                $address = isset($query->pickup_point['address']) ? $query->pickup_point['address'] : '';
                if($address && $query->deleted_at !== null){
                    return '<span style="color:  #000000" data-toggle="tooltip" title="'.$address.'">'.stringLong($address, 'title',20).'</span>';
                }else{
                    return isset($address) ? '<span data-toggle="tooltip" title="'.$address.'">'.stringLong($address, 'title',20).'</span>' : '-';
                }
            })

            ->editColumn('delivery_point',function($query){
                $address = isset($query->delivery_point['address']) ? $query->delivery_point['address'] : '';
                if($address && $query->deleted_at !== null){
                    return '<span style="color:  #000000" data-toggle="tooltip" title="'.$address.'">'.stringLong($address, 'title',20).'</span>';
                }else{
                    return isset($address) ? '<span data-toggle="tooltip" title="'.$address.'">'.stringLong($address, 'title',20).'</span>' : '-';
                }
            })

            ->addColumn('label', function($row) {
                return $row ? '<a href="' . route('labelprint', $row->id) . '"><i class="fa-solid fa-print"></i></a>' : '-';
            })
            ->addColumn('print_qrcode', function($row) {
                return $row ? '<a href="' . route('printbarcode', $row->id) . '"><i class="fa-solid fa-print"></i></a>' : '-';
            })

            ->addColumn('invoice', function ($query) {
                return $query->status == 'completed' ? '<a href="' . route('order-invoice', $query->id) . '"><i class="fa fa-download"></i></a>' : 'N/A';
            })
            ->editColumn('delivery_man_id', function ($query) {
                $deliveryMan = $query->delivery_man;
                return $deliveryMan ? '<a href="' . route('deliveryman.show', $deliveryMan->id) . '">' . $deliveryMan->name . '</a>' : '-' ;
            })

            ->filterColumn('delivery_man_id', function($query, $keyword) {
                return $query->orWhereHas('delivery_man', function($q) use($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })

            ->editColumn('milisecond', function($row) {
                $milisecond = $row->milisecond;
                return $milisecond ?? '-';
            })
            ->editColumn('delivery_datetime', function ($row) {
                return dateAgoFormate($row->delivery_datetime, true) ?? '-';
            })
            ->editColumn('pickup_datetime', function ($row) {
                return dateAgoFormate($row->pickup_datetime, true) ?? '-';
            })
            ->editColumn('assign_datetime', function ($row) {
                return dateAgoFormate($row->assign_datetime, true) ?? '-';
            })

            ->order(function ($query) {
                if (request()->has('order')) {
                    $order = request()->order[0];
                    $column_index = $order['column'];

                    $column_name = 'id';
                    $direction = 'desc';
                    if ($column_index != 0) {
                        $column_name = request()->columns[$column_index]['data'];
                        $direction = $order['dir'];
                    }

                    $query->orderBy($column_name, $direction);
                }
            })
            ->editColumn('id', function ($row) {
                $user = $row->id;
                return $user ? '<a href="' . route('order.show', $user) . '">' . $user . '</a>' : '-' ;
            })
            ->editColumn('client_id', function ($row) {
                $user = $row->client;
                return $user ? '<a href="' . route('users.show', $user->id) . '">' . $user->name . '</a>' : '-' ;
            })
            ->filterColumn('client_id', function($query, $keyword) {
                return $query->orWhereHas('client', function($q) use($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })

            ->addIndexColumn()
            ->rawColumns(['status','label','checkbox', 'pickup_point', 'delivery_point', 'invoice', 'delivery_man_id', 'client_id','id','print_qrcode']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Order $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Order $model)
    {
        $model = Order::whereIn('status', [ 'courier_departed', 'courier_picked_up', 'courier_assigned','courier_arrived','active','create']);
        return $model->newQuery();
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        $prefix = strtoupper(appSettingcurrency('prefix'));
        return [
            Column::make('checkbox')
                ->searchable(false)
                ->title('<input type="checkbox" class ="select-all-table" name="select_all" id="select-all-table">')
                ->orderable(false)
                ->width(20),
            ['data' => 'milisecond', 'name' => 'milisecond',  'title' => $prefix .' #' ?? __('message.document_at')],
            ['data' => 'id', 'name' => 'id', 'title' => __('message.order_id')],
            ['data' => 'client_id', 'name' => 'client_id', 'title' => __('message.customer_name')],
            ['data' => 'pickup_point', 'name' => 'pickup_point', 'title' => __('message.pickup_address')],
            ['data' => 'delivery_point', 'name' => 'delivery_point', 'title' => __('message.delivery_address')],
            ['data' => 'delivery_man_id', 'name' => 'delivery_man_id', 'title' => __('message.delivery_man')],
            ['data' => 'pickup_datetime', 'name' => 'pickup_datetime', 'title' => __('message.pickup_date')],
            ['data' => 'delivery_datetime', 'name' => 'delivery_datetime', 'title' => __('message.delivery_date')],
            ['data' => 'print_qrcode', 'name' => 'print_qrcode', 'title' => __('message.print_barcode')],
            ['data' => 'label', 'name' => 'label', 'title' => __('message.label')],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => __('message.created_at')],
            ['data' => 'status', 'name' => 'status', 'title' => __('message.status')],
        ];
    }
}
