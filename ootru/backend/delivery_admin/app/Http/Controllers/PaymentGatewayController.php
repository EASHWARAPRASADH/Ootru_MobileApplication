<?php

namespace App\Http\Controllers;

use App\DataTables\PaymentDataTable;
use Illuminate\Http\Request;
use App\Models\PaymentGateway;

class PaymentGatewayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function paymentIndex(PaymentDataTable $dataTable){
        $payment_type = isset($_GET['payment_type']) ? $_GET['payment_type'] : null;

        switch ($payment_type) {
            case 'cash':
                $pageTitle = __('message.list_form_title',['form' => __('message.cash_payment')] );
                break;
            case 'online':
                $pageTitle = __('message.list_form_title',['form' => __('message.online_payment')] );
                break;
            case 'wallet':
                $pageTitle = __('message.list_form_title',['form' => __('message.wallet_payment')] );
                break;

            default:
                break;
        }
        $auth_user = authSession();
        $assets = ['datatable'];

        return $dataTable->render('global.datatable', compact('pageTitle','auth_user'));
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
        $data['test_value'] = json_decode($data['test_value'], true);
        $result = PaymentGateway::updateOrCreate([ 'type' => request('type') ],$data);
        uploadMediaFile($result,$request->gateway_logo,'gateway_logo');
        $message = __('message.update_form',[ 'form' => __('message.payment_gateway') ] );
		if($result->wasRecentlyCreated) {
			$message = __('message.save_form',[ 'form' => __('message.payment_gateway') ] );
		}
            return json_message_response($message);
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
