<?php

namespace App\DataTables;

use App\Models\ExtraCharge;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;

class ExtraChargeDataTable extends DataTable
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
            ->editColumn('status', function($data) {
               $delete_at = null;
               $action_type = 'status';
               return view('extracharge.action',compact('data','delete_at','action_type'))->render();
            })

            ->editColumn('country_id', function($row) {
                $country = optional($row->country)->name ?? '-';
                return $country;
            })
            ->filterColumn('country_id', function ($query, $keyword) {
                $query->whereHas('country', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })

            ->editColumn('city_id', function($row) {
                $city = optional($row->city)->name ?? '-';
                return $city ;
            })
            ->filterColumn('city_id', function ($query, $keyword) {
                $query->whereHas('city', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->editColumn('charges', function ($row) {
                $charges_type = optional($row)->charges_type;
                $charges = optional($row)->charges;
                $charges = $charges_type == 'percentage' ? $charges . ' % ' : ($charges_type === 'fixed' ? $charges . ' fixed ' : '-');
                return $charges;
            })

            ->editColumn('created_at', function ($row) {
                return dateAgoFormate($row->created_at, true);
            })

            ->order(function ($query) {
                if (request()->has('order')) {
                    $order = request()->order[0];
                    $column_index = $order['column'];

                    $column_name = 'id';
                    $direction = 'desc';
                    if( $column_index != 0) {
                        $column_name = request()->columns[$column_index]['data'];
                        $direction = $order['dir'];
                    }

                    $query->orderBy($column_name, $direction);
                }
            })

            ->addColumn('action', function($row){
                $id = $row->id;
                $delete_at = $row->deleted_at;
                $action_type = 'action';
                return view('extracharge.action',compact('id','delete_at','action_type'))->render();
            })

            ->addIndexColumn()
            ->rawColumns(['action','status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\ExtraCharge $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ExtraCharge $model)
    {
        $model = ExtraCharge::query();
        return $model->withTrashed();
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
                ['data' => 'title', 'name' => 'title', 'title' => __('message.title'),'class' => 'text-capitalize'],
                ['data' => 'country_id', 'name' => 'country_id', 'title' => __('message.country')],
                ['data' => 'city_id', 'name' => 'city_id', 'title' => __('message.city')],
                ['data' => 'charges', 'name' => 'charges', 'title' => __('message.charges')],
                ['data' => 'created_at', 'name' => 'created_at', 'title' => __('message.created_at')],
                ['data' => 'status', 'name' => 'status', 'title' => __('message.status')],
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center hide-search'),
        ];
    }
}
