<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SMSSetting;
use App\Models\SMSTemplate;

class OrderSMSController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index(Request $request)
    {
        $ordersType =   $orders_type = isset($_GET['sms_type']) ? $_GET['sms_type'] : null;
        $data = SMSTemplate::where('type',$ordersType)->first();
        $pageTitle = __('message.sms_template_setting');

        return view('ordersms.form', compact('pageTitle','data','ordersType'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (env('APP_DEMO') && auth()->user()->hasRole('admin')) {
            $message = __('message.demo_permission_denied');
            if (request()->ajax()) {
                return response()->json(['status' => false, 'message' => $message, 'event' => 'validation']);
            }
            return redirect()->route('ordersms.index')->withErrors($message);
        }
        $data = $request->all();
        
        // Set default order_status based on the subject in the request
        if ($request->type == 'order_confirmation') {
            $data['order_status'] = 'create';
        } elseif ($request->type == 'you_have_parcel') {
            $data['order_status'] = 'create_receiver';
        }elseif ($request->type == 'out_for_delivery') {
            $data['order_status'] = 'courier_arrived';
        } elseif ($request->type == 'delivered_successfully') {
            $data['order_status'] = 'completed';
        } elseif ($request->type == 'delivery_attempt_failed') {
            $data['order_status'] = 'reschedule';
        } elseif ($request->type == 'new_delivery_assignment') {
            $data['order_status'] = 'courier_assigned';
        } elseif ($request->type == 'pickup_verification_code') {
            $data['order_status'] = 'courier_picked_up';
        }elseif ($request->type == 'delivery_verification_code') {
            $data['order_status'] = 'courier_picked_up_delivery_code';
        }

        $smsSetting = SMSSetting::first();
        $data['sms_id'] = isset($smsSetting) ? $smsSetting->id : null;
           
        $smsTemplate = SMSTemplate::updateOrCreate(
            ['type' => $request->type]
            ,$data
        );
        $message = __('message.update_form', ['form' => __('message.order-sms')]);
    
        if ($smsTemplate->wasRecentlyCreated) {
            $message = __('message.save_form', ['form' => __('message.order-sms')]);
        }
        return redirect()->route('ordersms.index', ['sms_type' => $request->type])->withSuccess($message);
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
