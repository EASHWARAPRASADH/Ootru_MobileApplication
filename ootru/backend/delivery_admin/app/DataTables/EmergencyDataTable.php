<?php

namespace App\DataTables;

use App\Models\Emergency;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;


class EmergencyDataTable extends DataTable
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
            ->editColumn('status', function($query) {
                $status = 'primary';
                $status_name = 'pending';
                switch ($query->status) {
                    case '0':
                        $status = 'primary';
                        $status_name = __('message.pending');
                        break;
                    case '1':
                        $status = 'info';
                        $status_name = __('message.in_progress');
                        break;
                    case '2':
                        $status = 'danger';
                        $status_name = __('message.close');
                        break;
                }
                return '<span class="text-capitalize badge bg-'.$status.'">'.$status_name.'</span>';
            })
            ->editColumn('created_at', function ($row) {
                return dateAgoFormate($row->created_at, true);
            })
            ->editColumn('updated_at', function ($row) {
                return dateAgoFormate($row->updated_at, true);
            })
          
            ->editColumn('emergency_resolved', function($query) {
                $emergencyResolved = $query->emergency_resolved;
            
                if ($emergencyResolved) {
                    return '<span data-toggle="tooltip" title="' . e($emergencyResolved) . '">' . stringLong($emergencyResolved, 'title', 20) . '</span>';
                }
            
                return '-';
            })
            
            ->editColumn('delivery_man_id', function ($row) {
                $user = $row->deliveryMan;
                return $user ? '<a href="' . route('users.show', $user->id) . '">' . $user->name . '</a>' : '-' ;
            })

            ->addColumn('in_progress', function ($row) {
                if($row->status == 0) {
                    return sprintf(
                        '<form action="%s" method="POST" class="d-inline update-status-form">
                            %s %s
                            <input type="hidden" name="status" value="1">
                            <button type="submit" class="btn btn-sm btn-warning">'.__('message.in_progress').'</button>
                        </form>',
                        route('emergency.update', $row->id),
                        csrf_field(),
                        method_field('PUT')
                    );
                }elseif($row->status == 2){
                    return '<span class="badge bg-success">' . __('message.resolved') . '</span>';
                }
                return '<span class="badge bg-light">' . __('message.in_progress') . '</span>';
            })
            
            ->editColumn('datetime', function ($row) {
                return dateAgoFormate($row->datetime, true) ?? '-';
            })
            ->addColumn('action', function($row){
                $id = $row->id;
                return view('emergency.action',compact('id'))->render();
            })

            ->addIndexColumn()
            ->rawColumns(['action','status','datetime','delivery_man_id','updated_at','emergency_resolved','in_progress']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Emergency $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Emergency $model)
    {
        $model = Emergency::query()->orderBy('created_at','desc');
        return $model;
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
                ['data' => 'delivery_man_id', 'name' => 'delivery_man_id', 'title' => __('message.delivery_man'),'class' => 'text-capitalize'],
                ['data' => 'emrgency_reason', 'name' => 'emrgency_reason', 'title' => __('message.reason')],
                ['data' => 'emergency_resolved', 'name' => 'emergency_resolved', 'title' => __('message.resolved')],
                ['data' => 'created_at', 'name' => 'created_at', 'title' => __('message.created_at')],
                ['data' => 'updated_at', 'name' => 'updated_at', 'title' => __('message.updated_at')],
                ['data' => 'status', 'name' => 'status', 'title' => __('message.status')],
                ['data' => 'in_progress', 'name' => 'in_progress', 'title' => __('message.in_progress')],
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center hide-search'),
        ];
    }
}