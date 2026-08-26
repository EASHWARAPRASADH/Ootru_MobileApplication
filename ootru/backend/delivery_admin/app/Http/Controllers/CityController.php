<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\CityDataTable;
use App\Models\City;
use App\Http\Requests\CityRequest;


class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CityDataTable $dataTable)
    {
        if (!auth()->user()->can('city-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.list_form_title',['form' => __('message.city')] );
        $auth_user = authSession();
        $assets = ['datatable'];

        $multi_checkbox_delete = $auth_user->can('city-delete') ? '<button id="deleteSelectedBtn" checked-title = "city-checked " class="float-left btn btn-sm ">'.__('message.delete_selected').'</button>' : '';

        $button = $auth_user->can('city-add') ? '<a href="'.route('city.create').'" class="float-right btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i> '.__('message.add_form_title',['form' => __('message.city')]).'</a>' : '';

        return $dataTable->render('global.datatable', compact('pageTitle','button','auth_user','multi_checkbox_delete'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('city-add')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.add_form_title',[ 'form' => __('message.city')]);

        return view('city.form', compact('pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CityRequest $request)
    {
        $data = $request->all();

        if($request->is('api/*')) {
            $result = City::updateOrCreate(['id' => $request->id], $data);
            $message = __('message.update_form',[ 'form' => __('message.city') ] );
            if($result->wasRecentlyCreated){
                $message = __('message.save_form',[ 'form' => __('message.city') ] );
            }

            return json_message_response($message);
		} else {
            $city = City::create($data);
		    $message = __('message.save_form',[ 'form' => __('message.city') ] );
		}
        return redirect()->route('city.index')->withSuccess($message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pageTitle = __('message.add_form_title',[ 'form' => __('message.city')]);
        $data = City::findOrFail($id);

        return view('city.show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('city-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.update_form_title',[ 'form' => __('message.city')]);
        $data = City::findOrFail($id);

        return view('city.form', compact('data', 'pageTitle', 'id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CityRequest $request, $id)
    {
        if (!auth()->user()->can('city-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $city = City::find($id);
        $message = __('message.not_found_entry', ['name' => __('message.city')]);
        if($city == null) {
            return response()->json(['status' => false, 'message' => $message ]);
        }
        $message = __('message.update_form', ['form' => __('message.city')]);
        $city->fill($request->all())->update();

        if(request()->is('api/*')){
            return response()->json(['status' => true, 'message' => $message ]);
        }
        if(auth()->check()){
            return redirect()->route('city.index')->withSuccess($message);
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
        if (!auth()->user()->can('city-delete')) {
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
            return redirect()->route('city.index')->withErrors($message);
        }
        $city = City::find($id);
        $status = 'error';
        $message = __('message.not_found_entry', ['name' => __('message.city')]);

        if($city != '') {
            $city->delete();
            $status = 'success';
            $message = __('message.delete_form', ['form' => __('message.city')]);
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
        $city = City::withTrashed()->where('id',$id)->first();
        $message = __('message.not_found_entry',['name' => __('message.city')] );
        if($request->type === 'restore'){
            $city->restore();
            $message = __('message.msg_restored',['name' => __('message.city')] );
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
                return redirect()->route('city.index')->withErrors($message);
            }
            $city->forceDelete();
            $message = __('message.msg_forcedelete',['name' => __('message.city')] );
        }

        if(request()->is('api/*')){
            return response()->json(['status' => true, 'message' => $message ]);
        }

        return redirect()->route('city.index')->withSuccess($message);
    }

}
