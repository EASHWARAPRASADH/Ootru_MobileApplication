<?php

namespace App\Http\Controllers;

use App\DataTables\PagesDataTable;
use App\Models\Pages;
use App\Models\Setting;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PagesDataTable $dataTable)
    {
        if (!auth()->user()->can('pages-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.list_form_title', ['form' => __('message.pages')]);
        $auth_user = authSession();
        $assets = ['datatable'];

        $button = $auth_user->can('pages-add') ? '<a href="'.route('pages.create').'" class="float-right btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i> '.__('message.add_form_title',['form' => __('message.pages')]).'</a>' : '';

        return $dataTable->render('global.order-filter', compact('pageTitle', 'auth_user','button'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('pages-add')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.add_form_title', ['form' => __('message.pages')]);
        return view('pages.form', compact('pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $pages = Pages::Create($request->all());

        $message = __('message.save_form',['form' => __('message.pages')]);
        if (request()->is('api/*')) {
            return json_message_response($message);
        }
        return redirect()->route('pages.index')->withSuccess($message);
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
        if (!auth()->user()->can('pages-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.update_form_title', ['form' => __('message.pages')]);

        $data = Pages::find($id);

        return view('pages.form', compact('pageTitle','data','id'));
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
        if (!auth()->user()->can('pages-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pages = Pages::find($id);
        $message = __('message.not_found_entry', ['name' => __('message.pages')]);
        if($pages == null) {
            return response()->json(['status' => false, 'message' => $message ]);
        }
        $message = __('message.update_form', ['form' => __('message.pages')]);
        $pages->fill($request->all())->update();
        if (request()->is('api/*')) {
            return json_message_response($message);
        }

        return redirect()->route('pages.index')->withSuccess($message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('pages-delete')) {
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
            return redirect()->route('pages.index')->withErrors($message);
        }
        $insurancedescription = Setting::where('type', 'insurance_description')->where('key', 'insurance_description')->first();
        $insurancedescriptionValue = $insurancedescription ? $insurancedescription->value : null;

        if ($id == $insurancedescriptionValue) {
            if ($insurancedescription) {
                $insurancedescription->value = null;
                $insurancedescription->save();
            }
        }
        $pages = Pages::find($id);
        $status = 'error';
        $message = __('message.not_found_entry', ['name' => __('message.pages')]);

        if($pages != '') {
            $pages->delete();
            $status = 'success';
            $message = __('message.delete_form', ['form' => __('message.pages')]);
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
