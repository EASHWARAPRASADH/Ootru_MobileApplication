<?php

namespace App\DataTables;

use App\Models\DeliveryManDocument;
use App\Models\User;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;

class ReferenceDataTable extends DataTable
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
            ->editColumn('city_id', function ($query) {
                return optional($query->city)->name ?? '-';
            })
            ->filterColumn('city_id', function ($query, $keyword) {
                $query->whereHas('city', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->editColumn('country_id', function ($query) {
                return optional($query->country)->name ?? '-';
            })
            ->filterColumn('country_id', function ($query, $keyword) {
                $query->whereHas('country', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->addColumn('app_version', function ($row) {
                return $row->app_version ?? '-';
            })
            ->editColumn('contact_number', function($query) {
                return auth()->user()->hasRole('admin') ? maskSensitiveInfo('contact_number', $query->contact_number) : maskSensitiveInfo('contact_number', $query->contact_number);
            })
            ->editColumn('email', function($query) {
                return auth()->user()->hasRole('admin') ? maskSensitiveInfo('email', $query->email) : maskSensitiveInfo('email', $query->email);
            })
            ->editColumn('last_actived_at', function ($query) {
                return dateAgoFormate($query->last_actived_at, true) ?? '-';
            })
            ->editColumn('name', function ($row) {
                if ($row->user_type === 'client') {
                    return '<a href="' . route('users.show', $row->id) . '" class="link-success">' . $row->name . '</a>';
                } elseif ($row->user_type === 'delivery_man') {
                    return '<a href="' . route('deliveryman.show', $row->id) . '" class="link-success">' . $row->name . '</a>';
                }
                return '-';
            })
            ->addIndexColumn()
            ->rawColumns(['action','name']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
         $model = User::whereNotNull('partner_referral_code');
         $city = request()->input('city_id');
         $country = request()->input('country_id');
         $lastActive = request()->input('last_actived_at');
         if ($city) {
            $model->where('city_id', $city);
        }
        if ($country) {
            $model->where('country_id', $country);
        }
        if ($lastActive) {
            if ($lastActive === 'active_user') {
                $model->where('last_actived_at', '<', now())
                ->where('last_actived_at', '>', now()->subDays(6));
            } elseif ($lastActive === 'engaged_user') {
                $model->where('last_actived_at', '<', now()->subDays(6))
                      ->where('last_actived_at', '>', now()->subDays(15));
            } elseif ($lastActive === 'inactive_user') {
                $model->where('last_actived_at', '<=', now()->subDays(15))
                ->orWhereNull('last_actived_at');
            }
        }

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
                ['data' => 'name', 'name' => 'name', 'title' => __('message.name'),  'class' => 'text-capitalize'],
                ['data' => 'email', 'name' => 'email', 'title' => __('message.email')],
                ['data' => 'city_id', 'name' => 'city_id', 'title' => __('message.city')],
                ['data' => 'country_id', 'name' => 'country_id', 'title' => __('message.country')],
                ['data' => 'contact_number', 'name' => 'contact_number', 'title' => __('message.contact_number')],
                ['data' => 'created_at', 'name' => 'created_at', 'title' => __('message.created_at')],
                ['data' => 'last_actived_at', 'name' => 'last_actived_at', 'title' => __('message.last_actived_at')],
                ['data' => 'app_version', 'name' => 'app_version', 'title' => __('message.app_version')],
                ['data' => 'partner_referral_code', 'name' => 'partner_referral_code', 'title' => __('message.use_referral')],
        ];
    }
}
