<?php

namespace App\DataTables;

use App\Models\Vehicle;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;

class VehicleDataTable extends DataTable
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
               $delete_at = null;
               $action_type = 'status';
               return view('vehicle.action',compact('data','delete_at','action_type'))->render();
            })

            ->editColumn('created_at', function ($row) {
                return dateAgoFormate($row->created_at, true);
            })
            ->addColumn('vehicle_image', function($row){
                return '<a href="'.getSingleMedia($row , 'vehicle_image').'" class="image-popup-vertical-fit"><img src="'.getSingleMedia($row , 'vehicle_image').'" width="40" height="40" ></a>';
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
            ->editColumn('price', function ($row) {
                $price = optional($row)->price;
                return $price ? getPriceFormat($price) : '-';
            })
            ->editColumn('min_km', function ($row) {
                $min_km = optional($row)->min_km;
                return $min_km ? $min_km : '-';
            })

            ->editColumn('per_km_charge', function ($row) {
                $per_km_charge = optional($row)->per_km_charge;
                return $per_km_charge ? getPriceFormat($per_km_charge) : '-';
            })

            ->editColumn('title', function ($row) {
                return $row->id ? '<a href="' . route('vehicle.show', $row->id) . '" class="link-success">' . $row->title . '</a>' : '-' ;
            })
            ->addColumn('action', function($row){
                $id = $row->id;
                $delete_at = $row->deleted_at;
                $action_type = 'action';
                return view('vehicle.action',compact('id','delete_at','action_type'))->render();
            })

            ->addIndexColumn()
            ->rawColumns(['action','status','checkbox','vehicle_image','title']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Vehicle $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Vehicle $model)
    {
        $model = Vehicle::query();
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
                ->width(60),
                ['data' => 'id', 'name' => 'id', 'title' => __('message.id')],
                ['data' => 'title', 'name' => 'title', 'title' => __('message.name'),'class' => 'text-capitalize'],
                ['data' => 'size', 'name' => 'size', 'title' => __('message.vehicle_size')],
                ['data' => 'capacity', 'name' => 'capacity', 'title' => __('message.vehicle_capacity')],
                ['data' => 'price', 'name' => 'price', 'title' => __('message.price')],
                ['data' => 'min_km', 'name' => 'min_km', 'title' => __('message.min_km')],
                ['data' => 'per_km_charge', 'name' => 'per_km_charge', 'title' => __('message.per_km_charge')],
                ['data' => 'created_at', 'name' => 'created_at', 'title' => __('message.created_at')],
                ['data' => 'status', 'name' => 'status', 'title' => __('message.status')],
                ['data' => 'vehicle_image', 'name' => 'vehicle_image', 'title' => __('message.vehicle_image'),'orderable' => false],
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center hide-search'),
        ];
    }
}
