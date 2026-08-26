<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\ExtraChargeDataTable;
use App\Models\ExtraCharge;
use App\Http\Requests\ExtraChargeRequest;



class ExtraChargeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ExtraChargeDataTable $dataTable)
    {
        if (!auth()->user()->can('extracharge-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.list_form_title',['form' => __('message.extracharges')] );
        $auth_user = authSession();
        $assets = ['datatable'];

        $button = $auth_user->can('extracharge-add') ? '<a href="'.route('extracharge.create').'" class="float-right btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i> '.__('message.add_form_title',['form' => __('message.extracharge')]).'</a>' : '';

        return $dataTable->render('global.datatable', compact('pageTitle','button','auth_user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('extracharge-add')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.add_form_title', ['form' => __('message.extracharge')]);

        return view('extracharge.form', compact('pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ExtraChargeRequest $request)
    {
        $data = $request->all();

        if($request->is('api/*')) {
            $result = ExtraCharge::updateOrCreate(['id' => $request->id], $data);
            $message = __('message.update_form',[ 'form' => __('message.extracharge') ] );
            if($result->wasRecentlyCreated){
                $message = __('message.save_form',[ 'form' => __('message.extracharge') ] );
            }

            return json_message_response($message);
		} else {
            $extracharge = ExtraCharge::create($data);

            $message = __('message.save_form',[ 'form' => __('message.extracharge') ] );
        }
        return redirect()->route('extracharge.index')->withSuccess($message);
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
        if (!auth()->user()->can('extracharge-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.update_form_title',[ 'form' => __('message.extracharge')]);
        $data = ExtraCharge::findOrFail($id);

        return view('extracharge.form', compact('data', 'pageTitle', 'id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ExtraChargeRequest $request, $id)
    {
        if (!auth()->user()->can('extracharge-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $extracharge = ExtraCharge::find($id);
        $message = __('message.not_found_entry', ['name' => __('message.extracharge')]);
        if($extracharge == null) {
            return response()->json(['status' => false, 'message' => $message ]);
        }
        $message = __('message.update_form', ['form' => __('message.extracharge')]);
        $extracharge->fill($request->all())->update();

        if(request()->is('api/*')){
            return response()->json(['status' => true, 'message' => $message ]);
        }
        if(auth()->check()){
            return redirect()->route('extracharge.index')->withSuccess($message);
        }
        return redirect()->back()->withSuccess($message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('extracharge-delete')) {
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
            return redirect()->route('extracharge.index')->withErrors($message);
        }
        $extracharge = ExtraCharge::find($id);
        $status = 'error';
        $message = __('message.not_found_entry', ['name' => __('message.extracharge')]);

        if($extracharge != '') {
            $extracharge->delete();
            $status = 'success';
            $message = __('message.delete_form', ['form' => __('message.extracharge')]);
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
        $extra_charge = ExtraCharge::withTrashed()->where('id',$id)->first();
        $message = __('message.not_found_entry',['name' => __('message.extracharge')] );
        if($request->type === 'restore'){
            $extra_charge->restore();
            $message = __('message.msg_restored',['name' => __('message.extracharge')] );
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
                return redirect()->route('extracharge.index')->withErrors($message);
            }
            $extra_charge->forceDelete();
            $message = __('message.msg_forcedelete',['name' => __('message.extracharge')] );
        }

        if(request()->is('api/*')){
            return response()->json(['status' => true, 'message' => $message ]);
        }

        return redirect()->route('extracharge.index')->withSuccess($message);
    }
}
