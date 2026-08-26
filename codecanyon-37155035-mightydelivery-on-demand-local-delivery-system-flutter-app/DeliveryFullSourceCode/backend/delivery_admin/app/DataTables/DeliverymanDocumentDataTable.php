<?php

namespace App\DataTables;

use App\Models\DeliveryManDocument;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;

class DeliverymanDocumentDataTable extends DataTable
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
            ->addColumn('documents', function($row){
                $fileUrl = getSingleMedia($row, 'delivery_man_document');
                $fileExtension = pathinfo($fileUrl, PATHINFO_EXTENSION);
                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
 
                if (in_array(strtolower($fileExtension), $imageExtensions)) {
                    return '<a href="' . $fileUrl . '" class="image-popup-vertical-fit ml-2">
                                <img src="' . $fileUrl . '" width="40" height="40">
                            </a>';
                } else {
                    return '<a href="' . $fileUrl . '" target="_blank" class="ml-3"><i class="fas fa-file fa-2x"></i></a>';
                }
            })
            ->addColumn('delivery_man_id', function ($row) {
                $user = $row->delivery_man;
                return $user->id ? '<a href="' . route('deliveryman.show', $user->id) . '" class="link-success">' . $user->name . '</a>' : '-' ;
            })
            ->filterColumn('delivery_man_id', function($query, $keyword) {
                return $query->orWhereHas('delivery_man', function($q) use($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })

            ->editColumn('document_id', function ($query) {
                return optional($query->document)->name ?? '-';
            })
            ->addColumn('action', function ($data) {
                $id = $data->id;
                return view('deliverymandocument.action', compact('data', 'id'))->render();
            })
            ->addIndexColumn()
            ->rawColumns(['checkbox', 'action', 'documents','delivery_man_id']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\DeliveryManDocument $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(DeliveryManDocument $model)
    {
         $model = DeliveryManDocument::query();
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
                ->orderable(false)
                ->title('<input type="checkbox" class ="select-all-table" name="select_all" id="select-all-table">')
                ->width(10),
            Column::make('DT_RowIndex')
                ->searchable(false)
                ->title(__('message.srno'))
                ->orderable(false),
            ['data' => 'delivery_man_id', 'name' => 'delivery_man_id', 'title' => __('message.delivery_man')],
            ['data' => 'document_id', 'name' => 'document_id', 'title' => __('message.document_name')],
            ['data' => 'documents', 'name' => 'documents', 'title' => __('message.document'),'orderable' => false],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => __('message.created_at')],
            ['data' => 'action', 'name' => 'action', 'title' => __('message.action'),'orderable' => false],
        ];
    }
}
