<?php

namespace App\Http\Controllers;

use App\DataTables\CouponDataTable;
use App\Models\City;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CouponDataTable $dataTable)
    {
        if (!auth()->user()->can('coupon-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.list_form_title',['form' => __('message.coupon')] );
        $auth_user = authSession();
        $assets = ['datatable'];

        $button = $auth_user->can('coupon-add') ? '<a href="'.route('coupon.create').'" class="float-right btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i> '.__('message.add_form_title',['form' => __('message.coupon')]).'</a>' : '';

        return $dataTable->render('global.datatable', compact('pageTitle','button','auth_user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('coupon-add')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.add_form_title', ['form' => __('message.coupon')]);

        return view('coupon.form', compact('pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     */
    public function generateCouponCode()
    {
        return substr(str_shuffle('0123456789'), 0, 6);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $data['coupon_code'] = $this->generateCouponCode();
        $data['city_id'] = json_encode($request->city_id ?? null);
        $coupon = Coupon::Create($data);

        $message = __('message.save_form',['form' => __('message.coupon')]);
        if (request()->is('api/*')) {
            return json_message_response($message);
        }
        return redirect()->route('coupon.index')->withSuccess($message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('coupon-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.update_form_title',[ 'form' => __('message.coupon')]);
        $data = Coupon::findOrFail($id);
        $selected_cities = [];
        if(isset($data->city_id)){
            $selected_cities = City::whereIn('id', $data->city_id)->get()->pluck('name', 'id')->toArray();
        }
        return view('coupon.form', compact('data', 'pageTitle', 'id','selected_cities'));
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
        $data = $request->all();
        $coupon = Coupon::findOrFail($id);
       
            $data['city_id'] = json_encode($request->city_id, true);

            $coupon->update($data);

            $message = __('message.update_form', ['form' => __('message.coupon')]);
             if (request()->is('api/*')) {
                return json_message_response($message);
            }

            return redirect()->route('coupon.index')->withSuccess($message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('coupon-delete')) {
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
            return redirect()->route('coupon.index')->withErrors($message);
        }
        $coupon = Coupon::find($id);
        $status = 'error';
        $message = __('message.not_found_entry', ['name' => __('message.coupon')]);

        if($coupon != '') {
            $coupon->delete();
            $status = 'success';
            $message = __('message.delete_form', ['form' => __('message.coupon')]);
        }

        if(request()->is('api/*')){
            return response()->json(['status' => true, 'message' => $message ]);
        }
        if(request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message ]);
        }

        return redirect()->back()->with($status,$message);
    }

    public function action(Request $request)
    {
        $id = $request->id;
        $coupon = Coupon::withTrashed()->where('id',$id)->first();
        $message = __('message.not_found_entry',['name' => __('message.coupon')] );
        if($request->type === 'restore'){
            $coupon->restore();
            $message = __('message.msg_restored',['name' => __('message.coupon')] );
        }

        if($request->type === 'forcedelete'){
            if(env('APP_DEMO')){
                $message = __('message.demo_permission_denied');
                if(request()->is('api/*')){
                    return response()->json(['status' => true, 'message' => $message ]);
                }
                if(request()->ajax()) {
                    return response()->json(['status' => false, 'message' => $message, 'event' => 'validation']);
                }
                return redirect()->route('coupon.index')->withErrors($message);
            }
            $coupon->forceDelete();
            $message = __('message.msg_forcedelete',['name' => __('message.coupon')] );
        }

        if(request()->is('api/*')){
            return response()->json(['status' => true, 'message' => $message ]);
        }

        return redirect()->route('coupon.index')->withSuccess($message);
    }
}
