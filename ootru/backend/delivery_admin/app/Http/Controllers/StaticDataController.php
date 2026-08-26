<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\StaticDataDataTable;
use App\Models\StaticData;
use App\Http\Requests\StaticDataRequest;


class StaticDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(StaticDataDataTable $dataTable)
    {
        if (!auth()->user()->can('staticdata-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.list_form_title',['form' => __('message.parceltype')] );
        $auth_user = authSession();
        $assets = ['datatable'];

        $button = $auth_user->can('staticdata-add') ? '<a href="'.route('staticdata.create').'" class="float-right btn btn-sm btn-primary jqueryvalidationLoadRemoteModel"><i class="fa fa-plus-circle"></i> '.__('message.add_form_title',['form' => __('message.parceltype')]).'</a>' : '';

        return $dataTable->render('global.datatable', compact('pageTitle','button','auth_user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('staticdata-add')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle =  __('message.parceltype');

        return view('staticdata.form', compact('pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StaticDataRequest $request)
    {
        $data = $request->all();

        $data['value'] = strtolower(str_replace(' ', '_',$data['label']));

        $staticData = StaticData::updateOrCreate(['id' => $request->id], $data);
        $message = __('message.update_form',[ 'form' => __('message.parceltype') ] );
        if($staticData->wasRecentlyCreated){
            $message = __('message.save_form',[ 'form' => __('message.parceltype') ] );
        }

        if ($request->is('api/*')) {
            return json_message_response($message);
        }
        return response()->json(['status' => true, 'event' => 'submited', 'message' => $message]);
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
        if (!auth()->user()->can('staticdata-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.update_form_title',[ 'form' => __('message.parceltype')]);
        $data = StaticData::findOrFail($id);

        return view('staticdata.form', compact('data', 'pageTitle', 'id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StaticDataRequest $request, $id)
    {
        if (!auth()->user()->can('staticdata-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $staticdata = StaticData::find($id);

        if ($staticdata === null) {
            $message = __('message.not_found_entry', ['name' => __('message.parceltype')]);

            return redirect()->back()->withErrors($message);
        }

        $data = $request->all();
        $data['value'] = strtolower(str_replace(' ', '_', $data['label']));

        $message = __('message.update_form', ['form' => __('message.parceltype')]);

        $staticdata->update($data);

        if ($request->is('api/*')) {
            return response()->json(['status' => true, 'message' => $message]);
        }

        return response()->json(['status' => true, 'event' => 'submited', 'message' => $message]);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('staticdata-delete')) {
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
            return redirect()->route('staticdata.index')->withErrors($message);
        }
        $staticdata = StaticData::find($id);
        $status = 'error';
        $message = __('message.not_found_entry', ['name' => __('message.parceltype')]);

        if($staticdata != '') {
            $staticdata->delete();
            $status = 'success';
            $message = __('message.delete_form', ['form' => __('message.parceltype')]);
        }

        if(request()->is('api/*')){
            return response()->json(['status' => true, 'message' => $message ]);
        }
        if(request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message ]);
        }

        return redirect()->back()->with($status,$message);
    }
}
