<?php

namespace App\Http\Controllers;

use App\DataTables\ClaimsDataTable;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\DataTables\ClientDataTable;
use App\DataTables\WalletHistoryDataTable;
use App\DataTables\OrderDataTable;
use App\DataTables\RatingDataTable;
use App\DataTables\ReferenceDataTable;
use App\Http\Resources\WalletHistoryResource;
use App\Http\Resources\UserDetailResource;
use App\Models\User;
use App\Models\Order;
use App\Models\Wallet;
use App\Models\City;
use App\Models\Claims;
use App\Models\Country;
use App\Models\UserAddress;
use App\Models\WithdrawRequest;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ClientDataTable $dataTable)
    {
        if (!auth()->user()->can('users-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.list_form_title', ['form' => __('message.user')]);
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
            $pageTitle = __('message.active_list_form_title',['form' => __('message.user')] );
        } elseif (request('status') == 'inactive') {
            $pageTitle = __('message.inactive_list_form_title',['form' => __('message.user')] );
        } elseif (request('status') == 'pending') {
            $pageTitle = __('message.pending_list_form_title',['form' => __('message.user')] );
        }

        $reset_file_button = '<a href="' . route('users.index') . '" class=" mr-1 mt-0 btn btn-sm btn-info text-dark mt-3 pt-2 pb-2"><i class="ri-repeat-line" style="font-size:12px"></i> ' . __('message.reset_filter') . '</a>';
        $button = $auth_user->can('users-add') ? '<a href="' . route('users.create') . '" class="float-right btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i> ' . __('message.add_form_title', ['form' => __('message.user')]) . '</a>' : '';
        $multi_checkbox_delete = $auth_user->can('users-delete') ? '<button id="deleteSelectedBtn" checked-title = "users-checked" class="float-left btn btn-sm ">' . __('message.delete_selected') . '</button>' : '';
        return $dataTable->render('global.user-filter', compact('assets', 'pageTitle', 'button', 'auth_user', 'multi_checkbox_delete','params','reset_file_button','selectedCityId','cities','selectedCountryId','country'));
    }
    public function referenceindex(ReferenceDataTable $dataTable)
    {
        $pageTitle = __('message.list_form_title', ['form' => __('message.reference_program')]);
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
        $reset_file_button = '<a href="' . route('reference-list') . '" class=" mr-1 mt-0 btn btn-sm btn-info text-dark mt-3 pt-2 pb-2"><i class="ri-repeat-line" style="font-size:12px"></i> ' . __('message.reset_filter') . '</a>';
        $multi_checkbox_delete = null;
        return $dataTable->render('global.reference-filter', compact('assets', 'pageTitle','auth_user', 'multi_checkbox_delete','params','reset_file_button','selectedCityId','cities','selectedCountryId','country'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('users-add')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.add_form_title', ['form' => __('message.user')]);
        $assets = ['phone'];
        return view('users.form', compact('pageTitle', 'assets'));
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

        $request['password'] = bcrypt($request->password);
        $request['username'] = $request->username ?? stristr($request->email, "@", true) . rand(100, 1000);
        $request['display_name'] = $request['name'];
        $request['user_type'] = 'client';

        $request['referral_code'] = generateRandomCode();

        if ($is_email_verification == 0) {
            $request['email_verified_at'] = now();
        }

        if ($is_mobile_verification == 0) {
            $request['otp_verify_at'] = now();
        }
        $result = User::create($request->all());
        $result->assignRole($request->user_type);
        $message = __('message.save_form', ['form' => __('message.users')]);
        if ($request->is('api/*')) {
            return json_message_response($message);
        }
        return redirect()->route('users.index')->withSuccess($message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ClientDataTable $dataTable,  WalletHistoryDataTable $wallethistorydatatable,ClaimsDataTable $claimsdataTable, RatingDataTable $ratingdatatable, $id)
    {
        if (!auth()->user()->can('users-show')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $user = User::where('id', $id)->first();
        $pageTitle = __('message.view_form_title', ['form' => __('message.users')]);
        $data = User::findOrFail($id);
        $profileImage = getSingleMedia($data, 'profile_image');
        $type = request('type') ?? 'detail';

        switch ($type) {
            case 'detail':
                $bank_detail = $user->userBankAccount()->orderBy('id', 'desc')->paginate(10);
                $bank_detail_items = UserDetailResource::collection($bank_detail);

                return $dataTable->with($id)->render('users.show', compact('pageTitle', 'type', 'data','bank_detail','bank_detail_items','user'));
                break;

            case 'wallethistory':
                $wallet_history = $user->userWalletHistory()->get();
                $wallet_history_items = WalletHistoryResource::collection($wallet_history);
                $earning_detail = User::select('id', 'name')->withTrashed()->where('id', $user->id)
                    ->with([
                        'userWallet:total_amount,total_withdrawn',
                        'getPayment:order_id,admin_commission'
                    ])
                    ->withCount([
                        'deliveryManOrder as total_order',
                        'getPayment as paid_order' => function ($query) {
                            $query->where('payment_status', 'paid');
                        }
                    ])
                    ->withSum('userWallet', 'total_amount')
                    ->withSum('userWallet', 'total_withdrawn')
                    ->first();
                return $wallethistorydatatable->with('id', $id)->render('users.show', compact('pageTitle', 'type', 'data', 'wallet_history', 'wallet_history_items','earning_detail'));
                break;

                case 'orderhistory':
                    $order = Order::where('client_id', $id)->get();
                    return view('users.show', compact('pageTitle', 'data', 'type', 'order'));
                break;
                case 'withdrawrequest':
                    $wallte = Wallet::where('user_id',$id)->first();
                    $withdraw = WithdrawRequest::where('user_id', $id)->get();
                    return view('users.show', compact('pageTitle', 'data', 'type', 'withdraw','wallte'));
                break;
                case 'useraddress':
                    $userAddresses = UserAddress::where('user_id', $id)->get();
                    return view('users.show', compact('pageTitle', 'data', 'type', 'userAddresses'));
                break;
                case 'claimsinfo':
                    $claims = Claims::where('client_id',  $id)->get();
                    return view('users.show', compact('pageTitle', 'data', 'type', 'claims'));
                break;
                case 'rating':
                    return $ratingdatatable->with(['delivery_man_id' => $id])->render('users.show', compact('pageTitle', 'data', 'type'));
                    break;
            default:
                break;
        }
        return $dataTable->with($id)->render('users.show', compact('pageTitle', 'data', 'id', 'type', 'profileImage'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('users-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.update_form_title', ['form' => __('message.client')]);
        $data = User::findOrFail($id);
        $assets = ['phone'];

        return view('users.form', compact('data', 'pageTitle', 'id', 'assets'));
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
        if (!auth()->user()->can('users-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $user = User::findOrFail($id);

        $user->removeRole($user->user_type);
        $message = __('message.not_found_entry', ['name' => __('message.users')]);
        if ($user == null) {
            return json_custom_response(['status' => false, 'message' => $message]);
        }

        $user->fill($request->all())->update();

        $user->assignRole($request['user_type']);

        $message = __('message.update_form', ['form' => __('message.users')]);
        if ($request->is('api/*')) {
            return json_message_response($message);
        }
        return redirect()->route('users.index')->withSuccess($message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('users-delete')) {
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
            return redirect()->route('users.index')->withErrors($message);
        }
        $user = User::find($id);
        if ($user == null) {
            $message = __('message.not_found_entry', ['name' => __('message.users')]);
            return json_custom_response(['status' => false, 'message' => $message]);
        }
        if ($user != '') {
            $user->delete();
            $status = 'success';
            $message = __('message.delete_form', ['form' => __('message.users')]);
        }

        if (request()->ajax()) {
            return json_message_response($message);
        }
        return redirect()->route('users.index')->withSuccess($message);
    }
    public function action(Request $request)
    {
        $id = $request->id;
        $users = User::withTrashed()->where('id', $id)->first();

        $message = __('message.not_found_entry', ['name' => __('message.users')]);
        if ($request->type === 'restore') {
            $users->restore();
            $message = __('message.msg_restored', ['name' => __('message.users')]);
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
                return redirect()->route('users.index')->withErrors($message);
            }
            $users->forceDelete();
            $message = __('message.force_delete_msg', ['name' => __('message.users')]);
        }
        if (request()->is('api/*')) {
            return json_custom_response(['message' => $message, 'status' => true]);
        }

        return redirect()->route('users.index')->withSuccess($message);
    }

    public function userdelete(Request $request){
        $id = $request->id;
        $users = User::withTrashed()->where('id', $id)->first();
            $users->forceDelete();
            $message = __('message.delete_form', ['form' => __('message.users')]);

            return json_message_response($message);
    }
    public function frontendclientstore(UserRequest $request)
    {
        if (User::where('email', $request->email)->exists()) {
            $notification = [
                'message' => 'Email already exists',
                'alert-type' => 'error'
            ];
            return redirect()->back()->with($notification);
        }

        $request['password'] = bcrypt($request->password);
        $request['username'] = $request->username ?? stristr($request->email, "@", true) . rand(100, 1000);
        $request['display_name'] = $request['name'];
        $request['user_type'] = 'client';

        $result = User::create($request->all());
        $result->assignRole($request->user_type);
        $message = __('message.save_form', ['form' => __('message.users')]);
        if ($request->is('api/*')) {
            return json_message_response($message);
        }
        $notification = array(
            'message' => 'Successfully Register',
            'alert-type' => 'success'
        );

        return redirect()->route('frontend-section')->with($notification);
    }
}
