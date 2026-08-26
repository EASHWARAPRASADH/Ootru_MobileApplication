<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\DataTables\DeliverymanDataTable;
use App\DataTables\ClientDataTable;
use App\DataTables\RatingDataTable;
use App\DataTables\WalletHistoryDataTable;
use App\Http\Resources\DeliveryManEarningResource;
use App\Http\Resources\WalletHistoryResource;
use App\Http\Resources\UserDetailResource;
use App\Models\User;
use App\Models\Payment;
use App\Models\Order;
use App\Models\City;
use App\Models\Country;
use App\Models\DeliveryManDocument;
use App\Models\DeliverymanVehicleHistory;
use App\Models\Document;
use App\Models\OrderVehicleHistory;
use App\Models\Wallet;
use App\Models\WithdrawRequest;

class DeliverymanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(DeliverymanDataTable $dataTable)
    {
        if (!auth()->user()->can('deliveryman-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.list_form_title', ['form' => __('message.delivery_man')]);
        $auth_user = authSession();
        $assets = ['datatable'];
        $params = null;
        $params = [
            'city_id' => request('city_id') ?? null,
            'country_id' => request('country_id') ?? null,
            'last_actived_at' => request('last_actived_at') ?? null,
        ];
        if (!is_array($params['city_id']) && !is_object($params['city_id'])) {
            $params['city_id'] = null;
        }
        if (!is_array($params['country_id']) && !is_object($params['country_id'])) {
            $params['country_id'] = null;
        }
        $selectedCityId = request('city_id');
        $cities = City::pluck('name', 'id')->prepend(__('message.select_name', ['select' => __('message.city')]), '')->toArray();
        $selectedCountryId = request('country_id');
        $country = Country::pluck('name', 'id')->prepend(__('message.select_name', ['select' => __('message.country')]), '')->toArray();

        if(request('status') == 'active') {
            $pageTitle = __('message.active_list_form_title',['form' => __('message.delivery_man')] );
        } elseif (request('status') == 'inactive') {
            $pageTitle = __('message.inactive_list_form_title',['form' => __('message.delivery_man')] );
        } elseif (request('status') == 'pending') {
            $pageTitle = __('message.pending_list_form_title',['form' => __('message.delivery_man')] );

        }
        $reset_file_button = '<a href="' . route('deliveryman.index') . '" class=" mr-1 mt-0 btn btn-sm btn-info text-dark mt-3 pt-2 pb-2"><i class="ri-repeat-line" style="font-size:12px"></i> ' . __('message.reset_filter') . '</a>';
        $is_allow_deliveryman = SettingData('allow_deliveryman', 'allow_deliveryman');

        $button = ($is_allow_deliveryman == 0 && $auth_user->can('deliveryman-add'))
            ? '<a href="' . route('deliveryman.create') . '" class="float-right btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i> ' . __('message.add_form_title', ['form' => __('message.delivery_man')]) . '</a>'
            : null;

        $multi_checkbox_delete = $auth_user->can('deliveryman-delete') ? '<button id="deleteSelectedBtn" checked-title = "deliveryman-checked" class="float-left btn btn-sm ">' . __('message.delete_selected') . '</button>' : '';
        return $dataTable->with('status', request('status'))->render('global.deliveryman-filter', compact('assets', 'pageTitle', 'button', 'auth_user', 'multi_checkbox_delete','params','reset_file_button','selectedCityId','cities','selectedCountryId','country'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('deliveryman-add')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $is_allow_deliveryman = SettingData('allow_deliveryman', 'allow_deliveryman');
        if ($is_allow_deliveryman == 0) {
            $pageTitle = __('message.add_form_title', ['form' => __('message.delivery_man')]);
            $assets = ['phone'];
            return view('deliveryman.form', compact('pageTitle', 'assets'));
        } else {
           $message = __('message.demo_permission_denied');
            return redirect()->route('deliveryman.index')->withErrors($message);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $is_email_verification = SettingData('email_verification', 'email_verification');
        $is_mobile_verification = SettingData('mobile_verification', 'mobile_verification');
        $is_document_verification = SettingData('document_verification', 'document_verification');

        $request['password'] = bcrypt($request->password);
        $request['username'] = $request->username ?? stristr($request->email, "@", true) . rand(100, 1000);
        $request['display_name'] = $request['name'];
        $request['user_type'] = 'delivery_man';

        $request['referral_code'] = generateRandomCode();

        if ($is_email_verification == 0) {
            $request['email_verified_at'] = now();
        }

        if ($is_mobile_verification == 0) {
            $request['otp_verify_at'] = now();
        }

        if ($is_document_verification == 0) {
            $request['document_verified_at'] = now();
        }

        $result = User::create($request->all());
        $result->assignRole($request->user_type);

        $message = __('message.save_form', ['form' => __('message.delivery_man')]);
        if ($request->is('api/*')) {
            return json_message_response($message);
        }
        return redirect()->route('deliveryman.index')->withSuccess($message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(DeliverymanDataTable $dataTable, WalletHistoryDataTable $wallethistorydatatable,RatingDataTable $ratingdatatable, $id)
    {
        if (!auth()->user()->can('deliveryman-show')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $user = User::where('id', $id)->first();
        $auth_user = authSession();
        $pageTitle = __('message.view_form_title', ['form' => __('message.delivery_man')]);
        $data = User::findOrFail($id);
        $type = request('type') ?? 'detail';

        $requiredDocumentIds = Document::where('is_required', 1)
        ->where('status', 1)
        ->pluck('id')
        ->toArray();

        switch ($type) {
            case 'detail':
                $bank_detail = $user->userBankAccount()->orderBy('id', 'desc')->paginate(10);
                $bank_detail_items = UserDetailResource::collection($bank_detail);
                return $dataTable->with($id)->render('deliveryman.show', compact('pageTitle', 'type', 'data', 'bank_detail', 'bank_detail_items','user','requiredDocumentIds'));
                break;

            case 'wallethistory':
                $wallet_history = $user->userWalletHistory()->orderBy('id', 'desc')->get();
                $wallet_history_items = WalletHistoryResource::collection($wallet_history);

                $earning_list = Payment::with('order')->withTrashed()->where('payment_status', 'paid')
                    ->whereHas('order', function ($query) use ($user) {
                        $query->whereIn('status', ['completed', 'cancelled'])->where('delivery_man_id', $user->id);
                    })->orderBy('id', 'desc')->paginate(10);


                $earning_detail_items = DeliveryManEarningResource::collection($earning_list);

                $earning_detail = User::select('id', 'name')->withTrashed()->where('id', $user->id)
                    ->with([
                        'userWallet:total_amount,total_withdrawn',
                        'getPayment:order_id,delivery_man_commission,admin_commission'
                    ])
                    ->withCount([
                        'deliveryManOrder as total_order',
                        'getPayment as paid_order' => function ($query) {
                            $query->where('payment_status', 'paid');
                        }
                    ])
                    ->withSum('userWallet', 'total_amount')
                    ->withSum('userWallet', 'total_withdrawn')
                    ->withSum('getPayment', 'admin_commission')
                    ->withSum('getPayment as delivery_man_commission', 'delivery_man_commission')
                    ->first();

                return $wallethistorydatatable->with($id)->render('deliveryman.show', compact('pageTitle', 'type', 'data', 'id',  'earning_detail', 'wallet_history', 'wallet_history_items', 'earning_list', 'earning_detail_items'));
                break;
            case 'orderhistory':
                $order = Order::where('delivery_man_id', $id)->get();
                return view('deliveryman.show', compact('pageTitle', 'data', 'type', 'order'));
                break;
            case 'withdrawrequest':
                $wallte = Wallet::where('user_id',$id)->first();
                $withdraw = WithdrawRequest::where('user_id', $id)->get();
                return view('deliveryman.show', compact('pageTitle', 'data', 'type', 'withdraw','wallte'));
                break;
            case 'document':
                $documents = DeliveryManDocument::where('delivery_man_id',$user->id)->get();
                return view('deliveryman.show', compact('pageTitle', 'data', 'type', 'documents'));
                break;
            case 'vehicle_information':
                $deliverymanvehicle = DeliverymanVehicleHistory::where('delivery_man_id', $user->id)->get();
                return view('deliveryman.show', compact('pageTitle', 'data', 'type','deliverymanvehicle'));
                break;

                case 'rating':
                    return $ratingdatatable->with(['delivery_man_id'=>$id])->render('deliveryman.show', compact('pageTitle', 'data', 'type'));
                    break;

                default:
                break;
        }
        return $dataTable->with($id)->render('deliveryman.show', compact('pageTitle', 'data', 'id',  'auth_user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('deliveryman-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.update_form_title', ['form' => __('message.delivery_man')]);
        $data = User::find($id);
        $assets = ['phone'];

        return view('deliveryman.form', compact('data', 'pageTitle', 'id', 'assets'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('deliveryman-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $deliveryman = User::find($id);

        $deliveryman->removeRole($deliveryman->user_type);
        $message = __('message.not_found_entry', ['name' => __('message.delivery_man')]);
        if ($deliveryman == null) {
            return json_custom_response(['status' => false, 'message' => $message]);
        }

        $deliveryman->fill($request->all())->update();

        $deliveryman->assignRole($request['user_type']);

        $message = __('message.update_form', ['form' => __('message.delivery_man')]);
        if ($request->is('api/*')) {
            return json_message_response($message);
        }
        return redirect()->route('deliveryman.index')->withSuccess($message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('deliveryman-delete')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        if(env('APP_DEMO')){
            $message = __('message.demo_permission_denied');
            if(request()->is('api/*')){
                return response()->json(['status' => true, 'message' => $message ]);
            }
            if(request()->ajax()) {
                return response()->json(['status' => false, 'message' => $message, 'event' => 'validation']);
            }
            return redirect()->route('deliveryman.index')->withErrors($message);
        }
        $deliveryman = User::find($id);
        if ($deliveryman == null) {
            $message = __('message.not_found_entry', ['name' => __('message.delivery_man')]);
            return json_message_response($message, 400);
        }

        if ($deliveryman != '') {
            $deliveryman->delete();
            $status = 'success';
            $message = __('message.delete_form',['form' => __('message.delivery_man')]);
        }

        if (request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message]);
        }
        if (request()->is('api/*')) {
            return json_message_response($message);
        }
        return redirect()->route('deliveryman.index')->withSuccess($message);
    }
    public function action(Request $request, $id)
    {
        $id = $request->id;
        $users = User::withTrashed()->where('id', $id)->first();

        $message = __('message.not_found_entry', ['name' => __('message.delivery_man')]);
        if ($request->type === 'restore') {
            $users->restore();
            $message = __('message.msg_restored', ['name' => __('message.delivery_man')]);
        }

        if ($request->type === 'forcedelete') {
            if(env('APP_DEMO')){
                $message = __('message.demo_permission_denied');
                if(request()->is('api/*')){
                    return response()->json(['status' => true, 'message' => $message ]);
                }
                if(request()->ajax()) {
                    return response()->json(['status' => false, 'message' => $message, 'event' => 'validation']);
                }
                return redirect()->route('deliveryman.index')->withErrors($message);
            }
            $users->forceDelete();
            $message = __('message.msg_forcedelete', ['name' => __('message.delivery_man')]);
        }
        if (request()->is('api/*')) {
            return json_custom_response(['message' => $message, 'status' => true]);
        }

        return redirect()->route('deliveryman.index')->withSuccess($message);
    }
    public function updateVerification(User $user, Request $request)
    {
        if ($request->confirm === 'yes') {
            $user = User::findOrFail($request->id);
            switch ($request->type) {
                case 'email':
                    $user->is_autoverified_email = 0;
                    $user->email_verified_at = null;
                    $user->save();
                    return redirect()->back()->with('success',__('message.re_email_verification'));
                    break;
                case 'mobile':
                    $user->is_autoverified_mobile = 0;
                    $user->otp_verify_at = null;
                    $user->save();
                    return redirect()->back()->with('success', __('message.re_mobile_verification'));
                    break;
                case 'document':
                    $user->is_autoverified_document = 0;
                     $user->document_verified_at = null;
                    $user->save();
                    $documents = DeliveryManDocument::where('delivery_man_id', $user->id)->get();
                    foreach ($documents as $document) {
                        $document->delete();
                    }
                    return redirect()->back()->with('success', __('message.re_document_verification'));
                    break;
                default:
                    break;
            }
        } else {
            return redirect()->back()->with('info', __('message.cancel_verification'));
        }
    }
    public function vehicleInformationOrder(Request $request, $id)
    {
        $pageTitle = __('message.vehicle_information');
        $ordervehiclehistorydata = OrderVehicleHistory::where('order_id', $id)->get();
        foreach ($ordervehiclehistorydata as $history) {
            $history->vehicle_info = json_decode($history->vehicle_info, true);
        }
        return view('deliveryman.ordervehicleinfromation', compact('pageTitle','id','ordervehiclehistorydata'));
    }

    public function vehicleInformation(Request $request, $id)
    {
        $pageTitle = __('message.vehicle_information');
        // $data = User::findOrFail($id);
        $ordervehiclehistory = DeliverymanVehicleHistory::where('id', $id)->get();
        foreach ($ordervehiclehistory as $history) {
            $history->vehicle_info = json_decode($history->vehicle_info, true);
        }

        return view('deliveryman.vehicleinfromation', compact('pageTitle', 'id', 'ordervehiclehistory'));
    }
    public function updateVehicleStatus(Request $request)
    {
        if (env('APP_DEMO')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
    
        $vehicle = DeliverymanVehicleHistory::find($request->id);
    
        if ($vehicle) {
            $newStatus = $vehicle->is_active == 1 ? 0 : 1;

            DeliverymanVehicleHistory::where('delivery_man_id', $vehicle->delivery_man_id)
                ->where('id', '!=', $vehicle->id)
                ->update(['is_active' => 0]);
    
            $vehicle->is_active = $newStatus;
            $vehicle->save();
    
            $message = $newStatus == 1 
                ? __('message.vehicle_is_now_active')
                : __('message.vehicle_is_now_inactive');
    
            if (request()->is('api/*')) {
                return response()->json(['status' => true, 'message' => $message]);
            }
    
            return redirect()->back()->with('success', $message);
        }
    
        return redirect()->back()->with('error', 'Something went wrong.');
    }
    
    

}
