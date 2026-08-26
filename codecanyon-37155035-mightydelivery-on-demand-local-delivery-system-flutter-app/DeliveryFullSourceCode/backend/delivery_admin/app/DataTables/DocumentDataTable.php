<?php

namespace App\DataTables;

use App\Models\Document;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;

class DocumentDataTable extends DataTable
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
            ->editColumn('checkbox', function ($row) {
                return '<input type="checkbox" class=" select-table-row-checked-values" id="datatable-row-' . $row->id . '" name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">';
            })
            ->editColumn('status', function ($data) {
                $action_type = 'status';
                $deleted_at = null;
                return view('document.action', compact('data', 'action_type', 'deleted_at'))->render();
            })

            ->editColumn('created_at', function ($query) {
                return dateAgoFormate($query->created_at, true);
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
            ->editColumn('is_required', function ($query) {
                return $query->is_required ? 'yes' : 'no';
            })

            ->addColumn('action', function ($row) {
                $id = $row->id;
                $action_type = 'action';
                $deleted_at = $row->deleted_at;
                return view('document.action', compact('id', 'deleted_at', 'action_type'))->render();
            })
            ->addIndexColumn()
            ->rawColumns(['checkbox', 'action', 'status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Document $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Document $model)
    {
        $model = Document::query();
        return $model->newQuery()->withTrashed();
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
                ->orderable(false)
                ->title('<input type="checkbox" class ="select-all-table" name="select_all" id="select-all-table">')
                ->width(10),
            Column::make('DT_RowIndex')
                ->searchable(false)
                ->title(__('message.srno'))
                ->orderable(false),
            ['data' => 'name', 'name' => 'name', 'title' => __('message.name'),'class' => 'text-capitalize'],
            ['data' => 'is_required', 'name' => 'is_required', 'title' => __('message.required'),'class' => 'text-capitalize'],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => __('message.created_at')],
            ['data' => 'status', 'name' => 'status', 'title' => __('message.status')],

            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->title(__('message.action'))
                ->width(60)
                ->addClass('text-center hide-search'),
        ];
    }
}
