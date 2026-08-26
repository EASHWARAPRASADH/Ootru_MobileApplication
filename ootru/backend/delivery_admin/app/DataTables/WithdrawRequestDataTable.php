<?php

namespace App\DataTables;

use App\Models\Wallet;
use App\Models\WithdrawRequest;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;

class WithdrawRequestDataTable extends DataTable
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

            ->editColumn('status', function ($query) {
                $status = 'danger';
                $status_name = 'requested';
                switch ($query->status) {
                    case 'requested':
                        $status = 'indigo';
                        $status_name = __('message.pending');
                        break;
                    case 'decline':
                        $status = 'danger';
                        $status_name = __('message.declined');
                        break;
                    case 'approved':
                        $status = 'success';
                        $status_name = __('message.approved');
                        break;
                    case 'completed':
                        $status = 'warning';
                        $status_name = __('message.completed');
                        break;
                }
                return '<span class="text-capitalize badge bg-' . $status . '">' . $status_name . '</span>';
            })

            ->addColumn('action', function($row) {
                $delete_at = null;
                $user = $row->user_id;
                $id = $row->id;
                $action_type = 'action';
                return view('withdrawrequest.action',compact('id','row','delete_at','action_type','user'))->render();
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
            ->addColumn('total_withdrawn', function ($row) {
                if ($row->status == 'requested') {
                    $withdrawn = Wallet::where('user_id', $row->user_id)->first();
                    if ($withdrawn) {
                        return getPriceFormat($withdrawn->total_amount);
                    } else {
                        return getPriceFormat(0);
                    }
                }
                return '-';
            })
            ->addColumn('payment', function($row) {
                if($row->status == 'approved'){
                    $id = $row->id;
                    return '<a class="mr-2 loadRemoteModel" href="' . route('withdraw-history', $id) . '" title="' . __('message.add_details') . '">
                                <i class="fa-solid fa-circle-plus"></i>
                            </a>';
                }
            })

            ->addColumn('bank_details', function ($row) {
                $id = $row->id;
                $user = $row->user_id;
                $action_type = 'bank_details';
                $deleted_at = $row->deleted_at;
                return view('withdrawrequest.action', compact('row','id', 'deleted_at', 'action_type','user'))->render();
            })

            ->editColumn('amount', function ($row) {
                $amount = optional($row)->amount;

                return $amount ? getPriceFormat($amount) : '-';
            })

            ->filterColumn('user_id', function($query, $keyword) {
                return $query->orWhereHas('user', function($q) use($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })

            ->editColumn('created_at', function ($row) {
                return dateAgoFormate($row->created_at, true);
            })
            ->editColumn('datetime', function ($row) {
                return dateAgoFormate($row->datetime, true);
            })
            ->editColumn('updated_at', function ($row) {
                return dateAgoFormate($row->updated_at, true);
            })

            ->addColumn('user_id', function ($row) {
                $user = $row->user;
                if ($user->id) {
                    if ($user->user_type === 'client') {
                        return '<a href="' . route('users.show', $user->id) . '" class="link-success">' . $user->name . '</a>';
                    } elseif ($user->user_type === 'delivery_man') {
                        return '<a href="' . route('deliveryman.show', $user->id) . '" class="link-success">' . $user->name . '</a>';
                    }
                }
                return '-';
            })
            ->addIndexColumn()
            ->rawColumns([ 'bank_details','status','action','total_withdrawn','user_id','payment']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\WithdrawRequest $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(WithdrawRequest $model)
    {
        $auth = auth()->user();
        $model = WithdrawRequest::query()->orderBy('id', 'desc');

        if (in_array($auth->user_type, ['client'])) {
            $model->where('user_id', $auth->id);
        }
            $filter_status = request()->input('status');
            if ($filter_status) {
                $model->where('status', $filter_status);
            }

        $withdraw_type = request()->input('withdraw_type', 'all');
        switch ($withdraw_type) {
            case 'all':
                break;
            case 'pending':
                $model->where('status', 'requested');
                break;
            case 'approved':
                $model->where('status', 'approved');
                break;
            case 'decline':
                $model->where('status', 'decline');
                break;
            case 'completed':
                $model->where('status', 'completed');
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
        $withdraw_type = request()->input('withdraw_type', 'all');
        $columns = [
            Column::make('DT_RowIndex')
                ->searchable(false)
                ->title(__('message.srno'))
                ->orderable(false),
            ['data' => 'user_id', 'name' => 'user_id', 'title' => __('message.name')],
            ['data' => 'amount', 'name' => 'amount', 'title' => __('message.amount')],
        ];

        if ($withdraw_type === 'all') {
            $columns[] = ['data' => 'total_withdrawn', 'name' => 'total_withdrawn', 'title' => __('message.available_balnce'),'orderable' => false];
            $columns[] = Column::make('created_at', 'created_at')
                ->title(__('message.request_at'))
                ->orderable(false);

            $columns[] = Column::make('updated_at', 'updated_at')
                ->title(__('message.action_at'))
                ->orderable(false);

            $columns[] = Column::computed('bank_details')
                ->exportable(false)
                ->printable(false)
                ->title(__('message.bank_details'))
                ->width(100)
                ->addClass('text-center hide-search');

            $columns[] = Column::make('status', 'status')
                ->title(__('message.status'));
            $columns[] = Column::make('payment', 'payment')
                ->title(__('message.payment'))
                ->orderable(false);

            $columns[] = ['data' => 'action', 'name' => 'action', 'title' => __('message.action'),'orderable' => false];

        }

        if ($withdraw_type === 'pending') {
            $columns[] = ['data' => 'total_withdrawn', 'name' => 'total_withdrawn', 'title' => __('message.available_balnce'),'orderable' => false];
            $columns[] = Column::make('created_at', 'created_at')
                ->title(__('message.request_at'))
                ->orderable(false);

            $columns[] = Column::computed('bank_details')
                ->exportable(false)
                ->printable(false)
                ->title(__('message.bank_details'))
                ->width(100)
                ->addClass('text-center hide-search');

            $columns[] = Column::make('status', 'status')
                ->title(__('message.status'))
                ->orderable(false);

            $columns[] = ['data' => 'action', 'name' => 'action', 'title' => __('message.action'),'orderable' => false];


        }

        if ($withdraw_type === 'approved') {
            $columns[] = Column::make('created_at', 'created_at')
                ->title(__('message.request_at'))
                ->orderable(false);
            $columns[] = Column::make('updated_at', 'updated_at')
                ->title(__('message.approval_date'))
                ->orderable(false);
            $columns[] = Column::make('status', 'status')
                ->title(__('message.status'));
            $columns[] = Column::make('payment', 'payment')
                ->title(__('message.payment'))
                ->orderable(false);
        }

        if ($withdraw_type === 'decline') {
            $columns[] = Column::make('created_at', 'created_at')
                ->title(__('message.request_at'))
                ->orderable(false);
            $columns[] = Column::make('updated_at', 'updated_at')
                ->title(__('message.cancelled_date'))
                ->orderable(false);
            $columns[] = Column::make('status', 'status')
                ->title(__('message.status'))
                ->orderable(false);
        }
        if ($withdraw_type === 'completed') {
            $columns[] = Column::make('created_at', 'created_at')
                ->title(__('message.request_at'))
                ->orderable(false);
            $columns[] = Column::make('datetime', 'datetime')
                ->title(__('message.completed_at'))
                ->orderable(false);

            $columns[] = Column::make('status', 'status')
                ->title(__('message.status'))
                ->orderable(false);
        }

        return $columns;
    }
}
