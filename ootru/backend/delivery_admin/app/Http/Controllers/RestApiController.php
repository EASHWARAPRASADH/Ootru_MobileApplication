<?php

namespace App\Http\Controllers;

use App\DataTables\RestApiDataTable;
use App\Models\RestApi;
use App\Models\RestApiHistory;
use Illuminate\Http\Request;

class RestApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(RestApiDataTable $dataTable)
    {
        
        $pageTitle = __('message.list_form_title',['form' => __('message.rest_api')] );
        $auth_user = authSession();
        $assets = ['datatable'];

        $button = '<a href="' . route('rest-api.create') . '" class="float-right btn btn-sm btn-primary jqueryvalidationLoadRemoteModel"><i class="fa fa-plus-circle"></i> ' . __('message.add_form_title', ['form' => __('message.rest_api')]) . '</a>';
        $helpbutton = '<a href="' . route('help-restapi') . '" class="btn btn-sm btn-primary ml-2 mr-2" target="_blank">' . __('message.help') . '</a>';
        return $dataTable->render('global.datatable', compact('pageTitle','button','auth_user','helpbutton'));
    }
    public function generateRestApiCode()
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($characters), 0, 30);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $randome = $this->generateRestApiCode();
        $pageTitle = __('message.add_form_title', ['form' => __('message.rest_api')]);
        return view('restapi.form', compact('pageTitle','randome'));
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
        $restApi = RestApi::Create($data);

        $message = __('message.save_form',['form' => __('message.rest_api')]);
        if (request()->is('api/*')) {
            return json_message_response($message);
        }
        return redirect()->route('rest-api.index')->withSuccess($message);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RestApi  $restApi
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $restApi = RestApi::findOrFail($id);
        $pageTitle =  __('message.rest_api');
        $restApiHistory = RestApiHistory::where('rest_key',$restApi->rest_key)->get();
        return view('restapi.show', compact('id', 'restApiHistory','pageTitle'));
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RestApi  $restApi
     * @return \Illuminate\Http\Response
     */
    public function edit(RestApi $restApi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RestApi  $restApi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RestApi $restApi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RestApi  $restApi
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(env('APP_DEMO')){
            $message = __('message.demo_permission_denied');
            if(request()->is('api/*')){
                return response()->json(['status' => true, 'message' => $message ]);
            }
            if(request()->ajax()) {
                return response()->json(['status' => false, 'message' => $message, 'event' => 'validation']);
            }
            return redirect()->route('rest-api.index')->withErrors($message);
        }
        $rest_api = RestApi::find($id);

        $message = __('message.not_found_entry', ['name' => __('message.rest_api')]);
        if ($rest_api == null) {
            return json_message_response($message, 400);
        }
        if ($rest_api != '') {
            $rest_api->delete();
            $message = __('message.delete_form', ['form' => __('message.rest_api')]);
        }
        if (request()->is('api/*')) {
            return json_message_response($message);
        }

        if (request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message]);
        }
        return redirect()->route('rest-api.index')->withSuccess($message);
    }

    public function action(Request $request)
    {
        $id = $request->id;
        $users = RestApi::withTrashed()->where('id', $id)->first();

        $message = __('message.not_found_entry', ['name' => __('message.rest_api')]);
        if ($request->type === 'restore') {
            if(env('APP_DEMO')){
                $message = __('message.demo_permission_denied');
                if(request()->is('api/*')){
                    return response()->json(['status' => true, 'message' => $message ]);
                }
                if(request()->ajax()) {
                    return response()->json(['status' => false, 'message' => $message, 'event' => 'validation']);
                }
                return redirect()->route('rest-api.index')->withErrors($message);
            }
            $users->restore();
            $message = __('message.msg_restored', ['name' => __('message.rest_api')]);
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
                return redirect()->route('rest-api.index')->withErrors($message);
            }
            $users->forceDelete();
            $message = __('message.msg_forcedelete', ['name' => __('message.rest_api')]);
        }
        if (request()->is('api/*')) {
            return json_custom_response(['message' => $message, 'status' => true]);
        }
        return redirect()->route('rest-api.index')->withSuccess($message);
    }
    public function helprestapi(){

        $pageTitle = __('message.rest_api');
        return view('restapi.help',compact([ 'pageTitle' ]));  
    }
}
