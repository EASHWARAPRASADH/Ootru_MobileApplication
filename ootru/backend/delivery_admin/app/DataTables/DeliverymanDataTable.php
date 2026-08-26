<?php

namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;

class DeliverymanDataTable extends DataTable
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
                return view('deliveryman.action', compact('data', 'action_type', 'deleted_at'))->render();
            })

            ->editColumn('created_at', function ($row) {
                return dateAgoFormate($row->created_at, true);
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
            ->addColumn('deliveryman', function ($row) {
                return $row->user_type === 'delivery_man' ? '<a href="' . route('deliveryman.show', $row->id) . '" class="link-success">' . $row->name . '</a>' : '-';
            })
            ->filterColumn('deliveryman', function($query, $keyword) {
                return $query->where('name', 'like', "%{$keyword}%");
            })
            ->addColumn('app_version', function ($row) {
                return $row->app_version ?? '-';
            })
            ->addColumn('rating',function($query){
                return count($query->rating) > 0 ? (float) number_format(max($query->rating->avg('rating'),0), 2) : 0;
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
            ->editColumn('flag',function($query){
                $flag = $query->flag;
                if($flag == "1"){
                    return '<i class="fa-solid fa-flag" style="color: #de1717;"></i>';
                }else{
                    return '<i class="fa-solid fa-flag" style="color: #63E6BE;"></i>';
                }
            })

            ->addColumn('action', function ($row) {
                $id = $row->id;
                $deleted_at = $row->deleted_at;
                $action_type = 'action';
                return view('deliveryman.action', compact('id', 'deleted_at', 'action_type'))->render();
            })
            ->addIndexColumn()
            ->editColumn('is_autoverified_email', function ($data) {
                $user = $data->whereNotNull('email_verified_at')->get();
                if ($user) {
                    return '<div class="custom-switch custom-switch-text">
                                <input type="checkbox" class="custom-control-input change_user_verification"
                                    data-type="user" data-name="is_autoverified_email" id="email_' . $data->id . '"
                                    data-id="' . $data->id . '" ' . ($data->email_verified_at != NULL ? 'checked' : '') . ' value="' . $data->id . '">
                                <label class="custom-control-label" for="email_' . $data->id . '"data-on-label="Yes" data-off-label="No"></label>
                            </div>';
                } else {
                    $action_type = 'email_verified';
                    $deleted_at = null;
                    return view('deliveryman.action', compact('data', 'action_type', 'deleted_at'))->render();
                }
            })
            ->editColumn('is_autoverified_mobile', function ($data) {
                $user = $data->whereNotNull('otp_verify_at')->get();
                if ($user) {
                    return '<div class="custom-switch custom-switch-text">
                                <input type="checkbox" class="custom-control-input change_user_verification"
                                    data-type="user" data-name="is_autoverified_mobile" id="mobile_' . $data->id . '"
                                    data-id="' . $data->id . '" ' . ($data->otp_verify_at != NULL ? 'checked' : '') . ' value="' . $data->id . '">
                                <label class="custom-control-label" for="mobile_' . $data->id . '"data-on-label="Yes" data-off-label="No"></label>
                            </div>';
                } else {
                    $action_type = 'mobile_verified';
                    $deleted_at = null;
                    return view('deliveryman.action', compact('data', 'action_type', 'deleted_at'))->render();
                }
            })
            ->editColumn('is_autoverified_document', function ($data) {
                $user = $data->whereNotNull('document_verified_at')->get();
                if ($user) {
                    return '<div class="custom-switch custom-switch-text">
                                <input type="checkbox" class="custom-control-input change_user_verification"
                                    data-type="user" data-name="is_autoverified_document" id="document_' . $data->id . '"
                                    data-id="' . $data->id . '" ' . ($data->document_verified_at != NULL ? 'checked' : '') . ' value="' . $data->id . '">
                                <label class="custom-control-label" for="document_' . $data->id . '"data-on-label="Yes" data-off-label="No"></label>
                            </div>';
                } else {
                    $action_type = 'document_verified';
                    $deleted_at = null;
                    return view('deliveryman.action', compact('data', 'action_type', 'deleted_at'))->render();
                }
            })
            ->rawColumns(['checkbox', 'action', 'status', 'deliveryman','is_autoverified_email','is_autoverified_mobile','is_autoverified_document','flag']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        $model = User::whereIn('user_type', ['delivery_man'])->where('user_type', '!=', 'admin');
        $city = request()->input('city_id');
        $country = request()->input('country_id');
        $lastActive = request()->input('last_actived_at');
        $status = $this->status;

        switch ($status) {
            case '':
                break;

            case 'active':
                $model = $model->where('status', 1)->whereNull('deleted_at')
                        ->whereNotNull('email_verified_at')
                        ->whereNotNull('otp_verify_at')
                        ->whereNotNull('document_verified_at');
                break;
            case 'inactive':
                $model = $model->where('status', 0);
                break;
            case 'pending':
                $model = $model->where('status', 1)->where(function ($query) {
                    $query->where('is_autoverified_email', 0)
                        ->whereNull('email_verified_at')
                        ->orWhere('is_autoverified_mobile', 0)
                        ->whereNull('otp_verify_at')
                        ->orWhere('is_autoverified_document', 0)
                        ->whereNull('document_verified_at');
                });
                break;
            default:
                break;
        }
        if ($country) {
            $model->where('country_id', $country);
        } elseif ($city) {
            $model->where('city_id', $city);
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
        $status = $this->status;

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
            ['data' => 'deliveryman', 'name' => 'deliveryman', 'title' => __('message.name'), 'orderable' => false,'class' => 'text-capitalize'],
            ['data' => 'email', 'name' => 'email', 'title' => __('message.email')],
            ['data' => 'city_id', 'name' => 'city_id', 'title' => __('message.city')],
            ['data' => 'country_id', 'name' => 'country_id', 'title' => __('message.country')],
            ['data' => 'contact_number', 'name' => 'contact_number', 'title' => __('message.contact_number')],
            ['data' => 'app_version', 'name' => 'app_version', 'title' => __('message.app_version')],
            ['data' => 'rating', 'name' => 'rating', 'title' => __('message.rating'),'orderable' => false],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => __('message.created_at')],
            ['data' => 'last_actived_at', 'name' => 'last_actived_at', 'title' => __('message.last_active')],
            ['data' => 'flag', 'name' => 'flag', 'title' => __('message.flag')],
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

            $columns[] = Column::make('is_autoverified_document', 'is_autoverified_document')
                ->title(__('message.document') . ' ' . __('message.is_verify'))
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
