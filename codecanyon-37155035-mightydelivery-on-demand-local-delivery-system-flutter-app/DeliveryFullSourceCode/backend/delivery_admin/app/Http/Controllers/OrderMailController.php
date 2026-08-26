<?php

namespace App\Http\Controllers;

use App\Models\OrderMail;
use Illuminate\Http\Request;

class OrderMailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $ordersType =   $orders_type = isset($_GET['mails_type']) ? $_GET['mails_type'] : null;
        $data = OrderMail::where('type',$ordersType)->first();
        $pageTitle = __('message.mail_template_setting');

        return view('ordermail.form', compact('pageTitle','data','ordersType'));
    }

    public function otpVerify_template(Request $request)
    {
        $ordersType =   $orders_type = isset($_GET['mails_type']) ? $_GET['mails_type'] : null;
        $data = OrderMail::where('type',$ordersType)->first();
        $pageTitle = __('message.mail_template_setting');

        return view('ordermail.form', compact('pageTitle','data','ordersType'));
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
        $data = $request->all();
        $result = OrderMail::updateOrCreate(['type' => $request->type], $data);


        $message = __('message.update_form',[ 'form' => __('message.order-mail') ] );
		if($result->wasRecentlyCreated){
			$message = __('message.save_form',[ 'form' => __('message.order-mail') ] );
		}

        if($request->is('api/*')) {
            return json_message_response($message);
		}
        return redirect()->route('ordermail.index', ['mails_type' =>$request->type])->withSuccess($message);

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
