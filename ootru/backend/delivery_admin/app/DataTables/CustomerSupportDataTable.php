<?php

namespace App\DataTables;

use App\Models\CustomerSupport;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;


class CustomerSupportDataTable extends DataTable
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
                $status = 'warning';
                $status_name = 'pending';
                switch ($query->status) {
                    case 'pending':
                        $status = 'warning';
                        $status_name = __('message.pending');
                        break;
                    case 'inreview':
                        $status = 'primary';
                        $status_name = __('message.inreview');
                        break;
                    case 'resolved':
                        $status = 'success';
                        $status_name = __('message.resolved');
                        break;
                }
                return '<span class="text-capitalize badge bg-'.$status.'">'.$status_name.'</span>';
            })
            ->editColumn('created_at', function ($row) {
                return dateAgoFormate($row->created_at, true);
            })
            ->editColumn('resolution_detail', function ($row) {
                return $row->resolution_detail ?? '-';
            })
            ->editColumn('support_type', function ($row) {
                return $row->support_type ?? '-';
            })
            ->editColumn('message', function ($row) {
                return $row->message ?? '-';
            })
            ->editColumn('order_id', function ($row) {
                $order = $row->order_id;
                return $order ? '<a href="' . route('order.show', $order) . '">' . $order . '</a>' : '-' ;
            })

            ->editColumn('user_id', function($row) {
                $country = optional($row->user)->name ?? '-';

                return $country;
            })
            ->editColumn('id', function ($row) {
                $user = $row->id;
                return $user ? '<a href="' . route('customersupport.show', $user) . '">' . $user . '</a>' : '-' ;
            })
            ->addColumn('support_image', function ($row) {
                $imageurl = getSingleMediaCustomerSupport($row, 'support_image', true, false);
                return $imageurl ? '<a href="' . $imageurl . '" class="image-popup-vertical-fit">
                <img src="' . $imageurl . '" width="40" height="40"></a>' : '-';
            })
            
            ->addColumn('support_videos', function ($row) { 
                $videoUrl = getSingleMediaCustomerSupport($row, 'support_videos');
                return $videoUrl ? '<a href="' . $videoUrl . '" target="_blank">
                <video src="' . $videoUrl . '" width="60" height="60"></video></a>' : '-';
            })
             
            ->editColumn('user_id', function ($row) {
                $user = $row->user;
                return $user ? '<a href="' . route('users.show', $user->id) . '">' . $user->name . '</a>' : '-' ;
            })
            
            ->addColumn('action', function($row){
                $id = $row->id;
                return view('customer-suport.action',compact('id'))->render();
            })

            ->addIndexColumn()
            ->rawColumns(['action','status','id','support_image','support_videos','user_id','order_id']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\CustomerSupport $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(CustomerSupport $model)
    {
        $model = CustomerSupport::query()->orderBy('created_at','desc');
        $status_type = request()->input('status_type', 'all');
        switch ($status_type) {
            case 'all':
                break;
            case 'pending':
                $model->where('status', 'pending');
                break;
            case 'inreview':
                $model->where('status', 'inreview');
                break;
            case 'resolved':
                $model->where('status', 'resolved');
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
        return [
            Column::make('DT_RowIndex')
            ->searchable(false)
            ->title(__('message.srno'))
            ->orderable(false),
                ['data' => 'id', 'name' => 'id', 'title' => __('message.id')],
                ['data' => 'user_id', 'name' => 'user_id', 'title' => __('message.user'),'class' => 'text-capitalize'],
                ['data' => 'message', 'name' => 'message', 'title' => __('message.message')],
                ['data' => 'support_type', 'name' => 'support_type', 'title' => __('message.support_type') ],
                ['data' => 'order_id', 'name' => 'order_id', 'title' => __('message.order_id') ],
                ['data' => 'resolution_detail', 'name' => 'resolution_detail', 'title' => __('message.resolution_detail')],
                ['data' => 'support_image', 'name' => 'support_image', 'title' => __('message.image')],
                ['data' => 'support_videos', 'name' => 'support_videos', 'title' => __('message.videos')],
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