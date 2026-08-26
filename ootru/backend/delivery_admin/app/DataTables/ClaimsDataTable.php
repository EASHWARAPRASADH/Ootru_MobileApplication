<?php

namespace App\DataTables;

use App\Models\Claims;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;


class ClaimsDataTable extends DataTable
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
            ->editColumn('status', function ($query) {
                $status = 'danger';
                $status_name = 'cancelled';
                switch ($query->status) {
                    case 'pending':
                        $status = 'primary';
                        $status_name = __('message.pending');
                        break;
                    case 'approved':
                        $status = 'success';
                        $status_name = __('message.approved');
                        break;
                    case 'reject':
                        $status = 'warning';
                        $status_name = __('message.reject');
                        break;
                    case 'close':
                        $status = 'danger';
                        $status_name = __('message.close');
                        break;
                   
                }
                return '<span class="text-capitalize badge bg-' . $status . '">' . $status_name . '</span>';
            })
            ->editColumn('created_at', function ($query) {
                return dateAgoFormate($query['status'] == 'pending' ? $query->created_at : $query->updated_at, true);
            })
            ->addColumn('attachment_file', function ($row) {
                $media = getAttachmentArray( $row->getMedia('attachment_file'));
                $fileLinks = [];

                if( count($media) > 0 ) {
                    foreach ( $media as $file ) {
                        $fileLinks[] = '<a href="' .($file['url']) . '" target="_blank">' .  stringLong($file['name']) . '</a>';
                    }
                    return implode('<br>', $fileLinks);
                }
                return '-';
            })
            ->editColumn('detail', function($query) {
                return '<span data-toggle="tooltip" data-placement="top" title="' . htmlspecialchars($query->detail) . '">'
                        . stringLong($query->detail) .
                       '</span>';
            })
            ->editColumn('prof_value', function($query) {
                return '<span data-toggle="tooltip" data-placement="top" title="' . htmlspecialchars($query->prof_value) . '">'
                        . stringLong($query->prof_value) .
                       '</span>';
            })
            ->editColumn('client_id', function ($query) {
                $user = $query->user;
                return $user ? '<a href="' . route('users.show', $user->id) . '">' . $user->name . '</a>' : '-' ;
            })
            ->editColumn('traking_no',function($row){
                $order = $row->order;
                if ($order && $order->milisecond == $row->traking_no) {
                    
                    return '<a href="' . route('order.show', $order->id) . '">' . $order->milisecond . '</a>';
                }  
                return '-';
            })
            ->addColumn('option',function($row){
                $id = $row->id;
                $action_type = 'close';
                return view('claims.action',compact('id','row','action_type'))->render(); 
            })
            ->addColumn('resolve', function($row){
                $id = $row->id;
                $action_type = 'resolve';
                return view('claims.action',compact('id','row','action_type'))->render();
            })
            ->addColumn('action', function($row) {
                $data = $row->id;
                $action_type = 'action';
                return view('claims.action',compact('data','row','action_type'))->render();
            })

            ->addIndexColumn()
            ->rawColumns(['action','status','id','resolve','attachment_file','detail','prof_value','client_id','traking_no','action_type','option']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Claims $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Claims $model)
    {
        $model = Claims::query()->orderBy('created_at','desc');

        $claims_type = request()->input('claims_type', 'all');
        switch ($claims_type) {
            case 'all':
                break;
            case 'pending':
                $model->where('status', 'pending');
                break;
            case 'approved':
                $model->where('status', 'approved');
                break;
            case 'reject':
                $model->where('status', 'reject');
                break;
            case 'close':
                $model->where('status', 'close');
                break;
            default:
                break;
        }
        return $model;
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        $claims_type = request()->input('claims_type', 'all');
        $columns =  [
            Column::make('DT_RowIndex')
                ->searchable(false)
                ->title(__('message.srno'))
                ->orderable(false)
                ->addClass('text-capitalize')
                ->width(60),
                ['data' => 'traking_no', 'name' => 'traking_no', 'title' => __('message.traking_no')],
                ['data' => 'client_id', 'name' => 'client_id', 'title' => __('message.submited_by')],
                ['data' => 'prof_value', 'name' => 'prof_value', 'title' => __('message.prof_value')],
                ['data' => 'detail', 'name' => 'detail', 'title' => __('message.detail')],
                ['data' => 'attachment_file', 'name' => 'attachment_file', 'title' => __('message.attachment_file')],
           
        ];
        if($claims_type == 'all')
        {
            $columns[] = Column::make('created_at', 'created_at')->title(__('message.created_at'));
            $columns[] = Column::make('status', 'status')->title(__('message.status'));
            $columns[] = Column::make('action', 'action')->title(__('message.action'));


        }
        if($claims_type == 'approved')
        {
            $columns[] = Column::make('created_at', 'created_at')->title(__('message.date'));
            $columns[] = Column::make('status', 'status')->title(__('message.status'));
            $columns[] = Column::computed('resolve')
            ->exportable(false)
            ->printable(false)
            ->width(60)
            ->addClass('text-center hide-search');
        }
        if($claims_type == 'pending')
        {
            $columns[] = Column::make('created_at', 'created_at')->title(__('message.created_at'));
            $columns[] = Column::make('status', 'status')->title(__('message.status'));
            $columns[] = Column::make('action', 'action')->title(__('message.action'));
          

        }
        if($claims_type == 'reject')
        {
            $columns[] = Column::make('created_at', 'created_at')->title(__('message.date'));
            $columns[] = Column::make('status', 'status')->title(__('message.status'));
        }
        if($claims_type == 'close')
        {
            $columns[] = Column::make('created_at', 'created_at')->title(__('message.date'));
            $columns[] = Column::make('status', 'status')->title(__('message.status'));
            $columns[] = Column::make('option', 'option')->title(__('message.action'));

        }
        return $columns;
    }
}
