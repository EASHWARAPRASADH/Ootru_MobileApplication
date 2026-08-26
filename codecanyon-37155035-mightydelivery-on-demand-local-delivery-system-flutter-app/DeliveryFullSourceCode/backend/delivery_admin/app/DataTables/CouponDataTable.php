<?php

namespace App\DataTables;

use App\Models\Coupon;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;

class CouponDataTable extends DataTable
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
            ->editColumn('start_date', function ($row) {
                return dateFormate($row->start_date, true);
            })
            ->editColumn('end_date', function ($row) {
                return dateFormate($row->end_date, true);
            })

            ->editColumn('discount_amount', function ($row) {
                $value_type = optional($row)->value_type;
                $discount_amount = optional($row)->discount_amount;
                $discount_amount = $value_type == 'percentage' ? $discount_amount . ' % ' : ($value_type === 'fixed' ? $discount_amount . ' fixed ' : '-');
                return $discount_amount;
            })
            
            ->editColumn('city_type',function($row){
                $cityType =  str_replace('_', ' ', ucfirst(optional($row)->city_type));
                return $cityType;
            })
            ->editColumn('status', function ($data) {
                $action_type = 'status';
                $deleted_at = $data->deleted_at;
                $deleted_at = null;
                return view('coupon.action', compact('data', 'action_type', 'deleted_at'))->render();
            })
            
            ->addColumn('action', function($row){
                $id = $row->id;
                $deleted_at = $row->deleted_at;
                $action_type = 'action';
                return view('coupon.action',compact('id','deleted_at','action_type'))->render();
            })

            ->addIndexColumn()
            ->rawColumns(['action','status','checkbox']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Coupon $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Coupon $model)
    {
        $model = Coupon::query()->orderBy('id','desc');
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
            Column::make('DT_RowIndex')
                ->searchable(false)
                ->title(__('message.srno'))
                ->orderable(false),
                ['data' => 'coupon_code', 'name' => 'coupon_code', 'title' => __('message.coupon_code')],
                ['data' => 'start_date', 'name' => 'start_date', 'title' => __('message.start_date')],
                ['data' => 'end_date', 'name' => 'end_date', 'title' => __('message.end_date')],
                ['data' => 'discount_amount', 'name' => 'discount_amount', 'title' => __('message.discount_amount')],
                ['data' => 'city_type', 'name' => 'city_type', 'title' => __('message.city_type')],
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
