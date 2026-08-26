<?php

namespace App\Http\Controllers;

use App\Models\FrontendData;
use App\Models\Setting;
use Illuminate\Http\Request;

class DeliverYourWayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pageTitle =  __('message.deliver_your_way');
        $count = FrontendData::where('type', 'deliver_your_way')->count();
        $data = FrontendData::where('type', 'deliver_your_way')->get();

        $deliveryourway = config('constant.deliver_your_way');
        
        foreach ($deliveryourway as $key => $val) {
            if( in_array( $key, ['title','subtitle','description']) ) {
                $deliveryourway[$key] = Setting::where('type','deliver_your_way')->where('key',$key)->pluck('value')->first();
            } else {
                $deliveryourway[$key] = Setting::where('type','deliver_your_way')->where('key',$key)->first();
            }
        }
        return view('deliveryourway.main', compact('pageTitle', 'count', 'data', 'deliveryourway'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = __('message.add_form_title', ['form' => __('message.deliver_your_way')]);

        return view('deliveryourway.model', compact('pageTitle'));
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

        $result = FrontendData::Create($data);

        uploadMediaFile($result, $request->frontend_data_image, 'frontend_data_image');

        $type = $result->type ? __('message.' . $result->type) : __('message.record');

        $message = __('message.update_form', ['form' => $type]);
        if ($result->wasRecentlyCreated) {
            $message = __('message.save_form', ['form' => $type]);
        }

        if ($request->is('api/*')) {
            return json_message_response($message);
        }
        return response()->json(['status' => true,  'message' => $message,'event' => 'refresh']);
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
        $pageTitle = __('message.update_form_title', ['form' => __('message.deliver_your_way')]);
        $data = FrontendData::findOrFail($id);

        return view('deliveryourway.model', compact('data', 'pageTitle', 'id'));
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
        $frontenddata = FrontendData::find($id);
        uploadMediaFile($frontenddata, $request->frontend_data_image, 'frontend_data_image');
        $message = __('message.not_found_entry', ['name' => __('message.frontenddata')]);
        if ($frontenddata == null) {
            return json_custom_response(['status' => false, 'message' => $message], 400);
        }
        $message = __('message.update_form', ['form' => __('message.frontenddata')]);
        $frontenddata->fill($request->all())->update();

        if ($frontenddata != '') {
            $frontenddata->update();
            $status = 'success';
            $message = __('message.update_form', ['form' => __('message.frontenddata')]);
        }
        if (request()->is('api/*')) {
            return json_message_response($message);
        }
        if (auth()->check()) {
            return response()->json(['status' => true, 'event' => 'refresh', 'message' => $message]);
        }
        return response()->json(['status' => true,  'message' => $message]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $frontend_data = FrontendData::find($id);

        $status = 'error';
        $message = __('message.not_found_entry', ['name' =>__('message.deliver_your_way')]);

        if( $frontend_data != '' ) {
            $status = 'success';
            $message = __('message.delete_form',['form' => __('message.'.$frontend_data->type)] );
            $frontend_data->delete();
        } 
        
        if(request()->is('api/*')) {
            return json_custom_response(['message' => $message, 'status' => true]);
        }
        return redirect()->back()->withErrors($message);
    }
    public function helpDeliverYourWay(){

        $pageTitle = __('message.deliver_your_way');
        return view('deliveryourway.help',compact([ 'pageTitle' ]));  
    }
}
