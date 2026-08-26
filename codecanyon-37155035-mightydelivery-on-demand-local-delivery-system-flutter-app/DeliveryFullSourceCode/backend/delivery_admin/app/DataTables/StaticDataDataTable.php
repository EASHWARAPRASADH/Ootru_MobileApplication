<?php

namespace App\DataTables;

use App\Models\StaticData;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;

class StaticDataDataTable extends DataTable
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

            ->editColumn('created_at', function ($query) {
                return dateAgoFormate($query->created_at, true);
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
                return view('staticdata.action',compact('id','delete_at'))->render();
            })

            ->addIndexColumn()
            ->rawColumns(['action','status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\StaticData $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(StaticData $model)
    {
        $model = StaticData::query();
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
                ['data' => 'label', 'name' => 'label', 'title' => __('message.label'),'class' => 'text-capitalize'],
                ['data' => 'value', 'name' => 'value', 'title' => __('message.value'),'class' => 'text-capitalize'],
                ['data' => 'created_at', 'name' => 'created_at', 'title' => __('message.created_at')],
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center hide-search'),
        ];
    }
}
