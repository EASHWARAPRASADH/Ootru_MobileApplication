<?php

namespace App\DataTables;

use App\Models\RestApi;
use App\Models\RestApiHistory;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;

class RestApiDataTable extends DataTable
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
            ->editColumn('last_access_date', function ($row) {
                return dateAgoFormate($row->last_access_date, true);
            })
            ->editColumn('rest_key', function($row) {
                $restApi = $row->rest_key;
                $maskedRestApi = str_repeat('*', strlen($restApi) - 5) . substr($restApi, -5);
                return $maskedRestApi;
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

            ->addColumn('use_value', function ($row) {
                $restApiHistoryCount = RestApiHistory::where('rest_key', $row->rest_key)->count();
                $id = $row->id;
            
                $link = '<a class="mr-2 loadRemoteModel" href="' . route('rest-api.show', $id) . '">' .
                        ($restApiHistoryCount > 0 ? $restApiHistoryCount : null) .
                        '</a>';
            
                return $link;
            })
            ->editColumn('city_id', function($row) {
                $city = optional($row->city)->name ?? '-';
                return $city ;
            })
            ->filterColumn('city_id', function ($query, $keyword) {
                $query->whereHas('city', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->addColumn('action', function($row){
                $id = $row->id;
                $deleted_at = $row->deleted_at;
                return view('restapi.action',compact('id','deleted_at'))->render();
            })

            ->addIndexColumn()
            ->rawColumns(['action','status','checkbox','use_value']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\RestApi $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(RestApi $model)
    {
        $model = RestApi::query()->orderBy('id','desc');
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
                ['data' => 'rest_key', 'name' => 'rest_key', 'title' => __('message.key')],
                ['data' => 'name', 'name' => 'name', 'title' => __('message.name')],
                ['data' => 'description', 'name' => 'description', 'title' => __('message.description')],
                ['data' => 'country_id', 'name' => 'country_id', 'title' => __('message.country')],
                ['data' => 'city_id', 'name' => 'city_id', 'title' => __('message.city')],
                ['data' => 'created_at', 'name' => 'created_at', 'title' => __('message.created_at')],
                ['data' => 'use_value', 'name' => 'use_value', 'title' => __('message.count')],
                ['data' => 'last_access_date', 'name' => 'last_access_date', 'title' => __('message.last_access_date')],
                // ['data' => 'status', 'name' => 'status', 'title' => __('message.status')],
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center hide-search'),
        ];
    }
}
