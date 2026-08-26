<?php

namespace App\DataTables;

use App\Models\Ratings;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;

class RatingDataTable extends DataTable
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
            ->editColumn('user_id', function ($row) {
                $user = $row->user; 
                if ($user) {
                    $routeName = $user->user_type === 'delivery_man' ? 'deliveryman.show' : 'users.show';
                    return '<a href="' . route($routeName, $user->id) . '">' . $user->name . '</a>';
                }
            
                return '-';
            })
            
            ->filterColumn('user_id', function ($query, $keyword) {
                $query->whereHas('user', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->editColumn('order_id', function ($row) {
                $order = $row->order_id;
                return $order ? '<a href="' . route('order.show', $order) . '">' . $order . '</a>' : '-' ;
            })

            ->addIndexColumn()
            ->rawColumns(['status','user_id','order_id']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Ratings $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Ratings $model)
    {
        if ($this->delivery_man_id != null) {
            # code...
            $model = $model->where('review_user_id',$this->delivery_man_id);
        }
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
                ['data' => 'user_id', 'name' => 'user_id', 'title' => __('message.name')],
                ['data' => 'order_id', 'name' => 'order_id', 'title' => __('message.order_id')],
                ['data' => 'rating', 'name' => 'rating', 'title' => __('message.rating')],
                ['data' => 'comment', 'name' => 'comment', 'title' => __('message.comment')],
                ['data' => 'created_at', 'name' => 'created_at', 'title' => __('message.created_at')],
        ];
    }
}
