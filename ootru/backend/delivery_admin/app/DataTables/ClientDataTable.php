<?php

namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;

class ClientDataTable extends DataTable
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
            ->editColumn('checkbox', function ($row) {
                return '<input type="checkbox" class=" select-table-row-checked-values" id="datatable-row-' . $row->id . '" name="datatable_ids[]" value="' . $row->id . '" onclick="dataTableRowCheck(' . $row->id . ')">';
            })
            ->editColumn('status', function ($data) {
                $action_type = 'status';
                $deleted_at = null;
                return view('users.action', compact('data', 'action_type', 'deleted_at'))->render();
            })
            ->editColumn('created_at', function ($query) {
                return dateAgoFormate($query->created_at, true);
            })
            ->editColumn('last_actived_at', function ($query) {
                return dateAgoFormate($query->last_actived_at, true) ?? '-';
            })
            ->editColumn('contact_number', function($query) {
                return auth()->user()->hasRole('admin') ? maskSensitiveInfo('contact_number', $query->contact_number) : maskSensitiveInfo('contact_number', $query->contact_number);
            })
            ->addColumn('rating',function($query){
                return count($query->rating) > 0 ? (float) number_format(max($query->rating->avg('rating'),0), 2) : 0;
            })
            ->editColumn('email', function($query) {
                return auth()->user()->hasRole('admin') ? maskSensitiveInfo('email', $query->email) : maskSensitiveInfo('email', $query->email);
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
            ->editColumn('country_id', function ($query) {
                return optional($query->country)->name ?? '-';
            })
            ->addColumn('name', function ($row) {

                return $row->user_type === 'client' ? '<a href="' . route('users.show', $row->id) . '" class="link-success">' . $row->name . '</a>' : '-' ;
            })
            ->editColumn('otp_verify_at', function ($data) {
                if ($data->otp_verify_at !== null) {
                    return '<p class="text-capitalize badge bg-success text-center mt-3"> Verified</p>';
                }else{

                    $action_type = 'verify';
                    $deleted_at = null;
                    return view('users.action', compact('data', 'action_type', 'deleted_at'))->render();
                }
            })
           ->editColumn('app_version',function($row){
                return $row->app_version ?? '-';
            })

            ->addColumn('action', function ($row) {
                $id = $row->id;
                $action_type = 'action';
                $deleted_at = $row->deleted_at;
                return view('users.action', compact('id', 'deleted_at', 'action_type'))->render();
            })
            ->addIndexColumn()
            ->editColumn('is_autoverified_email', function ($data) {
                $user = $data->whereNotNull('email_verified_at')->get();
                if ($user) {
                    return '<div class="custom-switch custom-switch-text">
                            <input type="checkbox" class="custom-control-input change_user_verification"
                                data-type="user" data-name="is_autoverified_email" id="email_' . $data->id . '"
                                data-id="' . $data->id . '" ' . ($data->email_verified_at ? 'checked' : '') . ' value="' . $data->id . '">
                            <label class="custom-control-label" for="email_' . $data->id . '" data-on-label="Yes" data-off-label="No"></label>
                        </div>';
        
                } else {
                    $action_type = 'email_verified';
                    $deleted_at = null;
                    return view('users.action', compact('data', 'action_type', 'deleted_at'))->render();
                }
            })
            ->editColumn('is_autoverified_mobile', function ($data) {
                $user = $data->whereNotNull('otp_verify_at')->get();
                if ($user) {
                    return '<div class="custom-switch custom-switch-text">
                                <input type="checkbox" class="custom-control-input change_user_verification"
                                    data-type="user" data-name="is_autoverified_mobile" id="mobile_' . $data->id . '"
                                    data-id="' . $data->id . '" ' . ($data->otp_verify_at != NULL ? 'checked' : '') . ' value="' . $data->id . '">
                                <label class="custom-control-label" for="mobile_' . $data->id . '" data-on-label="Yes" data-off-label="No"></label>
                            </div>';
                } else {
                    $action_type = 'mobile_verified';
                    $deleted_at = null;
                    return view('users.action', compact('data', 'action_type', 'deleted_at'))->render();
                }
            })
            ->rawColumns(['checkbox', 'action', 'status','name','otp_verify_at','is_autoverified_mobile','is_autoverified_email']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        $model = User::whereIn('user_type', ['client']);
        $city = request()->input('city_id');
        $country = request()->input('country_id');
        $lastActive = request()->input('last_actived_at');
        $status = request('status');
        switch ($status) {
            case '':
                break;

            case 'active':
                $model = $model->where('status', 1)->whereNull('deleted_at')
                        ->whereNotNull('email_verified_at')
                        ->whereNotNull('otp_verify_at');
                break;
            case 'inactive':
                $model = $model->where('status', 0);
                break;
                case 'pending':
                    $model = $model->where('status', 1)->where(function ($query) {
                        $query->where('is_autoverified_email', 0)
                            ->whereNull('email_verified_at')
                            ->orWhere('is_autoverified_mobile', 0)
                            ->whereNull('otp_verify_at');
                    });
                    break;
            default:
                break;
        }

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
        $status = request('status');
        // dd($status);
        $columns = [
            Column::make('checkbox')
                ->searchable(false)
                ->orderable(false)
                ->title('<input type="checkbox" class ="select-all-table" name="select_all" id="select-all-table">')
                ->width(10),
            Column::make('DT_RowIndex')
                ->searchable(false)
                ->title(__('message.srno'))
                ->addClass('text-capitalize')
                ->orderable(false),
            ['data' => 'name', 'name' => 'name', 'title' => __('message.name'),  'class' => 'text-capitalize'],
            ['data' => 'email', 'name' => 'email', 'title' => __('message.email')],
            ['data' => 'city_id', 'name' => 'city_id', 'title' => __('message.city')],
            ['data' => 'country_id', 'name' => 'country_id', 'title' => __('message.country')],
            ['data' => 'contact_number', 'name' => 'contact_number', 'title' => __('message.contact_number')],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => __('message.created_at')],
            ['data' => 'last_actived_at', 'name' => 'last_actived_at', 'title' => __('message.last_actived_at')],
            ['data' => 'rating', 'name' => 'rating', 'title' => __('message.rating')],
            ['data' => 'app_version', 'name' => 'app_version', 'title' => __('message.app_version')],

        ];
        if ($status === 'active' || $status === 'inactive') {
            $columns[] = Column::make('status', 'status')
                ->title(__('message.status'))
                ->visible(true)
                ->orderable(false);
        } elseif ($status === 'pending') {
            $columns[] = Column::make('is_autoverified_email', 'is_autoverified_email')
                ->title(__('message.email') . ' ' . __('message.is_verify'))
                ->visible(true)
                ->orderable(false);

            $columns[] = Column::make('is_autoverified_mobile', 'is_autoverified_mobile')
                ->title(__('message.mobile') . ' ' . __('message.is_verify'))
                ->visible(true)
                ->orderable(false);
        } else {
            $columns[] = Column::make('status', 'status')
                ->title(__('message.status'))
                ->visible(true)
                ->orderable(false);
        }

        $columns[] = Column::computed('action')
            ->exportable(false)
            ->printable(false)
            ->title(__('message.action'))
            ->width(60)
            ->addClass('text-center hide-search');

        return $columns;
    }
}
