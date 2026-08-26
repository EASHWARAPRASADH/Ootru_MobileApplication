<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\CountryDataTable;
use App\Models\Country;
use App\Http\Requests\CountryRequest;


class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CountryDataTable $dataTable)
    {
        if (!auth()->user()->can('country-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.list_form_title',['form' => __('message.country')] );
        $auth_user = authSession();
        $assets = ['datatable'];

        $multi_checkbox_delete = $auth_user->can('country-delete') ? '<button id="deleteSelectedBtn" checked-title = "country-checked" class="float-left btn btn-sm ">'.__('message.delete_selected').'</button>' : '';
        $button = $auth_user->can('country-add') ? '<a href="'.route('country.create').'" class="float-right btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i> '.__('message.add_form_title',['form' => __('message.country')]).'</a>' : '';

        return $dataTable->render('global.datatable', compact('pageTitle','button','auth_user','multi_checkbox_delete'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('country-add')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $country = country();
        $pageTitle = __('message.add_form_title', ['form' => __('message.country')]);

        return view('country.form', compact('pageTitle', 'country'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CountryRequest $request)
    {
        $data = $request->all();

        if($request->is('api/*')) {
            $result = Country::updateOrCreate(['id' => $request->id], $data);
            $message = __('message.update_form',[ 'form' => __('message.country') ] );
            if($result->wasRecentlyCreated){
                $message = __('message.save_form',[ 'form' => __('message.country') ] );
            }

            return json_message_response($message);
		} else {
            $countryList = country();
            $selectedCountry = collect($countryList)->firstWhere('countryNameEn',  $data['name']);
    
            $data['code'] =$selectedCountry['countryCode'];
            $country = Country::create($data);
                $message = __('message.save_form',[ 'form' => __('message.country') ] );
    
        }
        return redirect()->route('country.index')->withSuccess($message);
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
        if (!auth()->user()->can('country-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $country = country();
        $pageTitle = __('message.update_form_title',[ 'form' => __('message.country')]);
        $data = Country::findOrFail($id);

        return view('country.form', compact('data', 'pageTitle', 'id','country'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CountryRequest $request, $id)
    {
        if (!auth()->user()->can('country-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $country = Country::find($id);
        $message = __('message.not_found_entry', ['name' => __('message.country')]);

        if ($country == null) {
            if ($request->is('api/*')) {
                return response()->json(['status' => false, 'message' => $message]);
            }
            return redirect()->back()->withError($message);
        }

        $countryList = country();
        $selectedCountry = collect($countryList)->firstWhere('countryNameEn',  $request->input('name'));

        $country['code'] =$selectedCountry['countryCode'];
        $country->fill($request->all())->save();

        $message = __('message.update_form', ['form' => __('message.country')]);

        if ($request->is('api/*')) {
            return response()->json(['status' => true, 'message' => $message]);
        }

        return redirect()->route('country.index')->withSuccess($message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('country-delete')) {
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
            return redirect()->route('country.index')->withErrors($message);
        }
        $country = Country::find($id);
        $status = 'error';
        $message = __('message.not_found_entry', ['name' => __('message.country')]);

        if($country != '') {
            $country->delete();
            $status = 'success';
            $message = __('message.delete_form', ['form' => __('message.country')]);
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
        $country = Country::withTrashed()->where('id',$id)->first();
        $message = __('message.not_found_entry',['name' => __('message.country')] );
        if($request->type === 'restore'){
            $country->restore();
            $message = __('message.msg_restored',['name' => __('message.country')] );
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
                return redirect()->route('country.index')->withErrors($message);
            }
            $country->forceDelete();
            $message = __('message.msg_forcedelete',['name' => __('message.country')] );
        }

        if(request()->is('api/*')){
            return response()->json(['status' => true, 'message' => $message ]);
        }

        return redirect()->route('country.index')->withSuccess($message);
    }
}
