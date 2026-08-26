<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\DocumentRequest;
use App\DataTables\DocumentDataTable;
use App\Models\Document;


class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(DocumentDataTable  $dataTable)
    {
        $pageTitle = __('message.list_form_title', ['form' => __('message.document')]);
        $auth_user = authSession();
        $assets = ['datatable'];
        $button = $auth_user->can('document-add') ? '<a href="' . route('document.create') . '" class="float-right btn btn-sm btn-primary jqueryvalidationLoadRemoteModel"><i class="fa fa-plus-circle"></i> ' . __('message.add_form_title', ['form' => __('message.document')]) . '</a>' : '';
        $multi_checkbox_delete = $auth_user->can('users-delete') ? '<button id="deleteSelectedBtn" checked-title = "document-checked" class="float-left btn btn-sm ">' . __('message.delete_selected') . '</button>' : '';
        return $dataTable->render('global.datatable', compact('assets', 'pageTitle', 'button', 'auth_user', 'multi_checkbox_delete'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = __('message.add_form_title', ['form' => __('message.document')]);
        $assets = ['phone'];
        return view('document.form', compact('pageTitle', 'assets'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DocumentRequest $request)
    {
        $data = $request->all();

        if (request()->is('api/*')) {
            $result = Document::updateOrCreate(['id' => $request->id], $data);
            $message = __('message.update_form',[ 'form' => __('message.document') ] );
            if(!$request->id){
                $message = __('message.save_form',[ 'form' => __('message.document') ] );
            }

            return json_message_response($message);
        } else {
            $document = Document::create($data);
            $message = __('message.save_form', ['form' => __('message.document')]);
        }
        return redirect()->back()->withSuccess($message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pageTitle = __('message.view_form_title', ['form' => __('message.document')]);
        $data = Document::findOrFail($id);

        return view('document.show', compact('pageTitle', 'data', 'id'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pageTitle = __('message.update_form_title', ['form' => __('message.document')]);
        $data = Document::findOrFail($id);
        $is_required = $data->is_required;

        return view('document.form', compact('data', 'pageTitle', 'id', 'is_required'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DocumentRequest $request, $id)
    {
        $document = Document::find($id);
        $message = __('message.not_found_entry', ['name' => __('message.document')]);
        if ($document == null) {
            return json_custom_response(['status' => false, 'message' => $message]);
        }

        $document->update($request->all());

        $message = __('message.update_form', ['form' => __('message.document')]);
        if ($request->is('api/*')) {
            return json_message_response($message);
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
        if(env('APP_DEMO')){
            $message = __('message.demo_permission_denied');
            if(request()->is('api/*')){
                return response()->json(['status' => true, 'message' => $message ]);
            }
            if(request()->ajax()) {
                return response()->json(['status' => false, 'message' => $message, 'event' => 'validation']);
            }
            return redirect()->route('document.index')->withErrors($message);
        }
        $document = Document::find($id);

        $message = __('message.not_found_entry', ['name' => __('message.document')]);
        if ($document == null) {
            return json_message_response($message, 400);
        }
        if ($document != '') {
            $document->delete();
            $message = __('message.delete_form', ['form' => __('message.document')]);
        }
        if (request()->is('api/*')) {
            return json_message_response($message);
        }

        if (request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message]);
        }
        return redirect()->route('document.index')->withSuccess($message);
    }

    public function action(Request $request)
    {
        $id = $request->id;
        $users = Document::withTrashed()->where('id', $id)->first();

        $message = __('message.not_found_entry', ['name' => __('message.document')]);
        if ($request->type === 'restore') {
            $users->restore();
            $message = __('message.msg_restored', ['name' => __('message.document')]);
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
                return redirect()->route('document.index')->withErrors($message);
            }
            $users->forceDelete();
            $message = __('message.msg_forcedelete', ['name' => __('message.document')]);
        }
        if (request()->is('api/*')) {
            return json_custom_response(['message' => $message, 'status' => true]);
        }
        return redirect()->route('document.index')->withSuccess($message);
    }
}
