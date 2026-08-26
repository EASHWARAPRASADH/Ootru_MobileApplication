<?php

namespace App\DataTables;

use App\Models\Payment;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;

class PaymentDataTable extends DataTable
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
            ->editColumn('payment_status', function ($query) {
                $status = 'primary';
                $status_name = 'paid';
                switch ($query->status) {
                    case 'paid':
                        $status = 'success';
                        $status_name = __('message.paid');
                        break;

                }
                return '<span class="text-capitalize badge bg-' . $status . '">' . $status_name . '</span>';
            })

            ->editColumn('created_at', function ($query) {
                return dateAgoFormate($query->created_at, true);
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
            ->editColumn('order_id', function ($row) {
                $order = $row->order_id;
                return $order ? '<a href="' . route('order.show', $order) . '">' . $order . '</a>' : '-' ;
            })

            ->editColumn('received_by',function($row){
                $received_by = str_replace('_', ' ', ucfirst($row->received_by));
                return $received_by ?? '-';
            })

            ->addColumn('delivery_man', function($row) {
                if ($row->received_by == 'delivery_man') {
                    return '<a href="' . route('deliveryman-view.show', $row->order->delivery_man_id) . '">' . optional($row->order->delivery_man)->name . '</a>';
                }
                return '-';
            })
            
            ->editColumn('total_amount', function ($row) {
                $total_amount = optional($row)->total_amount;

                return $total_amount ? getPriceFormat($total_amount) : '-';
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

            ->addIndexColumn()
            ->rawColumns(['status','payment_status','client_id','order_id','total_amount','delivery_man']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Payment $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Payment $model)
    {
        $query = $model->newQuery();
        $payment_type = isset($_GET['payment_type']) ? $_GET['payment_type'] : null;

        switch ($payment_type) {
            case 'cash':
                $query = $query->where('payment_type', 'cash');
                break;
            case 'online':
                $query = $query->whereNotIn('payment_type', ['cash','wallet']);
                break;
            case 'wallet':
                $query = $query->where('payment_type', 'wallet');
                break;

            default:
                break;
        }

        return $query;
    }


    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {

        $orders_type = isset($_GET['payment_type']) ? $_GET['payment_type'] : null;
        switch ($orders_type) {
            case 'cash':
                return [
                    Column::make('DT_RowIndex')
                    ->searchable(false)
                    ->title(__('message.srno'))
                    ->addClass('text-capitalize')
                    ->orderable(false),
                        ['data' => 'client_id', 'name' => 'client_id', 'title' => __('message.name')],
                        ['data' => 'order_id', 'name' => 'order_id', 'title' => __('message.order_id')],
                        ['data' => 'total_amount', 'name' => 'total_amount', 'title' => __('message.amount')],
                        ['data' => 'delivery_man', 'name' => 'delivery_man', 'title' => __('message.received_by')],
                        ['data' => 'created_at', 'name' => 'created_at', 'title' => __('message.created_at')],
                        ['data' => 'payment_status', 'name' => 'payment_status', 'title' => __('message.status')],
                ];
                break;
            case 'wallet':
                return [
                    Column::make('DT_RowIndex')
                    ->searchable(false)
                    ->title(__('message.srno'))
                    ->addClass('text-capitalize')
                    ->orderable(false),
                        ['data' => 'client_id', 'name' => 'client_id', 'title' => __('message.name')],
                        ['data' => 'order_id', 'name' => 'order_id', 'title' => __('message.order_id')],
                        ['data' => 'total_amount', 'name' => 'total_amount', 'title' => __('message.amount')],
                        ['data' => 'created_at', 'name' => 'created_at', 'title' => __('message.created_at')],
                        ['data' => 'payment_status', 'name' => 'payment_status', 'title' => __('message.status')],
                ];
                break;
            case 'online':
                return [
                    Column::make('DT_RowIndex')
                    ->searchable(false)
                    ->title(__('message.srno'))
                    ->addClass('text-capitalize')
                    ->orderable(false),
                        ['data' => 'client_id', 'name' => 'client_id', 'title' => __('message.name')],
                        ['data' => 'order_id', 'name' => 'order_id', 'title' => __('message.order_id')],
                        ['data' => 'total_amount', 'name' => 'total_amount', 'title' => __('message.amount')],
                        ['data' => 'payment_type', 'name' => 'payment_type', 'title' => __('message.payment_type')],
                        ['data' => 'txn_id', 'name' => 'txn_id', 'title' => __('message.transaction_id')],
                        ['data' => 'created_at', 'name' => 'created_at', 'title' => __('message.created_at')],
                        ['data' => 'payment_status', 'name' => 'payment_status', 'title' => __('message.status')],
                ];
                break;

            default:
                break;
        }
    }
}
