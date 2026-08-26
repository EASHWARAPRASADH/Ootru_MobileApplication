<?php

namespace App\DataTables;

use App\Models\WalletHistory;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;

class WalletHistoryDataTable extends DataTable
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
            ->editColumn('amount', function ($amount) {
                $amount = getPriceFormat($amount->amount);
                return $amount;
            })
            ->editColumn('order_id', function ($row) {
                return $row->order_id ?? '-';
            })
            ->editColumn('transaction_type', function ($row) {
                return $row->transaction_type ?? '-';
            })
            ->addIndexColumn()
            ->rawColumns([]);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\WalletHistory $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(WalletHistory $model)
    {
        $model = WalletHistory::query();
        return $model->newQuery();
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('DT_RowIndex')
                ->searchable(false)
                ->title(__('message.srno'))
                ->orderable(false),
            ['data' => 'order_id', 'name' => 'order_id', 'title' => __('message.order_id')],
            ['data' => 'transaction_type', 'name' => 'transaction_type', 'title' => __('message.transaction_type')],
            ['data' => 'amount', 'name' => 'amount', 'title' => __('message.amount')],
            ['data' => 'datetime', 'name' => 'datetime', 'title' => __('message.date')],
        ];
    }
}
