<?php

namespace App\DataTables;

use App\Models\Order;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;
use Carbon\Carbon;

class ShippedOrderDataTable extends DataTable
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
                return '<input type="checkbox" class=" select-table-row-checked-values" id="datatable-row-' . $row->id . '" name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">';
            })
            ->editColumn('created_at', function ($query) {
                return dateAgoFormate($query->created_at, true);
            })


            ->addColumn('invoice', function ($query) {
                return $query ? '<a href="' . route('order-invoice', $query->id) . '"><i class="fa fa-download"></i></a>' : 'N/A';
            })

            ->editColumn('milisecond', function($row) {
                $set_ids = $row->milisecond ?? '-';
                return $set_ids;
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
            ->addColumn('name', function ($row) {
                $user = optional($row->couriercompany)->name;
                return $user ?? '-' ;
            })
            ->addColumn('link', function ($row) {
                $courierLink = $row->courierCompany->link ?? '-';
                return $courierLink ? '<a href="' . $courierLink . '" target="_blank">' . $courierLink . '</a>' : '-';
            })
            ->addColumn('action', function ($order) {
                $id = $order->id;
                $delete_at = $order->deleted_at;
                return view('order.action', compact('id', 'delete_at', 'order'))->render();
            })

            ->addIndexColumn()
            ->rawColumns(['action', 'status', 'checkbox', 'pickup_point', 'delivery_point', 'invoice', 'delivery_man_id', 'client_id','id','assign','parent_order_id','link']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Order $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Order $model)
    {
        $auth = auth()->user();
        $query = $model->newQuery()->where('status','shipped');

        $filter_status = request()->input('status');
        $filter_from_date = request()->input('from_date');
        $filter_to_date = request()->input('to_date');
        $filter_city = request()->input('city_id');
        $filter_country = request()->input('country_id');

        if ($filter_status) {
            $query->where('status', $filter_status);
        }
        if ($filter_city) {
            $query->where('city_id', $filter_city);
        }
        if ($filter_country) {
            $query->where('country_id', $filter_country);
        }

        if ($filter_from_date && $filter_to_date) {
            $query->whereBetween('created_at', [$filter_from_date, $filter_to_date]);
        }

        if ($auth->user_type == 'client') {
            $query->where('client_id', $auth->id);
        }

        return $query->withTrashed();
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
            ['data' => 'name', 'name' => 'name', 'title' => __('message.company_name')],
            ['data' => 'link', 'name' => 'link', 'title' => __('message.traking_link')],
            ['data' => 'date', 'name' => 'date', 'title' => __('message.date')],
            ['data' => 'invoice', 'name' => 'invoice', 'title' => __('message.invoice'),'orderable' => false],
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center hide-search'),
        ];
    }
}