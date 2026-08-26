<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\CourierCompaniesDataTable;
use App\Models\CourierCompanies;
use App\Http\Requests\CourierCompaniesRequest;

class CourierCompaniesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CourierCompaniesDataTable $dataTable)
    {
        if (!auth()->user()->can('couriercompanies-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.list_form_title',['form' => __('message.courier_companies')] );
        $auth_user = authSession();
        $assets = ['datatable'];

        $button = $auth_user->can('couriercompanies-add') ? '<a href="'.route('couriercompanies.create').'" class="float-right btn btn-sm btn-primary jqueryvalidationLoadRemoteModel"><i class="fa fa-plus-circle"></i> '.__('message.add_form_title',['form' => __('message.courier_companies')]).'</a>' : '';

        return $dataTable->render('global.datatable', compact('pageTitle','button','auth_user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('couriercompanies-add')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle =  __('message.courier_companies');

        return view('couriercompanies.form', compact('pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CourierCompaniesRequest $request)
    {
        $data = $request->all();

        $couriercompanies = CourierCompanies::updateOrCreate(['id' => $request->id], $data);
        $message = __('message.update_form',[ 'form' => __('message.courier_companies') ] );
        if($couriercompanies->wasRecentlyCreated){
            $message = __('message.save_form',[ 'form' => __('message.courier_companies') ] );
        }
        if($request->couriercompanies_image != null)
        {
            uploadMediaFile($couriercompanies, $request->couriercompanies_image, 'couriercompanies_image');
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
        if (!auth()->user()->can('couriercompanies-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.update_form_title',[ 'form' => __('message.courier_companies')]);
        $data = CourierCompanies::findOrFail($id);

        return view('couriercompanies.form', compact('data', 'pageTitle', 'id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CourierCompaniesRequest $request, $id)
    {
        if (!auth()->user()->can('couriercompanies-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $couriercompanies = CourierCompanies::find($id);

        if ($couriercompanies === null) {
            $message = __('message.not_found_entry', ['name' => __('message.courier_companies')]);

            return redirect()->back()->withErrors($message);
        }

        $data = $request->all();
        uploadMediaFile($couriercompanies, $request->couriercompanies_image, 'couriercompanies_image');

        $message = __('message.update_form', ['form' => __('message.vehicle')]);
        $message = __('message.update_form', ['form' => __('message.courier_companies')]);

        $couriercompanies->update($data);

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
        if (!auth()->user()->can('couriercompanies-delete')) {
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
        $couriercompanies = CourierCompanies::find($id);
        $status = 'error';
        $message = __('message.not_found_entry', ['name' => __('message.courier_companies')]);

        if($couriercompanies != '') {
            $couriercompanies->delete();
            $status = 'success';
            $message = __('message.delete_form', ['form' => __('message.courier_companies')]);
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
