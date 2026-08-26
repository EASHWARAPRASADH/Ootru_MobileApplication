<?php

namespace App\DataTables;

use App\Models\CourierCompanies;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;

class CourierCompaniesDataTable extends DataTable
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
            ->editColumn('status', function ($data) {
                $action_type = 'status';
                return view('couriercompanies.action', compact('data', 'action_type'))->render();
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
            ->addColumn('couriercompanies_image', function ($row) {
                $imageurl = getSingleMediaCustomerSupport($row, 'couriercompanies_image', true, false);
                return $imageurl ? '<a href="' . $imageurl . '" class="image-popup-vertical-fit">
                <img src="' . $imageurl . '" width="40" height="40"></a>' : '-';
            })
           
            ->editColumn('link', function ($row) {
                $link = $row->link;
                return $link ? '<a href="' . $link . '" target="_blank">' . $link . '</a>' : '-';
            })

            ->addColumn('action', function($row){
                $id = $row->id;
                $action_type = 'action';
                return view('couriercompanies.action',compact('id','action_type'))->render();
            })

            ->addIndexColumn()
            ->rawColumns(['action','status','link','couriercompanies_image']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\CourierCompanies $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(CourierCompanies $model)
    {
        $model = CourierCompanies::query();
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
                ['data' => 'name', 'name' => 'name', 'title' => __('message.name'),'class' => 'text-capitalize'],
                ['data' => 'link', 'name' => 'link', 'title' => __('message.link'),'class' => 'text-capitalize'],
                ['data' => 'status', 'name' => 'status', 'title' => __('message.status')],
                ['data' => 'couriercompanies_image', 'name' => 'couriercompanies_image', 'title' => __('message.image'),'class' => 'text-capitalize'],
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center hide-search'),
        ];
    }
}
