<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{DeliveryManDocument, Document, User};
use App\DataTables\DeliverymanDocumentDataTable;
use App\Http\Requests\DeliveryManDocumentRequest;

class DeliveryManDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(DeliverymanDocumentDataTable $dataTable)
    {
        $pageTitle = __('message.list_form_title', ['form' => __('message.deliverymandocument')]);
        $auth_user = authSession();
        $assets = ['datatable'];
        $button = $auth_user->can('deliverymandocument-add') ? '<a href="' . route('deliverymandocument.create') . '" class="float-right btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i> ' . __('message.add_form_title', ['form' => __('message.deliverymandocument')]) . '</a>' : '';
        $multi_checkbox_delete = $auth_user->can('deliveryman-delete') ? '<button id="deleteSelectedBtn" checked-title = "deliverymandocument-checked" class="float-left btn btn-sm ">' . __('message.delete_selected') . '</button>' : '';
        return $dataTable->render('global.datatable', compact('assets', 'pageTitle', 'button', 'auth_user', 'multi_checkbox_delete'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pageTitle = __('message.add_form_title', ['form' => __('message.deliverymandocument')]);
        $assets = ['phone'];
        return view('deliverymandocument.form', compact('pageTitle', 'assets'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(DeliveryManDocumentRequest $request)
    {
        $data = $request->all();

        $is_verified = isset($data['is_verified']) ? $data['is_verified'] : null;

        $result = DeliveryManDocument::updateOrCreate(['id' => $request->id], $data);

        if ($is_verified == '1') {
            $result->is_verified = 1;
            $user = User::where('id',$result->delivery_man_id)->first();
            $documents = Document::where('is_required',1)->where('status', 1)->withCount([
                'deliveryManDocument',
                'deliveryManDocument as is_verified_document' => function ($query) use($user) {
                    $query->where('is_verified', 1)->where('delivery_man_id', $user->id);
                }])
            ->get();
            $is_verified = $documents->where('is_verified_document', 1);

            if(count($documents) == count($is_verified))
            {
                $user->document_verified_at = now();
            }
            $user->save();

        } elseif ($is_verified == '2') {
            $result->is_verified = 2;
            // $user = User::where('id',$result->delivery_man_id)->first();
            // $user->document_verified_at = now();
            // $user->save();
        }elseif($is_verified == '0'){
            $result->is_verified = 0;
            // $user = User::where('id',$result->delivery_man_id)->first();
            // $user->document_verified_at = now();
            // $user->save();
        } else {
            $result->is_verified = 0;
            // $user = User::where('id',$result->delivery_man_id)->first();
            // $user->document_verified_at = now();
            // $user->save();
        }
        $result->save();

        uploadMediaFile($result, $request->delivery_man_document, 'delivery_man_document');

        $message = __('message.update_form', ['form' => __('message.delivery_man_document')]);
        if ($result->wasRecentlyCreated) {
            $message = __('message.save_form', ['form' => __('message.delivery_man_document')]);
        }

        if ($request->is('api/*')) {
            return json_message_response($message);
        }
        return redirect()->route('deliverymandocument.index')->withSuccess($message);
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $pageTitle = __('message.deliverymandocument');
        $assets = ['map'];
        return view('delieverymanlocation.form', compact('pageTitle', 'assets'));
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
        $data = DeliveryManDocument::findOrFail($id);
        return view('deliverymandocument.form', compact('data', 'pageTitle', 'id'));
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
        $delivery_man_document = DeliveryManDocument::find($id);
        $message = __('message.msg_fail_to_delete', ['item' => __('message.delivery_man_document')]);

        if ($delivery_man_document != '') {
            $delivery_man_document->delete();
            $message = __('message.delete_form', ['form' => __('message.delivery_man_document')]);
        }
        if (request()->is('api/*')) {
            return json_custom_response(['message' => $message, 'status' => true]);
        }
    }

    public function action(Request $request)
    {
        $id = $request->id;
        $delivery_man_document = DeliveryManDocument::withTrashed()->where('id', $id)->first();
        $message = __('message.not_found_entry', ['name' => __('message.delivery_man_document')]);
        if ($request->type === 'restore') {
            $delivery_man_document->restore();
            $message = __('message.msg_restored', ['name' => __('message.delivery_man_document')]);
        }

        if ($request->type === 'forcedelete') {
            $delivery_man_document->forceDelete();
            $message = __('message.msg_forcedelete', ['name' => __('message.delivery_man_document')]);
        }
        if (request()->is('api/*')) {
            return json_custom_response(['message' => $message, 'status' => true]);
        }

        return redirect()->route('deliverymandocument.index')->withSuccess($message);
    }
}
