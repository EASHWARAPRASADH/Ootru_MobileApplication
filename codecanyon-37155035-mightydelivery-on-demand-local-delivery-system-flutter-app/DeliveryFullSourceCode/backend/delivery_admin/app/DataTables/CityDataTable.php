<?php

namespace App\DataTables;

use App\Models\City;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;


class CityDataTable extends DataTable
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
                $action_type = 'status';
                $delete_at = null;
                return view('city.action',compact('data','action_type','delete_at'))->render();
            })

            ->editColumn('country_id', function($row) {
                $country = optional($row->country)->name ?? '-';

                return $country;
            })

            ->filterColumn('country_id', function ($query, $keyword) {
                $query->whereHas('country', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })

            ->editColumn('min_distance', function($row) {
                $country = optional($row->country)->distance_type;
                $distance = optional($row)->min_distance;

                return $distance ? $distance.' '.$country : '-';
            })

            ->editColumn('min_weight',function($row){
                $country = optional($row->country)->weight_type;
                $weight = optional($row)->min_weight;

                return $weight ? $weight.' '.$country : '-';
            })

            ->editColumn('fixed_charges', function ($row) {
                $fixed_charges = optional($row)->fixed_charges;

                return $fixed_charges ? getPriceFormat($fixed_charges) : '-';
            })

            ->editColumn('cancel_charges',function($row){
                $cancel_charges = optional($row)->cancel_charges;

                return $cancel_charges ? getPriceFormat($cancel_charges) : '-';
            })
            ->editColumn('id', function ($row) {
                $city = $row->id;
                return $city ? '<a href="' . route('city.show', $city) . '">' . $city . '</a>' : '-' ;
            })

            ->editColumn('per_distance_charges',function($row){
                $per_distance_charges = optional($row)->per_distance_charges;

                return $per_distance_charges ? getPriceFormat($per_distance_charges) : '-';

            })

            ->editColumn('per_weight_charges',function($row){
                $per_weight_charges = optional($row)->per_weight_charges;
                return $per_weight_charges ? getPriceFormat($per_weight_charges) : '-';

            })

            ->editColumn('admin_commission', function ($row) {
                $commission_type = optional($row)->commission_type;
                $admin_commission = optional($row)->admin_commission;
                $admin_commission = $commission_type == 'percentage' ? $admin_commission . ' % ' : ($commission_type === 'fixed' ? $admin_commission . ' fixed ' : '-');
                return $admin_commission;
            })

            ->editColumn('created_at', function ($row) {
                return dateAgoFormate($row->created_at, true);
            })

            ->addColumn('action', function($row){
                $id = $row->id;
                $delete_at = $row->deleted_at;
                $action_type= 'action';
                return view('city.action',compact('id','delete_at','action_type'))->render();
            })

            ->addIndexColumn()
            ->rawColumns(['action','status','checkbox','id']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\City $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(City $model)
    {
        $model = City::query()->orderBy('created_at','desc');
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
                ->addClass('text-capitalize')
                ->width(60),
                ['data' => 'id', 'name' => 'id', 'title' => __('message.id')],
                ['data' => 'name', 'name' => 'name', 'title' => __('message.name'),'class' => 'text-capitalize'],
                ['data' => 'country_id', 'name' => 'country_id', 'title' => __('message.country')],
                ['data' => 'min_distance', 'name' => 'min_distance', 'title' => __('message.min_distance')],
                ['data' => 'min_weight', 'name' => 'min_weight', 'title' => __('message.min_weight')],
                ['data' => 'fixed_charges', 'name' => 'fixed_charges', 'title' => __('message.fixed_charges')],
                ['data' => 'cancel_charges', 'name' => 'cancel_charges', 'title' => __('message.cancel_charges')],
                ['data' => 'per_distance_charges', 'name' => 'per_distance_charges', 'title' => __('message.per_distance_charges')],
                ['data' => 'per_weight_charges', 'name' => 'per_weight_charges', 'title' => __('message.per_weight_charges')],
                ['data' => 'admin_commission', 'name' => 'admin_commission', 'title' => __('message.admin_commission')],
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
