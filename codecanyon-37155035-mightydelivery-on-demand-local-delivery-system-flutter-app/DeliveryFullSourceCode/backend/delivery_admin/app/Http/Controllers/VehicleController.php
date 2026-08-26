<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DataTables\VehicleDataTable;
use App\Models\Vehicle;
use App\Http\Requests\VehicleRequest;
use App\Models\City;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(VehicleDataTable $dataTable)
    {
        if (!auth()->user()->can('vehicle-list')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.list_form_title',['form' => __('message.vehicle')] );
        $auth_user = authSession();
        $assets = ['datatable'];

        $multi_checkbox_delete = $auth_user->can('vehicle-delete') ? '<button id="deleteSelectedBtn" checked-title = "vehicle-checked" class="float-left btn btn-sm ">'.__('message.delete_selected').'</button>' : '';

        $button = $auth_user->can('vehicle-add') ? '<a href="'.route('vehicle.create').'" class="float-right btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i> '.__('message.add_form_title',['form' => __('message.vehicle')]).'</a>' : '';

        return $dataTable->render('global.datatable', compact('pageTitle','button','auth_user','multi_checkbox_delete'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('vehicle-add')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.add_form_title', ['form' => __('message.vehicle')]);

        return view('vehicle.form', compact('pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(VehicleRequest $request)
    {
        $data = $request->all();

        if (request()->is('api/*')) {
            if($data['type'] == 'all') {
                $data['city_ids'] = null;
            }

            $vehicle = Vehicle::updateOrCreate(['id' => $request->id], $data);

            if($request->id) {
                $message = __('message.update_form', ['form' => __('message.vehicle')]);
            } else {
                $message = __('message.save_form', ['form' => __('message.vehicle')]);
            }

        } else {
            $data['city_ids'] = json_encode($request->city_ids, true);
            $vehicle = Vehicle::create($data);
            uploadMediaFile($vehicle, $request->vehicle_image, 'vehicle_image');

            $message = __('message.save_form', ['form' => __('message.vehicle')]);
        }
        if($request->is('api/*')) {
            return json_message_response($message);
		}

        return redirect()->route('vehicle.index')->withSuccess($message);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('vehicle-show')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.add_form_title',[ 'form' => __('message.vehicle')]);
        $data = Vehicle::findOrFail($id);

        return view('vehicle.show', compact('data'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('vehicle-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $pageTitle = __('message.update_form_title',[ 'form' => __('message.vehicle')]);
        $data = Vehicle::findOrFail($id);
        $selected_cities = [];
        if(isset($data->city_ids)){
            $selected_cities = City::whereIn('id', $data->city_ids)->get()->pluck('name', 'id')->toArray();
        }
        return view('vehicle.form', compact('data', 'pageTitle', 'id','selected_cities'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(VehicleRequest $request, $id)
    {
        if (!auth()->user()->can('vehicle-edit')) {
            $message = __('message.demo_permission_denied');
            return redirect()->back()->withErrors($message);
        }
        $data = $request->all();
        $vehicle = Vehicle::findOrFail($id);

        if (request()->is('api/*')) {
            if ($data['type'] == 'all') {
                $data['city_ids'] = null;
            }

            $vehicle->update($data);
            uploadMediaFile($vehicle, $request->vehicle_image, 'vehicle_image');

            $message = __('message.update_form', ['form' => __('message.vehicle')]);

            return json_message_response(['message' => $message, 'status' => true]);
        } else {
            $data['city_ids'] = json_encode($request->city_ids, true);

            $vehicle->update($data);
            uploadMediaFile($vehicle, $request->vehicle_image, 'vehicle_image');

            $message = __('message.update_form', ['form' => __('message.vehicle')]);

            return redirect()->route('vehicle.index')->withSuccess($message);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('vehicle-delete')) {
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
            return redirect()->route('vehicle.index')->withErrors($message);
        }
        $vehicle = Vehicle::find($id);
        $status = 'error';
        $message = __('message.not_found_entry', ['name' => __('message.vehicle')]);

        if($vehicle != '') {
            $vehicle->delete();
            $status = 'success';
            $message = __('message.delete_form', ['form' => __('message.vehicle')]);
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
        $vehicle = Vehicle::withTrashed()->where('id',$id)->first();
        $message = __('message.not_found_entry',['name' => __('message.vehicle')] );
        if($request->type === 'restore'){
            $vehicle->restore();
            $message = __('message.msg_restored',['name' => __('message.vehicle')] );
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
                return redirect()->route('vehicle.index')->withErrors($message);
            }
            $vehicle->forceDelete();
            $message = __('message.msg_forcedelete',['name' => __('message.vehicle')] );
        }

        if(request()->is('api/*')){
            return response()->json(['status' => true, 'message' => $message ]);
        }

        return redirect()->route('vehicle.index')->withSuccess($message);
    }

}
