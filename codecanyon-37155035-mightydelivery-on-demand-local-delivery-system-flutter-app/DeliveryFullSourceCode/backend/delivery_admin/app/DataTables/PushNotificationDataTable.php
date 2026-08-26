<?php

namespace App\DataTables;

use App\Models\PushNotification;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

use App\Traits\DataTableTrait;

class PushNotificationDataTable extends DataTable
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

            ->editColumn('message', function ($row) {
                return isset($row->message) ? stringLong($row->message, 'desc') : null;
            })
            ->addColumn('notification_image',function($row){
                return '<a href="'.getSingleMedia($row , 'notification_image').'" class="image-popup-vertical-fit"><img src="'.getSingleMedia($row , 'notification_image').'" width="40" height="40" ></a>';
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
            ->addIndexColumn()
            ->addColumn('action', 'push_notification.action')
            ->rawColumns(['action','notification_image']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\PushNotification $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(PushNotification $model)
    {
        $model = PushNotification::query();
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
                ->orderable(false)
                ->width(60),
            Column::make('notification_image')->orderable(false)->title(__('message.image')),
            Column::make('title')->title(__('message.title'))->addClass('text-capitalize'),
            Column::make('message')->title(__('message.message'))->addClass('text-capitalize'),
            Column::make('notification_count')->title(__('message.number_sent')),
            Column::make('created_at')->title(__('message.created_at')),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->title(__('message.action'))
                ->width(60)
                ->addClass('text-center'),
        ];
    }
}
