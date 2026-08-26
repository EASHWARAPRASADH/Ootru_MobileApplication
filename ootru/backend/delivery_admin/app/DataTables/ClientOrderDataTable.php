<?php

namespace App\DataTables;

use App\Models\Order;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;
use Carbon\Carbon;

class ClientOrderDataTable extends DataTable
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
            ->editColumn('status', function ($query) {
                $status = 'danger';
                $status_name = 'cancelled';
                switch ($query->status) {
                    case 'draft':
                        $status = 'light';
                        $status_name = __('message.draft');
                        break;
                    case 'create':
                        $status = 'primary';
                        $status_name = __('message.create');
                        break;
                    case 'completed':
                        $status = 'success';
                        $status_name = __('message.delivered');
                        break;
                    case 'courier_assigned':
                        $status = 'warning';
                        $status_name = __('message.assigned');
                        break;
                    case 'active':
                        $status = 'info';
                        $status_name = __('message.active');
                        break;
                    case 'courier_departed':
                        $status = 'info';
                        $status_name = __('message.departed');
                        break;
                    case 'courier_picked_up':
                        $status = 'warning';
                        $status_name = __('message.picked_up');
                        break;
                    case 'courier_arrived':
                        $status = 'warning';
                        $status_name = __('message.arrived');
                        break;
                    case 'cancelled':
                        $status = 'danger';
                        $status_name = __('message.cancelled');
                        break;
                }
                return '<span class="text-capitalize badge bg-' . $status . '">' . $status_name . '</span>';
            })

            ->editColumn('created_at', function ($query) {
                return dateAgoFormate($query->created_at, true);
            })

            ->editColumn('pickup_point',function($query){
                $address = isset($query->pickup_point['address']) ? $query->pickup_point['address'] : '';
                if($address && $query->deleted_at !== null){
                    return '<span style="color:  #000000" data-toggle="tooltip" title="'.$address.'">'.stringLong($address, 'title',20).'</span>';
                }else{
                    return isset($address) ? '<span data-toggle="tooltip" title="'.$address.'">'.stringLong($address, 'title',20).'</span>' : '-';
                }
            })

            ->editColumn('delivery_point',function($query){
                $address = isset($query->delivery_point['address']) ? $query->delivery_point['address'] : '';
                if($address && $query->deleted_at !== null){
                    return '<span style="color:  #000000" data-toggle="tooltip" title="'.$address.'">'.stringLong($address, 'title',20).'</span>';
                }else{
                    return isset($address) ? '<span data-toggle="tooltip" title="'.$address.'">'.stringLong($address, 'title',20).'</span>' : '-';
                }
            })

            ->addColumn('invoice', function ($query) {
                return $query->status == 'completed' ? '<a href="' . route('order-invoice', $query->id) . '"><i class="fa fa-download"></i></a>' : 'N/A';
            })
            ->editColumn('delivery_man_id', function ($query) {
                $deliveryMan = $query->delivery_man;
                return $deliveryMan ? '<a href="' . route('deliveryman.show', $deliveryMan->id) . '">' . $deliveryMan->name . '</a>' : '-' ;
            })
            ->editColumn('parent_order_id', function ($query) {
                $parentOrderIds = $query->pluck('parent_order_id')->toArray();
                if (in_array($query->id, $parentOrderIds)) {
                    return '<i class="fa-solid fa-right-left text-primary"></i>';
                } else {
                    return '-';
                }
            })

            ->filterColumn('delivery_man_id', function($query, $keyword) {
                return $query->orWhereHas('delivery_man', function($q) use($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->editColumn('milisecond', function($row) {
                $set_prefix = strtoupper(appSettingcurrency('prefix'));
                $set_ids = $set_prefix != null ? $set_prefix .''.$row->milisecond : $row->milisecond;
                return $set_ids;
            })


            ->editColumn('delivery_datetime', function ($row) {
                return dateAgoFormate($row->delivery_datetime, true) ?? '-';
            })
            ->editColumn('pickup_datetime', function ($row) {
                return dateAgoFormate($row->pickup_datetime, true) ?? '-';
            })
            ->editColumn('assign_datetime', function ($row) {
                return dateAgoFormate($row->assign_datetime, true) ?? '-';
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
            ->editColumn('id', function ($row) {
                $user = $row->id;
                return $user ? '<a href="' . route('order.show', $user) . '">' . $user . '</a>' : '-' ;
            })
            ->editColumn('client_id', function ($row) {
                $user = $row->client;
                return $user ? '<a href="' . route('users.show', $user->id) . '">' . $user->name . '</a>' : '-' ;
            })
            ->filterColumn('client_id', function($query, $keyword) {
                return $query->orWhereHas('client', function($q) use($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })

            ->addColumn('action', function ($order) {
                $id = $order->id;
                $delete_at = $order->deleted_at;
                return view('order.action', compact('id', 'delete_at', 'order'))->render();
            })

            ->addIndexColumn()
            ->rawColumns(['action', 'status', 'checkbox', 'pickup_point', 'delivery_point', 'invoice', 'delivery_man_id', 'client_id','id','assign','parent_order_id']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Order $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Order $model)
    {
        $auth = auth()->user();
        $query = $model->newQuery();

        $filter_status = request()->input('status');
        $filter_from_date = request()->input('from_date');
        $filter_to_date = request()->input('to_date');
        $filter_city = request()->input('city_id');
        $filter_country = request()->input('country_id');

        if ($filter_status) {
            $query->where('status', $filter_status);
        }
        if ($filter_city) {
            $query->where('city_id', $filter_city);
        }
        if ($filter_country) {
            $query->where('country_id', $filter_country);
        }

        if ($filter_from_date && $filter_to_date) {
            $query->whereBetween('created_at', [$filter_from_date, $filter_to_date]);
        }

        if ($auth->user_type == 'client') {
            $query->where('client_id', $auth->id);
        }

        $orders_type = isset($_GET['orders_type']) ? $_GET['orders_type'] : null;
        switch ($orders_type) {
            case 'pending':
                $query = $query->where('status','create')->where(function($q){
                    $q->where('is_reschedule', 0)->Where('is_shipped', 0);
                });
                break;
            case 'schedule':
                $tomorrow = Carbon::tomorrow();
                $query = $query->where(function ($query) use ($tomorrow) {
                    $query->whereDate('pickup_datetime', $tomorrow)
                          ->orWhereDate('delivery_datetime', $tomorrow);
                });
                break;
            case 'draft':
                $query = $query->where('status','draft')->where(function($q){
                    $q->where('is_reschedule', 0)->Where('is_shipped',0);
                });
                break;
            case 'today':
                $query = $query->whereDate('created_at', Carbon::today());
                break;
            case 'inprogress':
                $query = $query->whereIn('status', ['courier_departed', 'courier_picked_up', 'courier_assigned','courier_arrived','active'])->where(function($q){
                    $q->where('is_reschedule', 0)->Where('is_shipped',0);
                });
                break;
            case 'cancel':
                $query = $query->where('status', 'cancelled')->where(function($q){
                    $q->where('is_reschedule', 0)->Where('is_shipped',0);
                });
                break;
            case 'complete':
                $query = $query->where('status', 'completed')->where(function($q){
                    $q->where('is_reschedule', 0)->Where('is_shipped',0);
                });
                break;
            case 'reschedule_order':
                $query = $query->where(function ($query) {
                    $query->whereNotNull('is_reschedule')->where('is_reschedule', '!=', 0);
                });
            case 'shipped_order':
                $query = $query->where(function ($query) {
                    $query->whereNotNull('is_shipped')->where('is_shipped', '!=', 0);
                });
                break;

            default:
                break;
        }
        return $query->withTrashed();
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        $prefix = strtoupper(appSettingcurrency('prefix'));
        return [
            Column::make('checkbox')
                ->searchable(false)
                ->title('<input type="checkbox" class ="select-all-table" name="select_all" id="select-all-table">')
                ->orderable(false)
                ->width(20),
            ['data' => 'milisecond', 'name' => 'milisecond',  'title' => $prefix .' #' ?? __('message.document_at')],
            ['data' => 'id', 'name' => 'id', 'title' => __('message.order_id')],
            ['data' => 'client_id', 'name' => 'client_id', 'title' => __('message.customer_name')],
            ['data' => 'pickup_point', 'name' => 'pickup_point', 'title' => __('message.pickup_address')],
            ['data' => 'delivery_point', 'name' => 'delivery_point', 'title' => __('message.delivery_address')],
            ['data' => 'delivery_man_id', 'name' => 'delivery_man_id', 'title' => __('message.delivery_man')],
            ['data' => 'pickup_datetime', 'name' => 'pickup_datetime', 'title' => __('message.pickup_date')],
            ['data' => 'delivery_datetime', 'name' => 'delivery_datetime', 'title' => __('message.delivery_date')],
            ['data' => 'assign_datetime', 'name' => 'assign_datetime', 'title' => __('message.assign_date')],
            ['data' => 'invoice', 'name' => 'invoice', 'title' => __('message.invoice'),'orderable' => false],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => __('message.created_at')],
            ['data' => 'status', 'name' => 'status', 'title' => __('message.status')],
            ['data' => 'parent_order_id', 'name' => 'parent_order_id', 'title' => __('message.is_return')],
            // ['data' => 'assign', 'name' => 'assign', 'title' => __('message.assign'),'orderable' => false],
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center hide-search'),
        ];
    }
}
