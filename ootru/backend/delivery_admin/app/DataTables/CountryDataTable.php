<?php

namespace App\DataTables;

use App\Models\Country;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;

class CountryDataTable extends DataTable
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
            ->editColumn('status', function($data) {
                $action_type = 'status';
                $delete_at = null;
                return view('country.action',compact('data','action_type','delete_at'))->render();
            })

            ->editColumn('created_at', function ($row) {
                return dateAgoFormate($row->created_at, true);
            })

            ->addColumn('action', function($row){
                $id = $row->id;
                $delete_at = $row->deleted_at;
                $action_type = 'action';
                return view('country.action',compact('id','delete_at','action_type'))->render();
            })

            ->addIndexColumn()
            ->rawColumns(['action','status','checkbox']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Country $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Country $model)
    {
        $model = Country::query()->orderBy('name','asc');
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
            Column::make('checkbox')
                ->searchable(false)
                ->title('<input type="checkbox" class ="select-all-table" name="select_all" id="select-all-table">')
                ->orderable(false)
                ->addClass('text-capitalize')
                ->width(60),
                ['data' => 'name', 'name' => 'name', 'title' => __('message.name',),'class' => 'text-capitalize'],
                ['data' => 'distance_type', 'name' => 'distance_type', 'title' => __('message.distance_type')],
                ['data' => 'weight_type', 'name' => 'weight_type', 'title' => __('message.weight_type')],
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
